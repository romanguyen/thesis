<?php
if (!defined('DOKU_INC')) die();

require_once __DIR__ . '/i18n-config.php';

/**
 * Confines the ComboStrap plugin (`combo`) to the requests that use it (see inc/combo.php and
 * thesis/PLAN_BOOTSTRAP5_EXTENSION.md). Combo overrides native syntax (bold, headings, paragraphs, tables, edit
 * markers) and has no setting to stop that, so on every other request the plugin is treated as disabled: native
 * pages parse and render exactly as without combo.
 *
 * Loaded from inc/preload.php. Combo requests:
 * - `components`: pages in the `demo` namespace (any language prefix), rendered by reskin with combo's wiki-syntax
 *   components (theme system off in conf/local.php, output filtered by inc/combo.php); plus combo's own routes:
 *   `fetch.php?drive=combo|combo-theme`, ajax `call=combo...` and `css.php` carrying combo's `combo-theme-enabled`
 *   flag (combo then serves its theme pages without template/core styles);
 * - `style`: the Bootstrap 5 style (`style=bootstrap5`) on a shown, current, localized page. Combo's theme system
 *   renders the whole page with the project theme `combo-theme/bootstrap5` (reskin's main.php does not run).
 *   Other actions (edit, search, login, revisions...) stay native and reskin renders them in the CESNET look.
 * Every combo request parses with separate parser cache keys, so native requests never reuse combo instructions.
 */

const RESKIN_COMBO_PAGE_PATTERN = '~^(?:[a-z]{2}:)?demo(?::|$)~';
const RESKIN_COMBO_THEME = 'bootstrap5';
const RESKIN_COMBO_HOOK_SEQUENCE = 1000; // combo registers at the default 0

/** Pure decision for combo's components and routes, so it can be tested without a request. */
function reskin_combo_scope_matches(string $id, string $drive = '', string $call = '', bool $themeCss = false): bool
{
    return preg_match(RESKIN_COMBO_PAGE_PATTERN, $id) === 1 || str_starts_with($drive, 'combo') || str_starts_with($call, 'combo') || $themeCss;
}

/** Pure decision for the Bootstrap 5 style: shown current revisions of localized pages only. */
function reskin_combo_style_matches(string $id, string $style, mixed $do, bool $historical): bool
{
    $lang = explode(':', $id, 2)[0];
    return $style === 'bootstrap5' && in_array($do, ['', 'show'], true) && !$historical
        && str_contains($id, ':') && isset(RESKIN_I18N_LANGS[$lang]);
}

/**
 * Request-time settings for the Bootstrap 5 style; conf/local.php keeps the components-only defaults.
 * @return array{plugin:array<string,int|string>,lang:string}
 */
function reskin_combo_style_settings(string $id): array
{
    return [
        'plugin' => [
            'combo-conf-001' => 1, // theme system: combo renders the whole page
            'combo-conf-005' => RESKIN_COMBO_THEME,
            'bootstrapVersionStylesheet' => '5.3.8 - bootstrap',
            'defaultLayoutContainer' => 'xl',
            // Combo owns this page, so its link, heading, code and image rendering apply as designed. Its highlight
            // stays off (conf/local.php): it turns DokuWiki ''monospace'' into a light <mark> that is unreadable in dark mode.
            'disableLink' => 0,
            'headingWikiEnable' => 1,
            'preformattedEnable' => 1,
            'imageEnable' => 1,
            'privateRailbar' => 1, // no combo admin railbar for anonymous visitors
        ],
        // <html lang>, DokuWiki and combo strings follow the page language.
        'lang' => explode(':', $id, 2)[0],
    ];
}

/** Combo mode of the current request, decided once: '' (disabled), 'components' or 'style'. */
function reskin_combo_request_mode(): string
{
    static $mode = null;
    if ($mode !== null) return $mode;
    global $INPUT;
    if (!$INPUT) return ''; // too early to know; not cached
    $id = getID('id', true);
    if (reskin_combo_style_matches($id, $INPUT->str('style'), $INPUT->param('do', ''), $INPUT->has('rev') || $INPUT->has('at'))) {
        return $mode = 'style';
    }
    $themeCss = str_ends_with($INPUT->server->str('SCRIPT_NAME'), '/css.php') && $INPUT->has('combo-theme-enabled');
    return $mode = reskin_combo_scope_matches($id, $INPUT->str('drive'), $INPUT->str('call'), $themeCss) ? 'components' : '';
}

