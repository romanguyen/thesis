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
    return reskin_navigation_title_id($pageId);
}

/**
 * Try to get the current page label directly from localized sidebar links.
 *
 * @param string $id
 * @return string|null
 */
function reskin_title_from_sidebar(string $id): ?string
{
    // Explicit lookup IDs still use the current request's resolved sidebar scope.
    return reskin_navigation_label(reskin_current_navigation(), $id);
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
