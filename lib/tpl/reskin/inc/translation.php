<?php
if (!defined('DOKU_INC')) die();

/**
 * Translation plugin (`translation`, lib/plugins/translation) bridge. The template owns language routing, the
 * missing-translation fallback, the language switcher and hreflang links (inc/page-context.php, inc/links.php,
 * head.php); no plugin offers the fallback. The plugin adds what the template does not: DokuWiki's own interface
 * follows the page language (`translateui`), outdated translations get a notice (`checkage`), new translations open
 * pre-filled (`copytrans`), and admins/managers get an overview of missing and outdated translations. Its switcher
 * (showTranslations()) is never printed. Everything below does nothing when the plugin is disabled or missing.
 */

const RESKIN_TRANSLATION_HOOK_SEQUENCE = 1000; // the plugin registers at the default 0

/** Register once per request, before tpl_metaheaders() runs. */
function reskin_register_translation_guards(): void
{
    global $EVENT_HANDLER;
    static $done = false;
    if ($done || !$EVENT_HANDLER || plugin_isdisabled('translation')) return;
    $done = true;
    $EVENT_HANDLER->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', null, 'reskin_drop_translation_alternates', null, RESKIN_TRANSLATION_HOOK_SEQUENCE);
}

/** head.php prints the hreflang alternates from the template's availability rules; drop the plugin's copies. */
function reskin_drop_translation_alternates($event): void
{
    if (!is_array($event->data['link'] ?? null)) return;
    $event->data['link'] = array_values(array_filter($event->data['link'], static function ($link): bool {
        return !(is_array($link) && strtolower((string) ($link['rel'] ?? '')) === 'alternate' && isset($link['hreflang']));
    }));
}

/**
 * The plugin's outdated-translation notice (native `div.notify` markup) for the current revision of a shown page.
 * Only logged-in users who may edit the page see it: the plugin compares save times, so any Czech edit flags the
 * English page, and readers cannot act on it. Login is checked too, because a wiki may give guests edit rights.
 */
function reskin_translation_outdated_notice(array $request): string
{
    global $INPUT;
    if ($request['action'] !== 'show' || !empty($request['historical'])) return '';
    if ($INPUT->server->str('REMOTE_USER') === '' || auth_quickaclcheck($request['id']) < AUTH_EDIT) return '';
    $helper = plugin_load('helper', 'translation');
    if (!$helper) return '';
    ob_start();
    $helper->checkage();
    return (string) ob_get_clean();
}
