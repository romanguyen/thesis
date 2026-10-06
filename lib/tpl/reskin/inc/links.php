<?php
if (!defined('DOKU_INC')) die();

/** @param array{layout:string,style:string} $variant */
function reskin_url_variant_params(array $variant, array $params = []): array
{
    $defaults = $variant['layout'] !== 'top' ? ['layout' => $variant['layout']] : [];
    return array_merge($defaults, $params);
}

/** Current-request compatibility adapter. */
function reskin_variant_url_params(array $params = []): array
{
    return reskin_url_variant_params(reskin_variant_context(), $params);
}

function reskin_page_link(string $base, string $preferred, array $variant, array $params = [], bool $abs = false): string
{
    $resolved = reskin_resolve_page($base, $preferred, true, true);
    if ($resolved['found'] && $resolved['fallback']) {
        reskin_i18n_log_missing($resolved['base'], $preferred, $resolved['lang'], 'link');
    }
    return wl($resolved['id'], reskin_url_variant_params($variant, $params), $abs, '&');
}

function reskin_i18n_link(string $base, array $params = [], bool $abs = false, ?string $lang = null): string
{
    return reskin_page_link($base, $lang ?: reskin_i18n_current_lang(), reskin_variant_context(), $params, $abs);
}

function reskin_article_link(string $pageId, string $anchor, array $variant): string
{
    return wl($pageId, reskin_url_variant_params($variant, ['article' => $anchor]), false, '&');
}

/** News opens a reading view; other update feeds retain their original archive anchors. */
function reskin_update_link(string $pageId, string $base, string $anchor, array $variant): string
{
    return $base === 'news:start'
        ? reskin_article_link($pageId, $anchor, $variant)
        : wl($pageId, reskin_url_variant_params($variant), false, '&') . '#' . $anchor;
}

/** @return array{clean:string,left:string,top:string} */
function reskin_layout_links(string $pageId): array
{
    return [
        'clean' => wl($pageId, ['layout' => 'clean'], false, '&'),
        'left' => wl($pageId, ['layout' => 'left'], false, '&'),
        'top' => wl($pageId, [], false, '&'),
    ];
}

/**
 * Build language targets using the already selected article, including non-show requests.
 * The caller authorizes/selects current-page source; counterpart reads are ACL checked here.
 * @param array{lang:string,base:string,localized:bool,id:string} $page
 * @return array<string,array{label:string,url:string,url_abs:string,current:bool,available:bool,resolved_lang:string}>
 */
function reskin_language_targets(array $page, array $variant, ?array $article): array
{
    $targets = [];
    $params = reskin_url_variant_params($variant);
    foreach (RESKIN_I18N_LANGS as $lang => $label) {
        $resolved = reskin_resolve_page($page['base'], $lang, true, true);
        $available = $resolved['found'] && !$resolved['legacy'] && $resolved['lang'] === $lang;
        $targets[$lang] = [
            'label' => $label,
            'url' => wl($resolved['id'], $params, false, '&'),
            'url_abs' => wl($resolved['id'], $params, true, '&'),
            'current' => $page['localized'] ? ($page['lang'] === $lang) : ($lang === RESKIN_I18N_PRIMARY_LANG),
            'available' => $available,
            'resolved_lang' => $resolved['lang'],
        ];
    }
    if ($page['base'] === 'news:start' && $article !== null) {
        foreach ($targets as $code => &$target) {
            if (!$target['available']) continue;
            $targetId = reskin_i18n_build_id('news:start', $code);
            $anchor = reskin_section_anchor_at_index(reskin_read_wiki_sections($targetId), $article['index']);
            if ($anchor === '') continue;
            $articleParams = reskin_url_variant_params($variant, ['article' => $anchor]);
            $target['url'] = wl($targetId, $articleParams, false, '&');
            $target['url_abs'] = wl($targetId, $articleParams, true, '&');
        }
        unset($target);
    }
    return $targets;
}

/** Legacy API adapts current input; new chrome reuses the page-context model instead. */
function reskin_i18n_language_targets(?string $id = null): array
{
    global $INPUT;
    $page = reskin_i18n_parse_id($id);
    $article = null;
    if ($page['base'] === 'news:start' && page_exists($page['id']) && auth_quickaclcheck($page['id']) >= AUTH_READ) {
        $article = reskin_i18n_news_article($page['id'], $INPUT->str('article'), $page['lang']);
    }
    return reskin_language_targets($page, reskin_variant_context(), $article);
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
