<?php
if (!defined('DOKU_INC')) die();

/**
 * Parse a page ID into language + base page id
 *
 * @param string|null $id
 * @return array{lang:string,base:string,localized:bool,id:string}
 */
function reskin_i18n_parse_id(?string $id = null): array
{
    global $ID;

    $raw = trim((string) ($id ?? $ID ?? ''), ':');
    if ($raw === '') $raw = 'start';

    $parts = explode(':', $raw);
    $first = $parts[0] ?? '';

    if (isset(RESKIN_I18N_LANGS[$first])) {
        array_shift($parts);
        $base = trim(implode(':', $parts), ':');
        if ($base === '') $base = 'start';

        return [
            'lang' => $first,
            'base' => cleanID($base),
            'localized' => true,
            'id' => cleanID($raw),
        ];
    }

    return [
        'lang' => RESKIN_I18N_PRIMARY_LANG,
        'base' => cleanID($raw),
        'localized' => false,
        'id' => cleanID($raw),
    ];
}

/**
 * @return string
 */
function reskin_i18n_current_lang(): string
{
    return reskin_i18n_parse_id()['lang'];
}

/**
 * Resolve visual variant from request
 *
 * @return array{layout:string,style:string}
 */
function reskin_variant_context(): array
{
    /** @var Input $INPUT */
    global $INPUT;

    static $variant;
    if (is_array($variant)) return $variant;

    $layout = strtolower(trim((string) $INPUT->str('layout')));
    $style = strtolower(trim((string) $INPUT->str('style')));

    if (!in_array($layout, ['left', 'top', 'clean'], true)) $layout = 'top';

    // top layout always uses the CESNET style variant
    if ($layout === 'top') {
        $style = 'cesnet';
    } else {
        $style = 'reskin';
    }

    $variant = [
        'layout' => $layout,
        'style' => $style,
    ];

    return $variant;
}

/**
 * Merge the current non-default layout into URL params
 *
 * @param array $params
 * @return array
 */
function reskin_variant_url_params(array $params = []): array
{
    $variant = reskin_variant_context();
    $variantParams = [];

    if ($variant['layout'] !== 'top') {
        $variantParams['layout'] = $variant['layout'];
    }

    return array_merge($variantParams, $params);
}

/**
 * @param string $base
 * @param string $lang
 * @return string
 */
function reskin_i18n_build_id(string $base, string $lang): string
{
    $base = trim(cleanID($base), ':');
    if ($base === '') $base = 'start';
    return cleanID($lang . ':' . $base);
}

/**
 * Build candidate localized page IDs for direct pages and namespace starts
 *
 * @param string $base
 * @param string $lang
 * @return string[]
 */
function reskin_i18n_id_variants(string $base, string $lang): array
{
    global $conf;

    $base = trim(cleanID($base), ':');
    if ($base === '') $base = 'start';

    $variants = [reskin_i18n_build_id($base, $lang)];

    $start = trim((string) ($conf['start'] ?? 'start'), ':');
    if ($start === '') $start = 'start';

    $isExplicitStart = $base === $start || preg_match('/(^|:)' . preg_quote($start, '/') . '$/', $base);
    if (!$isExplicitStart) {
        $resolver = new \dokuwiki\File\PageResolver('');
        $resolvedNamespace = cleanID($resolver->resolveId($variants[0] . ':'));
        if ($resolvedNamespace !== '') {
            $variants[] = $resolvedNamespace;
        }
    }

    return array_values(array_unique($variants));
}

/**
 * @param string $base
 * @return bool
 */
function reskin_i18n_is_excluded_base(string $base): bool
{
    $base = trim($base, ':');
    if ($base === '') return false;

    $root = strtok($base, ':');
    return in_array($root, RESKIN_I18N_EXCLUDED_ROOTS, true);
}

/**
 * @param string $base
 * @param string|null $preferredLang
 * @param bool $allowLegacy
 * @param bool $useacl
 * @return array{id:string,lang:string,base:string,found:bool,fallback:bool,legacy:bool}
 */
