<?php
if (!defined('DOKU_INC')) die();

const RESKIN_I18N_LANGS = [
    'cs' => 'Cestina',
    'en' => 'English',
];

const RESKIN_I18N_PRIMARY_LANG = 'cs';
const RESKIN_I18N_FALLBACK_LANG = 'en';
const RESKIN_I18N_EXCLUDED_ROOTS = [
    'wiki',
    'playground',
];

require_once __DIR__ . '/inc/i18n-resolver.php';
require_once __DIR__ . '/inc/home-data.php';
require_once __DIR__ . '/inc/i18n-messages.php';
