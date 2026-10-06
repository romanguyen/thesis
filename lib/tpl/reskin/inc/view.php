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
 * Adapt selected news data to presentation after the native pageheader hook.
 * Native wiki-body rendering remains at the body slot in news-entry.php.
 *
 * @param array<int,array<string,mixed>> $entries Parsed, ACL-authorized news entries
 * @param array<string,mixed>|null $selected Selected entry, or null for archive/native content
 * @return array{content_html:string,entries:array,archive?:bool,title?:string,anchor?:string,labels?:array<string,string>}
 */
function reskin_page_content_model(string $pageId, string $contentHtml, array $entries, ?array $selected, ?array $variant = null): array
{
    $model = ['content_html' => $contentHtml, 'entries' => []];
    if ($entries === []) return $model;

    $variant = $variant ?? reskin_variant_context();
    $archive = $selected === null;
    $title = reskin_i18n_t('updates_news_title');
    $anchors = [];
    $model += [
        'archive' => $archive,
        'title' => $title,
        'anchor' => $archive ? sectionID($title, $anchors) : '',
        'labels' => [
            'eyebrow' => $title,
            'external' => reskin_i18n_t('news_article_more'),
            'permalink' => reskin_i18n_t('news_article_permalink'),
            'footer' => reskin_i18n_t($archive ? 'news_article_read' : 'news_article_back'),
        ],
    ];
    foreach ($archive ? $entries : [$selected] as $entry) {
        $entry += array_fill_keys(['date', 'date_iso', 'author', 'permalink', 'external_url', 'external_title'], '');
        $url = reskin_article_link($pageId, $entry['anchor'], $variant);
        $model['entries'][] = [
            'article' => $entry,
            'url' => $url,
            'back_url' => $archive ? $url : reskin_i18n_link('news:start'),
        ];
    }
    return $model;
}

/**
 * Prepare header presentation at its native include stage, reusing context language/layout targets.
 * This adapter owns the current DokuWiki auth/label/link boundary; header.php retains tpl_searchform().
 * @param array{language_targets:array,layout_links:array{clean:string,left:string,top:string},variant:array{layout:string,style:string}} $context
 * @return array{
 *   brand_url:string,logo_url:string,labels:array{skip_to_content:string,language_switcher:string,close_search:string,open_search:string,toggle_theme:string,layout_switcher:string},
 *   languages:list<array{code:string,label:string,url:string,class:string,title:string,current:bool}>,
 *   layouts:list<array{label:string,url:string,icon:string,title:string,current:bool}>,logout:array{url:string,label:string}|null
 * }
 */
function reskin_header_model(array $context): array
{
    global $ID, $INPUT, $lang;
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
    $layouts = [];
    foreach (['left' => 'bi-layout-sidebar', 'top' => 'bi-menu-button-wide', 'clean' => 'bi-border-all'] as $layout => $icon) {
        $layouts[] = [
            'label' => reskin_i18n_t('layout_' . $layout),
            'url' => $context['layout_links'][$layout],
            'icon' => $icon,
            'title' => reskin_i18n_t('layout_' . $layout . '_hint'),
            'current' => $context['variant']['layout'] === $layout,
        ];
    }
    $labels = [];
    foreach (['skip_to_content', 'language_switcher', 'close_search', 'open_search', 'toggle_theme', 'layout_switcher'] as $key) {
        $labels[$key] = reskin_i18n_t($key);
    }
    $logout = null;
    if ($INPUT->server->str('REMOTE_USER')) {
        $logout = [
            'url' => wl($ID, reskin_url_variant_params($context['variant'], ['do' => 'logout', 'sectok' => getSecurityToken()]), true, '&'),
            'label' => $lang['btn_logout'] ?? 'Logout',
        ];
    }
    return [
        'brand_url' => reskin_i18n_link('start'),
        'logo_url' => tpl_basedir() . 'img/metacentrum_RGB.svg',
        'labels' => $labels,
        'languages' => $languages,
        'layouts' => $layouts,
        'logout' => $logout,
    ];
}
