<?php
if (!defined('DOKU_INC')) die();

/** @param array{layout:string,style:string} $variant */
function reskin_url_variant_params(array $variant, array $params = []): array
{
    $defaults = [];
    if ($variant['layout'] !== 'top') $defaults['layout'] = $variant['layout'];
    if ($variant['style'] !== 'cesnet') $defaults['style'] = $variant['style'];
    return array_merge($defaults, $params);
}

/** Current-request compatibility adapter. */
function reskin_variant_url_params(array $params = []): array
{
    return reskin_url_variant_params(reskin_variant_context(), $params);
}

function reskin_page_link(string $base, string $preferred, array $variant, array $params = [], bool $abs = false): string
{
    $resolved = reskin_resolve_page($base, $preferred);
    if ($resolved['found'] && $resolved['fallback']) {
        reskin_i18n_log_missing($resolved['base'], $preferred, $resolved['lang'], 'link');
    }
    return wl($resolved['id'], reskin_url_variant_params($variant, $params), $abs, '&');
}

function reskin_i18n_link(string $base, array $params = [], bool $abs = false, ?string $lang = null): string
{
    return reskin_page_link($base, $lang ?: reskin_i18n_current_lang(), reskin_variant_context(), $params, $abs);
}

/** Update feeds built from page sections retain their original archive anchors. */
function reskin_update_link(string $pageId, string $anchor, array $variant): string
{
    return wl($pageId, reskin_url_variant_params($variant), false, '&') . '#' . $anchor;
}

/**
 * Chooser targets for the current page: layout links keep the style, style links keep the layout.
 * @return array{layouts:array{left:string,top:string},styles:array{cesnet:string,metacentrum:string,bootstrap5:string}}
 */
function reskin_variant_links(string $pageId, array $variant): array
{
    $links = ['layouts' => [], 'styles' => []];
    foreach (['left', 'top'] as $layout) {
        $links['layouts'][$layout] = wl($pageId, reskin_url_variant_params(['layout' => $layout, 'style' => $variant['style']]), false, '&');
    }
    foreach (RESKIN_STYLES as $style) {
        $links['styles'][$style] = wl($pageId, reskin_url_variant_params(['layout' => $variant['layout'], 'style' => $style]), false, '&');
    }
    return $links;
}

/**
 * Build language targets for any request, including non-show ones; counterpart reads are ACL checked here.
 * @param array{lang:string,base:string,localized:bool,id:string} $page
 * @return array<string,array{label:string,url:string,url_abs:string,current:bool,available:bool,resolved_lang:string}>
 */
function reskin_language_targets(array $page, array $variant): array
{
    $targets = [];
    $params = reskin_url_variant_params($variant);
    foreach (RESKIN_I18N_LANGS as $lang => $label) {
        $resolved = reskin_resolve_page($page['base'], $lang);
        $available = $resolved['found'] && $resolved['lang'] === $lang;
        $targetId = $available ? $resolved['id'] : $page['id'];
        $languageParams = $available ? $params : array_merge($params, ['fallback_from' => $lang]);
        $targets[$lang] = [
            'label' => $label,
            'url' => wl($targetId, $languageParams, false, '&'),
            'url_abs' => wl($targetId, $languageParams, true, '&'),
            'current' => $page['localized'] ? ($page['lang'] === $lang) : ($lang === RESKIN_I18N_PRIMARY_LANG),
            'available' => $available,
            'resolved_lang' => $available ? $resolved['lang'] : $page['lang'],
        ];
    }
    return $targets;
}

/** Legacy API adapts current input; new chrome reuses the page-context model instead. */
function reskin_i18n_language_targets(?string $id = null): array
{
    return reskin_language_targets(reskin_i18n_parse_id($id), reskin_variant_context());
}

/** Pure adaptation of native breadcrumb HTML; retains the existing URL/fragment policy. */
function reskin_breadcrumb_links(string $html, array $params): string
{
    if ($html === '' || $params === []) return $html;
    return preg_replace_callback('/href="([^"]+)"/', static function (array $match) use ($params): string {
        $href = html_entity_decode($match[1], ENT_QUOTES, 'UTF-8');
        if ($href === '' || $href[0] === '#' || preg_match('/^(mailto:|tel:|javascript:)/i', $href)) return $match[0];
        $parts = parse_url($href);
        if (!is_array($parts)) return $match[0];
        $query = [];
        if (!empty($parts['query'])) parse_str($parts['query'], $query);
        foreach ($params as $key => $value) $query[$key] = $value;
        $rebuilt = '';
        if (isset($parts['scheme'])) $rebuilt .= $parts['scheme'] . '://';
        if (isset($parts['user'])) {
            $rebuilt .= $parts['user'];
            if (isset($parts['pass'])) $rebuilt .= ':' . $parts['pass'];
            $rebuilt .= '@';
        }
        if (isset($parts['host'])) $rebuilt .= $parts['host'];
        if (isset($parts['port'])) $rebuilt .= ':' . $parts['port'];
        if (isset($parts['path'])) $rebuilt .= $parts['path'];
        $queryString = http_build_query($query, '', '&');
        if ($queryString !== '') $rebuilt .= '?' . $queryString;
        if (!empty($parts['fragment'])) $rebuilt .= '#' . $parts['fragment'];
        return 'href="' . hsc($rebuilt) . '"';
    }, $html) ?? $html;
}

/** Scoped native-rendering boundary: configuration is restored even if rendering throws. */
function reskin_render_breadcrumb(array $variant, callable $render): string
{
    global $conf;
    $original = $conf['youarehere'];
    try {
        $conf['youarehere'] = 1;
        $html = $render();
    } finally {
        $conf['youarehere'] = $original;
    }
    return reskin_breadcrumb_links(is_string($html) ? $html : '', reskin_url_variant_params($variant));
}

/** Real, readable namespace destinations only; language namespaces are not pages in the trail. */
function reskin_page_breadcrumbs(string $pageId, string $title, array $variant, bool $article = false): array
{
    global $conf;
    $page = reskin_parse_page_id($pageId);
    $home = reskin_i18n_build_id($conf['start'], $page['lang']);
    if ($pageId === $home && !$article) return [];

    $candidates = [$home];
    $parts = explode(':', $pageId);
    array_pop($parts);
    $namespace = '';
    foreach ($parts as $part) {
        $namespace .= ($namespace === '' ? '' : ':') . $part;
        if (isset(RESKIN_I18N_LANGS[$namespace])) continue;
        $start = $namespace . ':' . $conf['start'];
        $candidates[] = page_exists($start) ? $start : $namespace;
    }
    if ($article) $candidates[] = $pageId;

    $crumbs = [];
    foreach (array_unique($candidates) as $candidate) {
        if ((!$article && $candidate === $pageId) || !page_exists($candidate) || auth_quickaclcheck($candidate) < AUTH_READ) continue;
        $crumbs[] = [
            'label' => reskin_page_title($candidate),
            'url' => wl($candidate, reskin_url_variant_params($variant), false, '&'),
        ];
    }
    $crumbs[] = ['label' => $title, 'url' => ''];
    return $crumbs;
}
