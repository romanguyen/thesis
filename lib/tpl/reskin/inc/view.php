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
