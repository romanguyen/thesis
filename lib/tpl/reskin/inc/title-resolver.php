<?php
if (!defined('DOKU_INC')) die();

/**
 * Normalize page IDs so links to namespace starts map consistently.
 *
 * @param string $pageId
 * @return string
 */
function reskin_title_normalize_id(string $pageId): string
{
    $normalized = trim(cleanID($pageId), ':');
    if (substr($normalized, -6) === ':start') {
        $normalized = substr($normalized, 0, -6);
    }
    return $normalized;
}

/**
 * Try to get the current page label directly from localized sidebar links.
 *
 * @param string $id
 * @return string|null
 */
function reskin_title_from_sidebar(string $id): ?string
{
    global $conf;

    $sidebarHtml = reskin_i18n_include_page($conf['sidebar'], false, true);
    if (!is_string($sidebarHtml) || trim($sidebarHtml) === '') {
        return null;
    }

    $currentBaseId = reskin_title_normalize_id($id);

    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="utf-8"?>' . $sidebarHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $xpath = new DOMXPath($doc);
    $links = $xpath->query('//a[@data-wiki-id]');

    foreach ($links as $link) {
        if (!($link instanceof DOMElement)) continue;

        $wikiId = trim((string) $link->getAttribute('data-wiki-id'));
        if ($wikiId === '') continue;

        if (reskin_title_normalize_id($wikiId) !== $currentBaseId) continue;

        $title = trim((string) preg_replace('/\s+/u', ' ', (string) $link->textContent));
        if ($title !== '') {
            return $title;
        }
    }

    return null;
}

/**
 * Resolve browser tab title with sidebar/nav labels as highest priority.
 *
 * @param string|null $id
 * @return string
 */
function reskin_page_title(?string $id = null): string
{
    global $ACT, $ID;

    $id = $id === null ? (string) $ID : (string) $id;

    if ($ACT !== 'show') {
        $actionTitle = html_entity_decode((string) tpl_pagetitle($id, true), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $actionTitle = trim($actionTitle);
        if ($actionTitle !== '') {
            return $actionTitle;
        }
    }

    $sidebarTitle = reskin_title_from_sidebar($id);
    if ($sidebarTitle !== null) {
        return $sidebarTitle;
    }

    $heading = trim((string) p_get_first_heading($id));
    if ($heading !== '') {
        return $heading;
    }

    $fallback = trim((string) noNS($id));
    if ($fallback === '') {
        $fallback = trim((string) html_entity_decode((string) tpl_pagetitle($id, true), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    if ($fallback === '' || $fallback === 'start') {
        return 'MetaCentrum';
    }

    return utf8_ucfirst(str_replace('_', ' ', $fallback));
}
