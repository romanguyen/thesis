<?php
if (!defined('DOKU_INC')) die();

/** Top navigation deliberately retains :start, unlike sidebar/title aliases. */
function reskin_navigation_top_id(string $pageId): string
{
    return trim(cleanID($pageId), ':');
}

/** Preserve the sidebar's raw-ID policy; native data-wiki-id values are already resolved. */
function reskin_navigation_sidebar_id(string $pageId): string
{
    return substr($pageId, -6) === ':start' ? substr($pageId, 0, -6) : $pageId;
}

function reskin_navigation_title_id(string $pageId): string
{
    return reskin_navigation_sidebar_id(reskin_navigation_top_id($pageId));
}

/** @return array<int,array{title:string,href:string,wiki_id:string,children:array}> Full first-list hierarchy. */
function reskin_navigation_items(DOMXPath $xpath, DOMElement $list): array
{
    $items = [];
    foreach ($xpath->query('./li', $list) as $li) {
        if (!($li instanceof DOMElement)) continue;
        $link = null;
        foreach (['./div[contains(concat(" ", normalize-space(@class), " "), " li ")]/a[1]', './a[1]', './/a[1]'] as $selector) {
            $candidate = $xpath->query($selector, $li)->item(0);
            if ($candidate instanceof DOMElement) {
                $link = $candidate;
                break;
            }
        }
        if (!($link instanceof DOMElement)) continue;
        $title = trim((string) preg_replace('/\s+/u', ' ', $link->textContent));
        if ($title === '') continue;
        $wikiId = trim($link->getAttribute('data-wiki-id'));
        $href = trim($link->getAttribute('href'));
        if ($href === '' && $wikiId === '') continue;
        $subList = $xpath->query('./ul[1]', $li)->item(0);
        $items[] = [
            'title' => $title,
            'href' => $href,
            'wiki_id' => $wikiId,
            'children' => $subList instanceof DOMElement ? reskin_navigation_items($xpath, $subList) : [],
        ];
    }
    return $items;
}

/**
 * Parse rendered native HTML once. Retain whole-document links for titles BEFORE
 * display cleanup, and the full cleaned DOM so sidebar prose/multiple lists survive.
 * @return array{document:?DOMDocument,links:array<int,array{wiki_id:string,title:string}>,items:array}
 */
