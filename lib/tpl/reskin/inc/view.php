<?php
if (!defined('DOKU_INC')) die();

/**
 * Render a project-owned partial in a scope containing only its explicit inputs.
 *
 * @param string $name Literal partial name, never request input
 * @param array<string,mixed> $model Shape documented in the selected partial
 * @param array<string,mixed> $options Presentation options documented in the partial
 */
function reskin_render_partial(string $name, array $model, array $options = []): void
{
    require __DIR__ . '/../partials/' . $name . '.php';
}

/**
 * Adapt native page content to presentation after the native pageheader hook. The news start page adds its article
 * cards after the native content; every other page, each news article included, is plain native content.
 *
 * @param array<int,array{id:string,title:string,date_iso:string,date:string,url:string,body:string,author:string}> $entries Authorized news articles of the start page, otherwise []
 * @param bool $heroTitle The home hero already holds the page H1 (MetaCentrum PageHeader); the native one follows as H2
 * @return array{content_html:string,entries:array,design:bool,labels:array<string,string>}
 */
function reskin_page_content_model(string $pageId, string $contentHtml, array $entries, ?array $variant = null, bool $heroTitle = false): array
{
    global $ACT;
    $variant = $variant ?? reskin_variant_context();
    $design = $variant['style'] === 'metacentrum' && $ACT === 'show';
    if ($variant['style'] === 'metacentrum' && $ACT === 'search') {
        // EmptyState replaces the frame of the native "nothing found" message, not its text.
        $contentHtml = preg_replace_callback('~<div class="nothing">([^<]*)</div>~u', static function (array $match): string {
            ob_start();
            reskin_render_partial('design-empty-state', ['title' => html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')]);
            return (string) ob_get_clean();
        }, $contentHtml) ?? $contentHtml;
    }
    if ($design && preg_match('/^(?:cs|en):outages:start$/', $pageId)) {
        // One Card per notice: each H2 section stays a run of native siblings, as section editing expects.
        $sections = preg_split('~(?=<h2\b)~u', $contentHtml) ?: [$contentHtml];
        $contentHtml = array_shift($sections);
        foreach ($sections as $section) $contentHtml .= '<section class="mc-card reskin-notice">' . $section . '</section>';
    }
    if ($design && $heroTitle) {
        $contentHtml = preg_replace('~<h1\b([^>]*)>(.*?)</h1>~su', '<h2$1>$2</h2>', $contentHtml, 1) ?? $contentHtml;
    } elseif ($design) {
        // Wrap the native heading in place: retain its ID, inline markup and section-edit boundaries.
        $contentHtml = preg_replace_callback('~<h1\b[^>]*>.*?</h1>~su', static function (array $match) use ($pageId, $variant): string {
            $title = trim(html_entity_decode(strip_tags($match[0]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            return reskin_page_header_html($match[0], reskin_page_breadcrumbs($pageId, $title, $variant));
        }, $contentHtml, 1) ?? $contentHtml;
    }
    return [
        'content_html' => $contentHtml,
        'entries' => $entries,
        'design' => $design,
        'labels' => $entries === [] ? [] : [
            'eyebrow' => reskin_i18n_t('updates_news_title'),
            'read' => reskin_i18n_t('news_article_read'),
        ],
    ];
}

/**
 * Native XHTML of a news card's wiki body, called by the card partials at their body slot. The archive's own TOC and
 * section-edit buttons belong to `news:start`, so the article's TOC never replaces it and its edit markers are dropped.
 */
function reskin_news_body_html(string $source): string
{
    global $TOC;
    if (trim($source) === '') return '';
    $toc = $TOC;
    $info = [];
    $html = (string) p_render('xhtml', p_get_instructions($source), $info);
    $TOC = $toc;
    return html_secedit($html, false);
}

/**
 * Native page-menu items as list items (PageMenu::getListItems() markup). Content comes from Git, so anonymous
 * visitors do not get the always-empty revisions, the backlinks or the page source; the actions stay reachable.
 */
function reskin_page_tools_html(): string
{
    global $INPUT;
    $anonymous = $INPUT->server->str('REMOTE_USER') === '';
    $html = '';
    foreach ((new \dokuwiki\Menu\PageMenu())->getItems() as $item) {
        if ($anonymous && in_array($item->getType(), ['revs', 'backlink', 'source'], true)) continue;
        $html .= '<li class="' . hsc($item->getType()) . '">' . $item->asHtmlLink(false) . '</li>';
    }
    return $html;
}

/** Heading HTML is trusted native output or escaped template-owned news markup. */
function reskin_page_header_html(string $headingHtml, array $breadcrumbs, string $contentHtml = ''): string
{
    ob_start();
    reskin_render_partial('page-header', ['heading_html' => $headingHtml, 'breadcrumbs' => $breadcrumbs, 'content_html' => $contentHtml]);
    return (string) ob_get_clean();
}

/**
 * Prepare header presentation at its native include stage, reusing context language/layout targets.
 * This adapter owns the current DokuWiki auth/label/link boundary; header.php retains tpl_searchform().
 * MetaCentrum brings its own frame, so its chooser offers styles only; the other styles also offer the layout.
 * @param array{language_targets:array,variant_links:array{layouts:array<string,string>,styles:array<string,string>},variant:array{layout:string,style:string}} $context
 * @return array{
 *   brand_url:string,logo_url:string,navigation_header:bool,design_layout:bool,mobile_navigation:bool,labels:array<string,string>,
 *   languages:list<array{code:string,label:string,url:string,class:string,title:string,current:bool}>,
 *   layouts:list<array{label:string,url:string,icon:string,title:string,current:bool}>,
 *   styles:list<array{label:string,url:string,icon:string,title:string,current:bool}>,logout:array{url:string,label:string}|null,
 *   user:array{firstName:string,lastName:string,href:string}|null,elements_url:string
 * }
 */
function reskin_header_model(array $context): array
{
    global $ID, $INPUT, $lang, $INFO;
    $languages = [];
    foreach ($context['language_targets'] as $code => $target) {
        $classes = 'reskin-lang-btn';
        if ($target['current']) $classes .= ' is-current';
        if (!$target['available']) $classes .= ' is-unavailable';
        $languages[] = [
            'code' => $code,
            'label' => strtoupper($code),
            'url' => $target['url'],
            'class' => $classes,
            'title' => $target['available'] ? $target['label'] : reskin_i18n_t('translation_missing'),
            'current' => $target['current'],
        ];
    }
    $choices = ['layouts' => [], 'styles' => []];
    $groups = [
        'layouts' => $context['variant']['style'] === 'metacentrum' ? [] : ['left' => 'bi-layout-sidebar', 'top' => 'bi-menu-button-wide'],
        'styles' => ['cesnet' => 'bi-palette', 'metacentrum' => 'bi-border-all', 'bootstrap5' => 'bi-bootstrap'],
    ];
    foreach ($groups as $group => $icons) {
        $key = $group === 'layouts' ? 'layout' : 'style';
        foreach ($icons as $value => $icon) {
            $choices[$group][] = [
                'label' => reskin_i18n_t($key . '_' . $value),
                'url' => $context['variant_links'][$group][$value],
                'icon' => $icon,
                'title' => reskin_i18n_t($key . '_' . $value . '_hint'),
                'current' => $context['variant'][$key] === $value,
            ];
        }
    }
    $labels = [];
    foreach (['close', 'skip_to_content', 'language_switcher', 'close_search', 'open_search', 'toggle_theme', 'layout_switcher', 'preferences', 'menu', 'appearance', 'dark_theme', 'preference_language', 'preference_layout', 'preference_style'] as $key) {
        $labels[$key] = reskin_i18n_t($key);
    }
    $logout = null;
    if ($INPUT->server->str('REMOTE_USER')) {
        $logout = [
            'url' => wl($ID, reskin_url_variant_params($context['variant'], ['do' => 'logout', 'sectok' => getSecurityToken()]), true, '&'),
            'label' => $lang['btn_logout'] ?? 'Logout',
        ];
    }
    $user = null;
    if ($logout !== null) {
        $name = trim((string) ($INFO['userinfo']['name'] ?? $INPUT->server->str('REMOTE_USER')));
        if ($name === '') $name = $INPUT->server->str('REMOTE_USER');
        $names = preg_split('/\s+/', $name, 2);
        $user = [
            'firstName' => $names[0],
            'lastName' => $names[1] ?? '',
            'href' => wl($ID, reskin_url_variant_params($context['variant'], ['do' => 'profile']), true, '&'),
        ];
    }
    return [
        'brand_url' => reskin_i18n_link('start'),
        'logo_url' => tpl_basedir() . 'img/' . ($context['variant']['style'] === 'metacentrum'
            ? 'reference-metacentrum-logo.svg' : 'metacentrum_RGB.svg'),
        'navigation_header' => true,
        'design_layout' => $context['variant']['style'] === 'metacentrum',
        'mobile_navigation' => $context['show']['sidebar_offcanvas'],
        'labels' => $labels,
        'languages' => $languages,
        'layouts' => $choices['layouts'],
        'styles' => $choices['styles'],
        'logout' => $logout,
        'user' => $user,
        'elements_url' => reskin_local_asset('vendor/metacentrum/mc-elements.js')['url'],
    ];
}
