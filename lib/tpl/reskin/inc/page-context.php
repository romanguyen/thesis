<?php
if (!defined('DOKU_INC')) die();

/** Selectable looks; `layout` only chooses where CESNET's navigation lives. */
const RESKIN_STYLES = ['cesnet', 'metacentrum', 'bootstrap5'];

/**
 * Pure variant normalization: layout (top|left) and style (cesnet|metacentrum|bootstrap5) are independent inputs.
 * Unknown values, including the retired `layout=clean`, fall back to Top and CESNET.
 * @return array{layout:string,style:string}
 */
function reskin_layout_variant(string $layout, string $style = ''): array
{
    $layout = strtolower(trim($layout));
    $style = strtolower(trim($style));
    if (!in_array($style, RESKIN_STYLES, true)) $style = 'cesnet';
    if (!in_array($layout, ['left', 'top'], true)) $layout = 'top';
    return ['layout' => $layout, 'style' => $style];
}

/**
 * Frame the reskin shell renders: MetaCentrum brings its own header/footer (internal key `clean`, used by
 * css/layouts/clean.css and the body/site classes), otherwise the layout. Bootstrap 5 reaches the shell only as a
 * fallback (requests ComboStrap does not render) and then uses the CESNET frame of the selected layout.
 */
function reskin_variant_frame(array $variant): string
{
    return $variant['style'] === 'metacentrum' ? 'clean' : $variant['layout'];
}

/** Preserve the public current-request API and its request-local static variant policy. */
function reskin_variant_context(): array
{
    global $INPUT;
    static $variant;
    // A Bootstrap 5 request that combo declined (e.g. a missing page) reaches the shell without `style`, see plugin-controller.php.
    if (!is_array($variant)) $variant = reskin_layout_variant($INPUT->str('layout'), reskin_combo_request_mode() === 'style' ? 'bootstrap5' : $INPUT->str('style'));
    return $variant;
}

/**
 * Named DokuWiki adapter; the rest of request preparation receives explicit inputs.
 * @return array{id:string,action:mixed,historical:bool,variant:array{layout:string,style:string},fallback_from:string,start:string,sidebar:string}
 */
function reskin_request_state(): array
{
    global $ID, $ACT, $INPUT, $conf, $REV, $DATE_AT;
    return [
        'id' => (string) $ID,
        'action' => $ACT,
        'historical' => !empty($REV) || !empty($DATE_AT),
        'variant' => reskin_variant_context(),
        'fallback_from' => $INPUT->str('fallback_from'),
        'start' => (string) $conf['start'],
        'sidebar' => (string) $conf['sidebar'],
    ];
}

/** Resolve routing data without printing or redirecting. @return array{url:string,missing:?array}|null */
function reskin_request_redirect(array $request): ?array
{
    if ($request['action'] !== 'show' || !empty($request['historical'])) return null;
    $page = reskin_parse_page_id($request['id']);
    if (reskin_i18n_is_excluded_base($page['base'])) return null;
    if (!$page['localized']) {
        $target = reskin_resolve_page($page['base'], RESKIN_I18N_PRIMARY_LANG);
        if (!$target['found'] || $target['id'] === $request['id']) return null;
        return ['url' => wl($target['id'], reskin_url_variant_params($request['variant']), true, '&'), 'missing' => null];
    }
    if (page_exists($request['id'])) return null;
    $target = reskin_resolve_page($page['base'], $page['lang']);
    if (!$target['found'] || $target['id'] === '' || $target['id'] === $request['id']) return null;
    $params = [];
    $missing = null;
    if ($target['lang'] !== $page['lang']) {
        $params['fallback_from'] = $page['lang'];
        $missing = ['base' => $page['base'], 'wanted' => $page['lang'], 'served' => $target['lang']];
    }
    return ['url' => wl($target['id'], reskin_url_variant_params($request['variant'], $params), true, '&'), 'missing' => $missing];
}

/** The only routing side-effect boundary; main invokes this before HTML. */
function reskin_handle_request(array $request): void
{
    $redirect = reskin_request_redirect($request);
    if ($redirect === null) return;
    if ($redirect['missing'] !== null) {
        $missing = $redirect['missing'];
        reskin_i18n_log_missing($missing['base'], $missing['wanted'], $missing['served'], 'route');
    }
    send_redirect($redirect['url'], 302);
}

/** Retained current-request routing API, including its native redirect side effects. */
function reskin_i18n_handle_request(): void
{
    reskin_handle_request(reskin_request_state());
}

/** Explicit notice preparation, preserving valid-language and legacy-page policy. */
function reskin_fallback_notice(array $page, string $from): ?string
{
    $from = trim($from);
    if (!isset(RESKIN_I18N_LANGS[$from])) return null;
    $to = $page['localized'] ? $page['lang'] : RESKIN_I18N_FALLBACK_LANG;
    if (!isset(RESKIN_I18N_LANGS[$to]) || $to === $from) return null;
    return reskin_i18n_t('fallback_notice', [RESKIN_I18N_LANGS[$from], RESKIN_I18N_LANGS[$to]], $page['lang']);
}