function reskin_i18n_resolve_exact(string $base, ?string $preferredLang = null, bool $allowLegacy = true, bool $useacl = true): array
{
    $preferred = $preferredLang ?: reskin_i18n_current_lang();
    if (!isset(RESKIN_I18N_LANGS[$preferred])) $preferred = RESKIN_I18N_PRIMARY_LANG;

    $base = trim(cleanID($base), ':');
    if ($base === '') $base = 'start';

    $langs = array_values(array_unique([
        $preferred,
        RESKIN_I18N_FALLBACK_LANG,
        RESKIN_I18N_PRIMARY_LANG,
    ]));

    foreach ($langs as $lang) {
        foreach (reskin_i18n_id_variants($base, $lang) as $candidate) {
            if (page_exists($candidate) && (!$useacl || auth_quickaclcheck($candidate) >= AUTH_READ)) {
                return [
                    'id' => $candidate,
                    'lang' => $lang,
                    'base' => $base,
                    'found' => true,
                    'fallback' => $lang !== $preferred,
                    'legacy' => false,
                ];
            }
        }
    }

    if ($allowLegacy) {
        $legacy = cleanID($base);
        if (page_exists($legacy) && (!$useacl || auth_quickaclcheck($legacy) >= AUTH_READ)) {
            return [
                'id' => $legacy,
                'lang' => RESKIN_I18N_FALLBACK_LANG,
                'base' => $base,
                'found' => true,
                'fallback' => true,
                'legacy' => true,
            ];
        }
    }

    return [
        'id' => reskin_i18n_build_id($base, $preferred),
        'lang' => $preferred,
        'base' => $base,
        'found' => false,
        'fallback' => true,
        'legacy' => false,
    ];
}

/**
 * @param string|null $id
 * @return string[]
 */
function reskin_i18n_parent_namespaces(?string $id = null): array
{
    $parsed = reskin_i18n_parse_id($id);
    $parts = explode(':', $parsed['base']);
    array_pop($parts);

    $namespaces = [];
    while (true) {
        $namespaces[] = implode(':', $parts);
        if (count($parts) === 0) break;
        array_pop($parts);
    }

    return $namespaces;
}

/**
 * @param string $fragment
 * @param string $lang
 * @param bool $useacl
 * @return string|null
 */
function reskin_i18n_find_fragment_in_lang(string $fragment, string $lang, bool $useacl = true): ?string
{
    $fragment = trim(cleanID($fragment), ':');
    if ($fragment === '') return null;

    foreach (reskin_i18n_parent_namespaces() as $ns) {
        $base = $ns !== '' ? ($ns . ':' . $fragment) : $fragment;
        foreach (reskin_i18n_id_variants($base, $lang) as $candidate) {
            if (page_exists($candidate) && (!$useacl || auth_quickaclcheck($candidate) >= AUTH_READ)) {
                return $candidate;
            }
        }
    }

    return null;
}

/**
 * @param string $fragment
 * @param string|null $preferredLang
 * @param bool $useacl
 * @return array{id:string,lang:string,base:string,found:bool,fallback:bool,legacy:bool}
 */
function reskin_i18n_resolve_fragment(string $fragment, ?string $preferredLang = null, bool $useacl = true): array
{
    $preferred = $preferredLang ?: reskin_i18n_current_lang();
    if (!isset(RESKIN_I18N_LANGS[$preferred])) $preferred = RESKIN_I18N_PRIMARY_LANG;

    $fragment = trim(cleanID($fragment), ':');
    if ($fragment === '') $fragment = 'start';

    $langs = array_values(array_unique([
        $preferred,
        RESKIN_I18N_FALLBACK_LANG,
        RESKIN_I18N_PRIMARY_LANG,
    ]));

    foreach ($langs as $lang) {
        $candidate = reskin_i18n_find_fragment_in_lang($fragment, $lang, $useacl);
        if ($candidate) {
            return [
                'id' => $candidate,
                'lang' => $lang,
                'base' => $fragment,
                'found' => true,
                'fallback' => $lang !== $preferred,
                'legacy' => false,
            ];
        }
    }

    $legacy = page_findnearest($fragment, $useacl);
    if ($legacy) {
        return [
            'id' => $legacy,
            'lang' => RESKIN_I18N_FALLBACK_LANG,
            'base' => $fragment,
            'found' => true,
            'fallback' => true,
            'legacy' => true,
        ];
    }

    return [
        'id' => '',
        'lang' => $preferred,
        'base' => $fragment,
        'found' => false,
        'fallback' => true,
        'legacy' => false,
    ];
}

/**
 * @param string $base
 * @param string $wantedLang
 * @param string $servedLang
 * @param string $context
 * @return void
 */