/** True when the current request should run with combo enabled. */
function reskin_combo_request_active(): bool
{
    return reskin_combo_request_mode() !== '';
}

/**
 * PARSER_CACHE_USE: pages parsed with combo enabled (and the fragments they include) get their own cache files.
 * Metadata is different: `.meta` is shared and persistent, so a combo request reuses existing metadata instead of
 * re-rendering it in combo mode (native requests would keep that). The next native request refreshes stale metadata.
 */
function reskin_separate_combo_parser_cache($event): void
{
    $cache = $event->data;
    if ($cache->mode === 'metadata' && file_exists(metaFN($cache->page, '.meta'))) {
        $event->preventDefault();
        $event->result = true;
        return;
    }
    if (strpos($cache->key, '|combo') !== false) return;
    $cache->key .= '|combo';
    $cache->cache = getCacheName($cache->key, $cache->ext);
}

/**
 * Combo reads custom themes only from <savedir>/combo/theme/; the tracked source stays in the template.
 * Creates the link if it is missing (relative for the default data directory); a manual link works too.
 */
function reskin_combo_link_theme(): void
{
    global $conf;
    $link = $conf['savedir'] . '/combo/theme/' . RESKIN_COMBO_THEME;
    if (is_dir($link)) return;
    if (is_link($link)) @unlink($link); // dangling after the checkout moved
    $source = 'lib/tpl/reskin/combo-theme/' . RESKIN_COMBO_THEME;
    @mkdir(dirname($link), 0777, true);
    @symlink(realpath($conf['savedir']) === realpath(DOKU_INC . 'data') ? '../../../' . $source : DOKU_INC . $source, $link);
}

/**
 * In-memory action component that the controller below adds to combo requests: plugin-load time is the first point
 * where a hook can be registered, and doku.php's pageinfo() may already parse before any template code runs.
 */
class action_plugin_reskincombo extends \dokuwiki\Extension\ActionPlugin
{
    public function getInfo()
    {
        return ['base' => 'reskincombo', 'name' => 'reskin combo guard', 'desc' => 'Separate parser cache for combo requests (lib/tpl/reskin/inc/plugin-controller.php)'];
    }

    public function register(\dokuwiki\Extension\EventHandler $controller)
    {
        $controller->register_hook('PARSER_CACHE_USE', 'BEFORE', null, 'reskin_separate_combo_parser_cache', null, -RESKIN_COMBO_HOOK_SEQUENCE);
    }
}

class ReskinPluginController extends \dokuwiki\Extension\PluginController
{
    private bool $comboActive = false;

    public function __construct()
    {
        global $conf;
        parent::__construct();
        if (empty($this->masterList['combo'])) return;
        if (!reskin_combo_request_active()) {
            $this->masterList['combo'] = 0;
            return;
        }
        $this->comboActive = true;
        if (reskin_combo_request_mode() !== 'style') return;
        $settings = reskin_combo_style_settings(getID('id', true));
        $conf['plugin']['combo'] = array_replace($conf['plugin']['combo'] ?? [], $settings['plugin']);
        $conf['lang'] = $settings['lang'];
        // Combo turns every request parameter into a fetcher attribute and parses `style` as inline CSS; the
        // decision is made, so hide it (reskin_variant_context() asks reskin_combo_request_mode() instead).
        unset($_GET['style'], $_POST['style'], $_REQUEST['style']);
        reskin_combo_link_theme();
    }

    public function getList($type = '', $all = false)
    {
        $list = parent::getList($type, $all);
        if ($this->comboActive && $type === 'action') $list[] = 'reskincombo';
        return $list;
    }

    public function load($type, $name, $new = false, $disabled = false)
    {
        if ($this->comboActive && $type === 'action' && $name === 'reskincombo') {
            return $GLOBALS['DOKU_PLUGINS']['action']['reskincombo'] ??= new action_plugin_reskincombo();
        }
        return parent::load($type, $name, $new, $disabled);
    }
}