/** Retained current-ID/input compatibility API; no removal/deprecation scheduled. */
function reskin_i18n_fallback_notice(): ?string
{
    global $INPUT;
    return reskin_fallback_notice(reskin_i18n_parse_id(), $INPUT->str('fallback_from'));
}

/**
 * Pure visibility policy over explicit page/request inputs and the rendered frame (top|left|clean, see
 * reskin_variant_frame()); sidebar authorization stays with the caller.
 * @return array{
 *   sidebar_aside:bool,top_navigation:bool,sidebar_mobile_trigger:bool,sidebar_offcanvas:bool,
 *   hero:bool,updates_section:bool,updates_inline:bool,metrics_section:bool,stories_section:bool,
 *   metrics_inline:bool,stories_inline:bool,start:bool
 * }
 */
function reskin_page_visibility(string $base, string $startPage, string $layout, mixed $action, bool $hasSidebar): array
{
    $show = $action === 'show';
    $sidebar = $hasSidebar && $show;
    $start = $base === $startPage;
    $leftInline = $show && $start && $layout === 'left';
    return [
        'sidebar_aside' => $sidebar && $layout === 'left',
        // MetaCentrum's navigation is the frame's permanent sidebar, so search, login and editing keep it.
        'top_navigation' => $hasSidebar && ($layout === 'clean' || ($show && $layout === 'top')),
        'sidebar_mobile_trigger' => $sidebar && in_array($layout, ['left', 'top'], true),
        'sidebar_offcanvas' => $sidebar && in_array($layout, ['left', 'top'], true),
        'hero' => $start && $show,
        'updates_section' => $show && $start && !$leftInline,
        'updates_inline' => $show && ($base === 'variants:news' || $leftInline),
        'metrics_section' => $show && $start && !$leftInline,
        'stories_section' => $show && $start && !$leftInline,
        'metrics_inline' => $show && ($base === 'variants:it4i-elements' || $leftInline),
        'stories_inline' => $show && ($base === 'variants:it4i-elements' || $leftInline),
        'start' => $start,
    ];
}

/**
 * Prepare one stable page model; native content/TOC rendering still happens later in main.
 * @param array{id:string,action:mixed,historical?:bool,variant:array{layout:string,style:string},fallback_from:string,start:string,sidebar:string} $request
 * @return array{
 *   request:array{id:string,action:mixed,historical?:bool,variant:array{layout:string,style:string},fallback_from:string,start:string,sidebar:string},
 *   page:array{lang:string,base:string,localized:bool,id:string},variant:array{layout:string,style:string},
 *   show:array{
 *     sidebar_aside:bool,top_navigation:bool,sidebar_mobile_trigger:bool,sidebar_offcanvas:bool,
 *     hero:bool,updates_section:bool,updates_inline:bool,metrics_section:bool,stories_section:bool,
 *     metrics_inline:bool,stories_inline:bool,start:bool
 *   },
 *   news_entries:list<array{id:string,title:string,date_iso:string,date:string,url:string}>,
 *   language_targets:array<string,array{label:string,url:string,url_abs:string,current:bool,available:bool,resolved_lang:string}>,
 *   variant_links:array{layouts:array{left:string,top:string},styles:array{cesnet:string,metacentrum:string,bootstrap5:string}},
 *   fallback_notice:string|null,outdated_notice:string,
 *   hardware:bool,hardware_catalog_url:string,page_class:string
 * }
 */
function reskin_page_context(array $request): array
{
    $page = reskin_parse_page_id($request['id']);
    $show = $request['action'] === 'show';
    $variant = $request['variant'];
    $hasSidebar = reskin_resolve_fragment($request['sidebar'], $page['lang'], $request['id'])['found'];
    $visibility = reskin_page_visibility($page['base'], $request['start'], reskin_variant_frame($variant), $request['action'], $hasSidebar);
    $entries = [];
    if ($show && empty($request['historical']) && $page['base'] === 'news:start' && page_exists($request['id']) && auth_quickaclcheck($request['id']) >= AUTH_READ) {
        $entries = reskin_news_articles($page['lang'], $variant);
    }
    $hardware = preg_match('/^(cs|en):resources:hardware(?::start)?$/', $request['id']) === 1;
    $pageClass = trim((string) preg_replace('/[^a-z0-9_-]+/i', '-', strtolower($request['id'])), '-');
    return [
        'request' => $request, 'page' => $page, 'variant' => $variant,
        'show' => $visibility,
        'news_entries' => $entries,
        'language_targets' => reskin_language_targets($page, $variant),
        'variant_links' => reskin_variant_links($request['id'], $variant),
        'fallback_notice' => reskin_fallback_notice($page, $request['fallback_from']),
        'outdated_notice' => reskin_translation_outdated_notice($request),
        'hardware' => $hardware,
        'hardware_catalog_url' => $hardware ? (DOKU_BASE . 'lib/exe/fetch.php?media=' . rawurlencode('hardware:catalog.json')) : '',
        'page_class' => 'reskin-pageid-' . ($pageClass !== '' ? $pageClass : 'start'),
    ];
}