function reskin_i18n_log_missing(string $base, string $wantedLang, string $servedLang, string $context = 'page'): void
{
    if ($wantedLang === $servedLang) return;

    static $logged = [];
    $key = $context . '|' . $base . '|' . $wantedLang . '|' . $servedLang;
    if (isset($logged[$key])) return;
    $logged[$key] = true;

    if (class_exists('\\dokuwiki\\Logger')) {
        \dokuwiki\Logger::debug('reskin i18n fallback', [
            'context' => $context,
            'base' => $base,
            'wanted_lang' => $wantedLang,
            'served_lang' => $servedLang,
        ]);
    }
}

/**
 * @param string $base
 * @param array $params
 * @param bool $abs
 * @param string|null $lang
 * @return string
 */
function reskin_i18n_link(string $base, array $params = [], bool $abs = false, ?string $lang = null): string
{
    $preferred = $lang ?: reskin_i18n_current_lang();
    $resolved = reskin_i18n_resolve_exact($base, $preferred, true, true);

    if ($resolved['found'] && $resolved['fallback']) {
        reskin_i18n_log_missing($resolved['base'], $preferred, $resolved['lang'], 'link');
    }

    return wl($resolved['id'], reskin_variant_url_params($params), $abs, '&');
}

/**
 * @param string $page
 * @param bool $print
 * @param bool $propagate
 * @param bool $useacl
 * @return string|bool|null
 */
function reskin_i18n_include_page(string $page, bool $print = true, bool $propagate = true, bool $useacl = true)
{
    $preferred = reskin_i18n_current_lang();
    $resolved = $propagate
        ? reskin_i18n_resolve_fragment($page, $preferred, $useacl)
        : reskin_i18n_resolve_exact($page, $preferred, true, $useacl);

    if (!$resolved['found'] || $resolved['id'] === '') return false;

    if ($resolved['fallback']) {
        reskin_i18n_log_missing($page, $preferred, $resolved['lang'], 'include');
    }

    return tpl_include_page($resolved['id'], $print, false, $useacl);
}

/**
 * Build a short list of section links from a localized wiki page
 *
 * @param string $base
 * @param int $limit
 * @param string|null $preferredLang
 * @return array<int, array{title:string,url:string,date:string,date_iso:string}>
 */
