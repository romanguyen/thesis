<?php
if (!defined('DOKU_INC')) die();

/** Pure layout normalization; style remains derived from the selected layout. @return array{layout:string,style:string} */
function reskin_layout_variant(string $layout): array
{
    $layout = strtolower(trim($layout));
    if (!in_array($layout, ['left', 'top', 'clean'], true)) $layout = 'top';
    return ['layout' => $layout, 'style' => $layout === 'top' ? 'cesnet' : 'reskin'];
}

/** Preserve the public current-request API and its request-local static layout policy. */
function reskin_variant_context(): array
{
    global $INPUT;
    static $variant;
    if (!is_array($variant)) $variant = reskin_layout_variant($INPUT->str('layout'));
    return $variant;
}

/**
 * Named DokuWiki adapter; the rest of request preparation receives explicit inputs.
 * @return array{id:string,action:mixed,variant:array{layout:string,style:string},article_id:string,fallback_from:string,start:string,sidebar:string}
 */
function reskin_request_state(): array
{
    global $ID, $ACT, $INPUT, $conf;
    return [
        'id' => (string) $ID,
        'action' => $ACT,
        'variant' => reskin_variant_context(),
        'article_id' => $INPUT->str('article'),
        'fallback_from' => $INPUT->str('fallback_from'),
        'start' => (string) $conf['start'],
        'sidebar' => (string) $conf['sidebar'],
    ];
}

/** Resolve routing data without printing or redirecting. @return array{url:string,missing:?array}|null */
function reskin_request_redirect(array $request): ?array
{
    if ($request['action'] !== 'show') return null;
    $page = reskin_parse_page_id($request['id']);
    if (reskin_i18n_is_excluded_base($page['base'])) return null;
    if (!$page['localized']) {
        $target = reskin_resolve_page($page['base'], RESKIN_I18N_PRIMARY_LANG, true, true);
        if (!$target['found'] || $target['legacy'] || $target['id'] === $request['id']) return null;
        return ['url' => wl($target['id'], reskin_url_variant_params($request['variant']), true, '&'), 'missing' => null];
    }
    if (page_exists($request['id'])) return null;
    $target = reskin_resolve_page($page['base'], $page['lang'], true, true);
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

function reskin_i18n_fallback_notice(): ?string
{
    global $INPUT;
    return reskin_fallback_notice(reskin_i18n_parse_id(), $INPUT->str('fallback_from'));
}

/**
 * Pure visibility policy over explicit page/request inputs; sidebar authorization stays with the caller.
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
        'top_navigation' => $sidebar && $layout === 'top',
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
 * Language selection may use an article during edit/preview, but reading-view selection may not.
 * @param array{id:string,action:mixed,variant:array{layout:string,style:string},article_id:string,fallback_from:string,start:string,sidebar:string} $request
 * @return array{
 *   request:array{id:string,action:mixed,variant:array{layout:string,style:string},article_id:string,fallback_from:string,start:string,sidebar:string},
 *   page:array{lang:string,base:string,localized:bool,id:string},variant:array{layout:string,style:string},
 *   show:array{
 *     sidebar_aside:bool,top_navigation:bool,sidebar_mobile_trigger:bool,sidebar_offcanvas:bool,
 *     hero:bool,updates_section:bool,updates_inline:bool,metrics_section:bool,stories_section:bool,
 *     metrics_inline:bool,stories_inline:bool,start:bool
 *   },
 *   news_entries:list<array<string,mixed>>,news_article:array<string,mixed>|null,
 *   language_targets:array<string,array{label:string,url:string,url_abs:string,current:bool,available:bool,resolved_lang:string}>,
 *   layout_links:array{clean:string,left:string,top:string},fallback_notice:string|null,
 *   hardware:bool,hardware_catalog_url:string,page_class:string,breadcrumb_html:string
 * }
 */
function reskin_page_context(array $request): array
{
    $page = reskin_parse_page_id($request['id']);
    $show = $request['action'] === 'show';
    $variant = $request['variant'];
    $hasSidebar = reskin_resolve_fragment($request['sidebar'], $page['lang'], $request['id'])['found'];
    $visibility = reskin_page_visibility($page['base'], $request['start'], $variant['layout'], $request['action'], $hasSidebar);
    $entries = [];
    $selection = null;
    if ($page['base'] === 'news:start' && page_exists($request['id']) && auth_quickaclcheck($request['id']) >= AUTH_READ) {
        if ($show || ($request['article_id'] !== '' && strlen($request['article_id']) <= 200)) {
            $entries = reskin_news_articles($request['id'], $page['lang']);
            $selection = reskin_select_news_article($entries, $request['article_id']);
        }
    }
    $hardware = preg_match('/^(cs|en):resources:hardware(?::start)?$/', $request['id']) === 1;
    $pageClass = trim((string) preg_replace('/[^a-z0-9_-]+/i', '-', strtolower($request['id'])), '-');
    $breadcrumb = $variant['layout'] === 'clean' && $show
        ? reskin_render_breadcrumb($variant, static function () { return tpl_youarehere(' / ', true); })
        : '';
    return [
        'request' => $request, 'page' => $page, 'variant' => $variant,
        'show' => $visibility,
        'news_entries' => $show ? $entries : [],
        'news_article' => $show ? $selection : null,
        'language_targets' => reskin_language_targets($page, $variant, $selection),
        'layout_links' => reskin_layout_links($request['id']),
        'fallback_notice' => reskin_fallback_notice($page, $request['fallback_from']),
        'hardware' => $hardware,
        'hardware_catalog_url' => $hardware ? (DOKU_BASE . 'lib/exe/fetch.php?media=' . rawurlencode('hardware:catalog.json')) : '',
        'page_class' => 'reskin-pageid-' . ($pageClass !== '' ? $pageClass : 'start'),
        'breadcrumb_html' => $breadcrumb,
    ];
}
