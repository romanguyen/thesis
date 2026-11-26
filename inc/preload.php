<?php
/**
 * Project preload (native DokuWiki hook, see inc/preload.php.dist).
 * Confines the ComboStrap plugin to the requests that use it: lib/tpl/reskin/inc/plugin-controller.php.
 */
if (!defined('DOKU_INC')) die();

spl_autoload_register(static function ($class) {
    if ($class === 'ReskinPluginController') require_once DOKU_INC . 'lib/tpl/reskin/inc/plugin-controller.php';
});
$plugin_controller_class = 'ReskinPluginController';