function reskin_i18n_recent_headings(string $base, int $limit = 3, ?string $preferredLang = null): array
{
    $limit = max(1, $limit);
    $preferred = $preferredLang ?: reskin_i18n_current_lang();
    $resolved = reskin_i18n_resolve_exact($base, $preferred, true, true);

    if (!$resolved['found'] || $resolved['id'] === '') return [];

    $raw = trim((string) rawWiki($resolved['id']));
    if ($raw === '') return [];

    if (!preg_match_all('/^(={2,6})\s*(.+?)\s*\1\s*$/mu', $raw, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
        return [];
    }

    $anchors = [];
    $items = [];
    $skipTitle = true;

    foreach ($matches as $index => $match) {
        $title = trim((string) ($match[2][0] ?? ''));
        if ($title === '') continue;

        $anchor = sectionID($title, $anchors);
        if ($skipTitle) {
            $skipTitle = false;
            continue;
        }

        $sectionStart = $match[0][1] + strlen($match[0][0]);
        $sectionEnd = $matches[$index + 1][0][1] ?? strlen($raw);
        $section = substr($raw, $sectionStart, $sectionEnd - $sectionStart);
        $dateIso = reskin_i18n_heading_date($section, $base, $resolved['lang']);

        $items[] = [
            'title' => trim((string) preg_replace('/\s+/u', ' ', $title)),
            'url' => $base === 'news:start'
                ? wl($resolved['id'], reskin_variant_url_params(['article' => $anchor]), false, '&')
                : wl($resolved['id'], reskin_variant_url_params(), false, '&') . '#' . $anchor,
            'date_iso' => $dateIso,
            'date' => reskin_i18n_format_update_date($dateIso, $preferred),
        ];

        if (count($items) >= $limit) break;
    }

    return $items;
}

/**
 * Read localized news entries without copying their content out of the wiki.
 *
 * @return array<int, array{index:int,anchor:string,title:string,date:string,date_iso:string,author:string,body:string,permalink:string,external_url:string,external_title:string}>
 */
function reskin_i18n_news_articles(string $pageId, string $lang): array
{
    $raw = (string) rawWiki($pageId);
    if (!preg_match_all('/^(={2,6})\s*(.+?)\s*\1\s*$/mu', $raw, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
        return [];
    }

    $anchors = [];
    $articles = [];
    foreach ($matches as $index => $match) {
        $title = trim($match[2][0]);
        $anchor = sectionID($title, $anchors);
        if ($index === 0) continue;

        $sectionStart = $match[0][1] + strlen($match[0][0]);
        $sectionEnd = $matches[$index + 1][0][1] ?? strlen($raw);
        $body = trim(substr($raw, $sectionStart, $sectionEnd - $sectionStart));
        $dateIso = reskin_i18n_heading_date($body, 'news:start', $lang);
        $author = '';
        $permalink = '';

        // The imported archive appends an optional permalink, author and timestamp.
        $byline = '/^\s*\[\[(https?:\/\/[^\]|]+)\|permalink\]\]\s*\/\/([^\/\r\n]+)\/\/,\s*[^\r\n]*$/mi';
        if (preg_match($byline, $body, $meta)) {
            if (filter_var($meta[1], FILTER_VALIDATE_URL)) $permalink = $meta[1];
            $author = trim($meta[2]);
            $body = trim((string) preg_replace($byline, '', $body, 1));
        }

        $externalUrl = '';
        $externalTitle = '';
        if (preg_match('/(?:Více informací|More (?:info|information))[^\r\n]*?\[\[(https?:\/\/[^\]|]+)(?:\|([^\]]+))?\]\]/iu', $body, $link)) {
            if (filter_var($link[1], FILTER_VALIDATE_URL)) {
                $externalUrl = $link[1];
                $externalTitle = trim($link[2] ?? '') ?: $externalUrl;
            }
        }

        $articles[] = [
            'index' => $index,
            'anchor' => $anchor,
            'title' => $title,
            'date' => reskin_i18n_format_update_date($dateIso, $lang),
            'date_iso' => $dateIso,
            'author' => $author,
            'body' => $body,
            'permalink' => $permalink,
            'external_url' => $externalUrl,
            'external_title' => $externalTitle,
        ];
    }

    return $articles;
}

/** Return null for missing or unrecognized heading IDs. */
function reskin_i18n_news_article(string $pageId, string $articleId, string $lang): ?array
{
    if ($articleId === '' || strlen($articleId) > 200) return null;

    foreach (reskin_i18n_news_articles($pageId, $lang) as $article) {
        if ($article['anchor'] === $articleId) return $article;
    }

    return null;
}

/** Find the corresponding heading when switching languages on a news article. */
function reskin_i18n_news_anchor_at_index(string $pageId, int $index): string
{
    if ($index < 1 || !preg_match_all('/^(={2,6})\s*(.+?)\s*\1\s*$/mu', (string) rawWiki($pageId), $matches, PREG_SET_ORDER)) {
        return '';
    }

    if (!isset($matches[$index])) return '';

    $anchors = [];
    foreach ($matches as $position => $match) {
        $anchor = sectionID(trim($match[2]), $anchors);
        if ($position === $index) return $anchor;
    }

    return '';
}

/** Extract a published news date or scheduled outage date from its wiki section. */
function reskin_i18n_heading_date(string $section, string $base, string $sourceLang): string
{
    $year = $month = $day = 0;

    if ($base === 'news:start') {
        if (!preg_match('/\b(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun)\s+(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s+(\d{1,2})\s+\d{2}:\d{2}:\d{2}\s+[A-Z]+\s+(\d{4})\b/', $section, $match)) {
            return '';
        }

        $months = ['Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6,
            'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12];
        $month = $months[$match[1]];
        $day = (int) $match[2];
        $year = (int) $match[3];
    } elseif ($base === 'outages:start' && $sourceLang === 'cs') {
        if (!preg_match('/\*\*Term[ií]n:\*\*[^\r\n]*?(\d{1,2})\.\s*(\d{1,2})\.\s*(\d{4})/ui', $section, $match)) {
            return '';
        }
        $day = (int) $match[1];
        $month = (int) $match[2];
        $year = (int) $match[3];
    } elseif ($base === 'outages:start') {
        if (!preg_match('/\*\*Window:\*\*\s*(?:[A-Za-z]{3}\s+)?(\d{4})-(\d{2})-(\d{2})/', $section, $match)) {
            return '';
        }
        $year = (int) $match[1];
        $month = (int) $match[2];
        $day = (int) $match[3];
    }

    return checkdate($month, $day, $year) ? sprintf('%04d-%02d-%02d', $year, $month, $day) : '';
}

/** Format a verified ISO date for the language of the current card. */
function reskin_i18n_format_update_date(string $iso, string $lang): string
{
    if ($iso === '') return '';

    $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $iso);
    if (!$date) return '';

    if ($lang !== 'cs') return $date->format('j F Y');

    $months = [1 => 'ledna', 'února', 'března', 'dubna', 'května', 'června',
        'července', 'srpna', 'září', 'října', 'listopadu', 'prosince'];
    return $date->format('j') . '. ' . $months[(int) $date->format('n')] . ' ' . $date->format('Y');
}

/**
 * @param string|null $id
 * @return array<string, array{label:string,url:string,url_abs:string,current:bool,available:bool,resolved_lang:string}>
 */
function reskin_i18n_language_targets(?string $id = null): array
{
    global $INPUT;

    $parsed = reskin_i18n_parse_id($id);
    $targets = [];
    $variantParams = reskin_variant_url_params();

    foreach (RESKIN_I18N_LANGS as $lang => $label) {
        $resolved = reskin_i18n_resolve_exact($parsed['base'], $lang, true, true);
        $available = $resolved['found'] && !$resolved['legacy'] && $resolved['lang'] === $lang;

        $targets[$lang] = [
            'label' => $label,
            'url' => wl($resolved['id'], $variantParams, false, '&'),
            'url_abs' => wl($resolved['id'], $variantParams, true, '&'),
            'current' => $parsed['localized'] ? ($parsed['lang'] === $lang) : ($lang === RESKIN_I18N_PRIMARY_LANG),
            'available' => $available,
            'resolved_lang' => $resolved['lang'],
        ];
    }

    if ($parsed['base'] === 'news:start' && page_exists($parsed['id']) && auth_quickaclcheck($parsed['id']) >= AUTH_READ) {
        $article = reskin_i18n_news_article($parsed['id'], $INPUT->str('article'), $parsed['lang']);
        if ($article !== null) {
            foreach ($targets as $code => &$target) {
                if (!$target['available']) continue;

                $targetId = reskin_i18n_build_id('news:start', $code);
                $anchor = reskin_i18n_news_anchor_at_index($targetId, $article['index']);
                if ($anchor === '') continue;

                $params = reskin_variant_url_params(['article' => $anchor]);
                $target['url'] = wl($targetId, $params, false, '&');
                $target['url_abs'] = wl($targetId, $params, true, '&');
            }
            unset($target);
        }
    }

    return $targets;
}

/**
 * @return string|null
 */
function reskin_i18n_fallback_notice(): ?string
{
    global $INPUT;

    $from = trim((string) $INPUT->str('fallback_from'));
    if (!isset(RESKIN_I18N_LANGS[$from])) return null;

    $parsed = reskin_i18n_parse_id();
    $to = $parsed['localized'] ? $parsed['lang'] : RESKIN_I18N_FALLBACK_LANG;
    if (!isset(RESKIN_I18N_LANGS[$to]) || $to === $from) return null;

    return reskin_i18n_t('fallback_notice', [RESKIN_I18N_LANGS[$from], RESKIN_I18N_LANGS[$to]]);
}

/**
 * Route non-localized and missing localized pages to localized fallback pages
 *
 * @return void
 */
function reskin_i18n_handle_request(): void
{
    global $ACT, $ID;

    if ($ACT !== 'show') return;

    $parsed = reskin_i18n_parse_id($ID);
    if (reskin_i18n_is_excluded_base($parsed['base'])) return;

    if (!$parsed['localized']) {
        $target = reskin_i18n_resolve_exact($parsed['base'], RESKIN_I18N_PRIMARY_LANG, true, true);
        if ($target['found'] && !$target['legacy'] && $target['id'] !== $ID) {
            send_redirect(wl($target['id'], reskin_variant_url_params(), true, '&'), 302);
        }
        return;
    }

    if (!page_exists($ID)) {
        $target = reskin_i18n_resolve_exact($parsed['base'], $parsed['lang'], true, true);
        if (!$target['found'] || $target['id'] === '' || $target['id'] === $ID) return;

        $params = [];
        if ($target['lang'] !== $parsed['lang']) {
            $params['fallback_from'] = $parsed['lang'];
            reskin_i18n_log_missing($parsed['base'], $parsed['lang'], $target['lang'], 'route');
        }

        send_redirect(wl($target['id'], reskin_variant_url_params($params), true, '&'), 302);
    }
}
