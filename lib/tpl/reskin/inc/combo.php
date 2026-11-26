<?php
if (!defined('DOKU_INC')) die();

/**
 * ComboStrap (plugin `combo`) is used for its wiki-syntax components only; reskin owns the page shell and
 * Bootstrap (see thesis/PLAN_BOOTSTRAP5_EXTENSION.md). Combo has no setting for the output removed here.
 * Combo itself only runs on the requests selected in inc/plugin-controller.php (demo pages; the Bootstrap 5 style is
 * rendered by combo's own theme and never reaches this shell unless combo declines the page); everywhere else it is
 * disabled, so native pages are untouched. Everything below does nothing when combo is disabled. The separate
 * parser cache for combo requests is registered earlier, in inc/plugin-controller.php. Dependencies on
 * combo v1.25.5:
 * - the `snippet-bootstrap-cs` class on every Bootstrap/Popper/jQuery head entry (Snippet::getClass);
 * - page-menu item classes in the `ComboStrap\` namespace, and BacklinkMenuItem standing in for native Backlink;
 * - the meta names listed in reskin_combo_foreign_meta();
 * - combo's section-edit buttons in its own format carry no `secid` (core requires it);
 * - the `ano`, `minimal-front` and `combo-theme-enabled` flags combo appends to the css.php link: for anonymous
 *   visitors they cut css.php from ~51 KB to ~5 KB (DokuWiki core, template and plugin styles);
 * - combo's snippet scripts for the removed menu items, appended to the page body (non-strap templates) as
 *   `<script class="snippet-backlink|metadata|quality-cs">`;
 * - combo's TPL_TOC_RENDER handler, which replaces the TOC with ids that do not match the native headings.
 * tests/m4.php pins these; re-check them on every combo update.
 */

const RESKIN_COMBO_BOOTSTRAP_CLASS = 'snippet-bootstrap-cs';

/** Register once per request, before tpl_metaheaders() and the page menu run. */
function reskin_register_combo_guards(): void
{
    global $EVENT_HANDLER;
    static $done = false;
    if ($done || !$EVENT_HANDLER || !reskin_combo_request_active()) return;
    $done = true;
    $EVENT_HANDLER->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', null, 'reskin_drop_combo_head_entries', null, RESKIN_COMBO_HOOK_SEQUENCE);
    // Same event, before combo (sequence 0) and after it: keep the native TOC.
    $EVENT_HANDLER->register_hook('TPL_TOC_RENDER', 'BEFORE', null, 'reskin_keep_native_toc', 'save', -RESKIN_COMBO_HOOK_SEQUENCE);
    $EVENT_HANDLER->register_hook('TPL_TOC_RENDER', 'BEFORE', null, 'reskin_keep_native_toc', 'restore', RESKIN_COMBO_HOOK_SEQUENCE);
    $EVENT_HANDLER->register_hook('HTML_SECEDIT_BUTTON', 'BEFORE', null, 'reskin_drop_combo_secedit_button', null, RESKIN_COMBO_HOOK_SEQUENCE);
    $EVENT_HANDLER->register_hook('RENDERER_CONTENT_POSTPROCESS', 'AFTER', null, 'reskin_drop_combo_menu_snippets', null, RESKIN_COMBO_HOOK_SEQUENCE);
    $EVENT_HANDLER->register_hook('MENU_ITEMS_ASSEMBLY', 'AFTER', null, 'reskin_drop_combo_menu_items', null, RESKIN_COMBO_HOOK_SEQUENCE);
}

/** True for the meta tags combo adds on every page (generator, social cards, app names, CSP). */
function reskin_combo_foreign_meta(array $meta): bool
{
    $name = strtolower($meta['name'] ?? '');
    $property = strtolower($meta['property'] ?? '');
    $httpEquiv = strtolower($meta['http-equiv'] ?? '');
    return in_array($name, ['generator', 'application-name', 'apple-mobile-web-app-title'], true)
        || str_starts_with($name, 'twitter:')
        || str_starts_with($property, 'og:')
        || str_starts_with($property, 'fb:')
        || $httpEquiv === 'content-security-policy';
}

/** TPL_METAHEADER_OUTPUT: combo's Bootstrap/Popper entries (reskin loads its own 5.3.8) and its meta tags. */
function reskin_drop_combo_head_entries($event): void
{
    foreach ($event->data as $type => $entries) {
        if (!is_array($entries)) continue;
        $event->data[$type] = array_values(array_filter(array_map(function ($entry) use ($type) {
            if (is_array($entry) && $type === 'link' && isset($entry['href']) && strpos($entry['href'], 'css.php') !== false) {
                $entry['href'] = preg_replace('/&(?:amp;)?(?:ano|minimal-front|combo-theme-enabled)(?=&|$)/', '', $entry['href']);
            }
            return $entry;
        }, $entries), function ($entry) use ($type) {
            if (!is_array($entry)) return true;
            if (strpos((string)($entry['class'] ?? ''), RESKIN_COMBO_BOOTSTRAP_CLASS) !== false) return false;
            return !($type === 'meta' && reskin_combo_foreign_meta($entry));
        }));
    }
}

/** MENU_ITEMS_ASSEMBLY: combo's page-menu items; its BacklinkMenuItem replaces native Backlink, so restore that. */
function reskin_drop_combo_menu_items($event): void
{
    $items = [];
    foreach ($event->data['items'] as $item) {
        if (strpos(get_class($item), 'ComboStrap\\') !== 0) {
            $items[] = $item;
        } elseif (get_class($item) === 'ComboStrap\\BacklinkMenuItem') {
            $items[] = new \dokuwiki\Menu\Item\Backlink();
        }
    }
    $event->data['items'] = $items;
}

/** HTML_SECEDIT_BUTTON: combo adds a second button per section without `secid`; keep only the native one. */
function reskin_drop_combo_secedit_button($event): void
{
    if (isset($event->data['secid'])) return;
    $event->preventDefault();
    $event->result = '';
}

/** TPL_TOC_RENDER: combo's handler (sequence 0) replaces the TOC; save it before and put it back after. */
function reskin_keep_native_toc($event, $step): void
{
    static $native = null;
    if ($step === 'save') {
        $native = $event->data;
    } elseif ($native !== null) {
        $event->data = $native;
        $native = null;
    }
}

/** RENDERER_CONTENT_POSTPROCESS: combo appends the scripts of its menu items (removed above) to the page body. */
function reskin_drop_combo_menu_snippets($event): void
{
    if (($event->data[0] ?? '') !== 'xhtml' || !is_string($event->data[1] ?? null)) return;
    $event->data[1] = preg_replace('~<script class="snippet-(?:backlink|metadata|quality)-cs"[^>]*>.*?</script>\s*~s', '', $event->data[1]);
}