function reskin_parse_navigation(string $html): array
{
    $model = ['document' => null, 'links' => [], 'items' => []];
    if (trim($html) === '') return $model;
    $previous = libxml_use_internal_errors(true);
    try {
        $doc = new DOMDocument();
        if (!$doc->loadHTML('<?xml encoding="utf-8"?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD)) return $model;
        $xpath = new DOMXPath($doc);
        foreach ($xpath->query('//a[@data-wiki-id]') as $link) {
            if (!($link instanceof DOMElement)) continue;
            $wikiId = trim($link->getAttribute('data-wiki-id'));
            $title = trim((string) preg_replace('/\s+/u', ' ', $link->textContent));
            if ($wikiId !== '' && $title !== '') $model['links'][] = ['wiki_id' => $wikiId, 'title' => $title];
        }
        foreach (['//h1|//h2|//h3|//h4|//h5|//h6', '//comment()', '//div[not(*) and normalize-space(.)=""]'] as $selector) {
            foreach ($xpath->query($selector) as $node) {
                if ($node->parentNode) $node->parentNode->removeChild($node);
            }
        }
        foreach ($xpath->query('//ul[li]') as $list) {
            if (!($list instanceof DOMElement) || $xpath->query('.//a[@href]', $list)->length === 0) continue;
            $model['items'] = reskin_navigation_items($xpath, $list);
            break;
        }
        $model['document'] = $doc;
        return $model;
    } finally {
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
    }
}

/**
 * Native include boundary with request-local reuse of parsed output, not rendered HTML.
 * Each consumer retains native ACL/fallback/cache/plugin hooks, including NOCACHE.
 * Core owns XHTML caching; changed rendered output gets a different parsed-data key.
 */
function reskin_navigation_source(string $fragment, string $pageId, string $preferred): array
{
    global $ID, $TOC;
    $originalId = $ID;
    $originalToc = $TOC;
    try {
        $html = reskin_include_page($fragment, $preferred, $pageId, false, true, true);
    } finally {
        $ID = $originalId;
        $TOC = $originalToc;
    }
    $html = is_string($html) ? $html : '';
    static $parsed = [];
    $key = sha1($html);
    if (!isset($parsed[$key])) $parsed[$key] = reskin_parse_navigation($html);
    $model = $parsed[$key];
    // Do not expose the cached mutable DOM to a consumer.
    if ($model['document'] !== null) $model['document'] = clone $model['document'];
    return $model;
}

/** Current-request compatibility boundary shared by title, top menu and sidebar. */
function reskin_current_navigation(): array
{
    global $ID, $conf;
    return reskin_navigation_source((string) $conf['sidebar'], (string) $ID, reskin_i18n_current_lang());
}

function reskin_navigation_label(array $source, string $pageId): ?string
{
    $id = reskin_navigation_title_id($pageId);
    foreach ($source['links'] as $link) {
        if (reskin_navigation_title_id($link['wiki_id']) === $id) return $link['title'];
    }
    return null;
}

/** Transform a node to the existing top-menu policy, pruning below its immediate children. */
function reskin_navigation_top_item(array $node, string $currentId, array $variant, bool $withChildren): array
{
    $id = $node['wiki_id'] !== '' ? reskin_navigation_top_id($node['wiki_id']) : '';
    $children = [];
    if ($withChildren) {
        foreach ($node['children'] as $child) $children[] = reskin_navigation_top_item($child, $currentId, $variant, false);
    }
    return [
        'title' => $node['title'],
        'href' => $node['wiki_id'] !== '' ? wl($node['wiki_id'], reskin_url_variant_params($variant), false, '&') : $node['href'],
        'is_current' => $id !== '' && $id === $currentId,
        'is_parent' => $id !== '' && strpos($currentId, $id . ':') === 0,
        'children' => $children,
    ];
}

/** @return array{items:array,labels:array{navigation:string,toggle:string,more:string}} */
function reskin_top_navigation_model(array $source, string $pageId, array $variant, string $lang): array
{
    $items = [];
    $currentId = reskin_navigation_top_id($pageId);
    $hasCurrent = false;
    foreach ($source['items'] as $node) {
        $item = reskin_navigation_top_item($node, $currentId, $variant, true);
        $item['is_current_child'] = false;
        foreach ($item['children'] as $child) {
            if ($child['is_current']) {
                $item['is_current_child'] = true;
                break;
            }
        }
        if ($item['is_current'] || $item['is_current_child']) $hasCurrent = true;
        $items[] = $item;
    }
    foreach ($items as &$item) {
        $item['is_active'] = $item['is_current'] || $item['is_current_child'] || (!$hasCurrent && $item['is_parent']);
    }
    unset($item);
    return ['items' => $items, 'labels' => [
        'navigation' => reskin_i18n_t('navigation', [], $lang),
        'toggle' => reskin_i18n_t('toggle_navigation', [], $lang),
        'more' => reskin_i18n_t('more_navigation', [], $lang),
    ]];
}

function reskin_current_top_navigation(): array
{
    global $ID;
    return reskin_top_navigation_model(reskin_current_navigation(), (string) $ID, reskin_variant_context(), reskin_i18n_current_lang());
}

function reskin_navigation_add_class(DOMElement $node, string $class): void
{
    $existing = trim($node->getAttribute('class'));
    $classes = $existing ? preg_split('/\s+/', $existing) : [];
    if (!in_array($class, $classes, true)) $classes[] = $class;
    $node->setAttribute('class', trim(implode(' ', $classes)));
}

/** Project the whole cleaned source to one sidebar instance without mutating shared data. */
function reskin_sidebar_navigation_html(array $source, string $pageId, array $variant, string $instance, string $toggleLabel): string
{
    if ($source['document'] === null) return '';
    $doc = clone $source['document'];
    $xpath = new DOMXPath($doc);
    $currentId = reskin_navigation_sidebar_id($pageId);
    foreach ($xpath->query('//a[@data-wiki-id]') as $link) {
        if (!($link instanceof DOMElement)) continue;
        $id = $link->getAttribute('data-wiki-id');
        if (!$id) continue;
        $link->setAttribute('href', wl($id, reskin_url_variant_params($variant), false, '&'));
        $linkId = reskin_navigation_sidebar_id($id);
        $current = $linkId === $currentId;
        $parent = !$current && strpos($currentId, $linkId . ':') === 0;
        if ($current) reskin_navigation_add_class($link, 'is-active');
        if (!$current && !$parent) continue;
        $li = $link->parentNode;
        while ($li && $li->nodeName !== 'li') $li = $li->parentNode;
        if ($li instanceof DOMElement) reskin_navigation_add_class($li, $current ? 'is-active' : 'is-active-parent');
    }
    $instance = preg_replace('/[^a-z0-9_-]+/i', '', $instance) ?: 'sidebar';
    $index = 0;
    foreach ($xpath->query('//li[ul]') as $li) {
        if (!($li instanceof DOMElement)) continue;
        $wrapper = null;
        foreach ($li->childNodes as $child) {
            if ($child instanceof DOMElement && strpos(' ' . $child->getAttribute('class') . ' ', ' li ') !== false) {
                $wrapper = $child;
                break;
            }
        }
        if ($wrapper instanceof DOMElement) reskin_navigation_add_class($wrapper, 'reskin-nav-item');
        $toggleId = 'reskin-toggle-' . $instance . '-' . ++$index;
        $input = $doc->createElement('input');
        $input->setAttribute('type', 'checkbox');
        $input->setAttribute('class', 'reskin-nav-toggle-input');
        $input->setAttribute('id', $toggleId);
        $classes = ' ' . $li->getAttribute('class') . ' ';
        if (strpos($classes, ' is-active ') !== false || strpos($classes, ' is-active-parent ') !== false) $input->setAttribute('checked', 'checked');
        $li->insertBefore($input, $li->firstChild);
        if ($wrapper instanceof DOMElement) {
            $label = $doc->createElement('label');
            $label->setAttribute('class', 'reskin-nav-toggle');
            $label->setAttribute('for', $toggleId);
            $label->setAttribute('aria-label', $toggleLabel);
            $wrapper->appendChild($label);
        }
    }
    return (string) preg_replace('/^<\?xml.*?\?>/i', '', $doc->saveHTML());
}

function reskin_current_sidebar_navigation(string $instance): string
{
    global $ID;
    return reskin_sidebar_navigation_html(reskin_current_navigation(), (string) $ID, reskin_variant_context(), $instance, reskin_i18n_t('toggle_navigation'));
}
