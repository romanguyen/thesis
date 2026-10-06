<?php
if (!defined('DOKU_INC')) die();

/** @return array{lang:string,base:string,localized:bool,id:string} */
function reskin_parse_page_id(string $id): array
{
    $raw = trim($id, ':');
    if ($raw === '') $raw = 'start';
    $parts = explode(':', $raw);
    $first = $parts[0] ?? '';
    if (isset(RESKIN_I18N_LANGS[$first])) {
        array_shift($parts);
        $base = trim(implode(':', $parts), ':');
        if ($base === '') $base = 'start';
        return ['lang' => $first, 'base' => cleanID($base), 'localized' => true, 'id' => cleanID($raw)];
    }
    return ['lang' => RESKIN_I18N_PRIMARY_LANG, 'base' => cleanID($raw), 'localized' => false, 'id' => cleanID($raw)];
}

/** Compatibility adapter for callers using the current DokuWiki ID. */
function reskin_i18n_parse_id(?string $id = null): array
{
    global $ID;
    return reskin_parse_page_id((string) ($id ?? $ID ?? ''));
}

function reskin_i18n_current_lang(): string
{
    return reskin_i18n_parse_id()['lang'];
}

function reskin_i18n_build_id(string $base, string $lang): string
{
    $base = trim(cleanID($base), ':');
    if ($base === '') $base = 'start';
    return cleanID($lang . ':' . $base);
}

/** DokuWiki integration: use its configured namespace-start resolver. @return string[] */
function reskin_i18n_id_variants(string $base, string $lang): array
{
    global $conf;
    $base = trim(cleanID($base), ':');
    if ($base === '') $base = 'start';
    $variants = [reskin_i18n_build_id($base, $lang)];
    $start = trim((string) ($conf['start'] ?? 'start'), ':');
    if ($start === '') $start = 'start';
    $isExplicitStart = $base === $start || preg_match('/(^|:)' . preg_quote($start, '/') . '$/', $base);
    if (!$isExplicitStart) {
        $resolver = new \dokuwiki\File\PageResolver('');
        $resolvedNamespace = cleanID($resolver->resolveId($variants[0] . ':'));
        if ($resolvedNamespace !== '') $variants[] = $resolvedNamespace;
    }
    return array_values(array_unique($variants));
}

function reskin_i18n_is_excluded_base(string $base): bool
{
    $base = trim($base, ':');
    if ($base === '') return false;
    return in_array(strtok($base, ':'), RESKIN_I18N_EXCLUDED_ROOTS, true);
}

/**
 * ACL-aware DokuWiki page lookup with an explicit language preference.
 * @return array{id:string,lang:string,base:string,found:bool,fallback:bool,legacy:bool}
 */
function reskin_resolve_page(string $base, string $preferred, bool $allowLegacy = true, bool $useacl = true): array
{
    if (!isset(RESKIN_I18N_LANGS[$preferred])) $preferred = RESKIN_I18N_PRIMARY_LANG;
    $base = trim(cleanID($base), ':');
    if ($base === '') $base = 'start';
    $langs = array_values(array_unique([$preferred, RESKIN_I18N_FALLBACK_LANG, RESKIN_I18N_PRIMARY_LANG]));
    foreach ($langs as $lang) {
        foreach (reskin_i18n_id_variants($base, $lang) as $candidate) {
            if (page_exists($candidate) && (!$useacl || auth_quickaclcheck($candidate) >= AUTH_READ)) {
                return ['id' => $candidate, 'lang' => $lang, 'base' => $base, 'found' => true, 'fallback' => $lang !== $preferred, 'legacy' => false];
            }
        }
    }
    if ($allowLegacy) {
        $legacy = cleanID($base);
        if (page_exists($legacy) && (!$useacl || auth_quickaclcheck($legacy) >= AUTH_READ)) {
            return ['id' => $legacy, 'lang' => RESKIN_I18N_FALLBACK_LANG, 'base' => $base, 'found' => true, 'fallback' => true, 'legacy' => true];
        }
    }
    return ['id' => reskin_i18n_build_id($base, $preferred), 'lang' => $preferred, 'base' => $base, 'found' => false, 'fallback' => true, 'legacy' => false];
}

/** Compatibility adapter: omitted preference follows the current page. */
function reskin_i18n_resolve_exact(string $base, ?string $preferredLang = null, bool $allowLegacy = true, bool $useacl = true): array
{
    return reskin_resolve_page($base, $preferredLang ?: reskin_i18n_current_lang(), $allowLegacy, $useacl);
}

/** @return string[] */
function reskin_parent_namespaces(string $pageId): array
{
    $parts = explode(':', reskin_parse_page_id($pageId)['base']);
    array_pop($parts);
    $namespaces = [];
    while (true) {
        $namespaces[] = implode(':', $parts);
        if (count($parts) === 0) break;
        array_pop($parts);
    }
    return $namespaces;
}

function reskin_i18n_parent_namespaces(?string $id = null): array
{
    global $ID;
    return reskin_parent_namespaces((string) ($id ?? $ID ?? ''));
}

function reskin_find_fragment_in_lang(string $fragment, string $lang, string $pageId, bool $useacl = true): ?string
{
    $fragment = trim(cleanID($fragment), ':');
    if ($fragment === '') return null;
    foreach (reskin_parent_namespaces($pageId) as $ns) {
        $base = $ns !== '' ? ($ns . ':' . $fragment) : $fragment;
        foreach (reskin_i18n_id_variants($base, $lang) as $candidate) {
            if (page_exists($candidate) && (!$useacl || auth_quickaclcheck($candidate) >= AUTH_READ)) return $candidate;
        }
    }
    return null;
}

function reskin_i18n_find_fragment_in_lang(string $fragment, string $lang, bool $useacl = true): ?string
{
    global $ID;
    return reskin_find_fragment_in_lang($fragment, $lang, (string) $ID, $useacl);
}

/** @return array{id:string,lang:string,base:string,found:bool,fallback:bool,legacy:bool} */
function reskin_resolve_fragment(string $fragment, string $preferred, string $pageId, bool $useacl = true): array
{
    if (!isset(RESKIN_I18N_LANGS[$preferred])) $preferred = RESKIN_I18N_PRIMARY_LANG;
    $fragment = trim(cleanID($fragment), ':');
    if ($fragment === '') $fragment = 'start';
    $langs = array_values(array_unique([$preferred, RESKIN_I18N_FALLBACK_LANG, RESKIN_I18N_PRIMARY_LANG]));
    foreach ($langs as $lang) {
        $candidate = reskin_find_fragment_in_lang($fragment, $lang, $pageId, $useacl);
        if ($candidate) {
            return ['id' => $candidate, 'lang' => $lang, 'base' => $fragment, 'found' => true, 'fallback' => $lang !== $preferred, 'legacy' => false];
        }
    }

    // Native legacy lookup is current-ID based; confine that dependency to this boundary.
    global $ID;
    $originalId = $ID;
    try {
        $ID = $pageId;
        $legacy = page_findnearest($fragment, $useacl);
    } finally {
        $ID = $originalId;
    }
    if ($legacy) {
        return ['id' => $legacy, 'lang' => RESKIN_I18N_FALLBACK_LANG, 'base' => $fragment, 'found' => true, 'fallback' => true, 'legacy' => true];
    }
    return ['id' => '', 'lang' => $preferred, 'base' => $fragment, 'found' => false, 'fallback' => true, 'legacy' => false];
}

function reskin_i18n_resolve_fragment(string $fragment, ?string $preferredLang = null, bool $useacl = true): array
{
    global $ID;
    return reskin_resolve_fragment($fragment, $preferredLang ?: reskin_i18n_current_lang(), (string) $ID, $useacl);
}

function reskin_i18n_log_missing(string $base, string $wantedLang, string $servedLang, string $context = 'page'): void
{
    if ($wantedLang === $servedLang) return;
    static $logged = [];
    $key = $context . '|' . $base . '|' . $wantedLang . '|' . $servedLang;
    if (isset($logged[$key])) return;
    $logged[$key] = true;
    if (class_exists('\\dokuwiki\\Logger')) {
        \dokuwiki\Logger::debug('reskin i18n fallback', ['context' => $context, 'base' => $base, 'wanted_lang' => $wantedLang, 'served_lang' => $servedLang]);
    }
}

/** Native fragment rendering boundary; restore DokuWiki's own TOC handling. */
function reskin_include_page(string $page, string $preferred, string $pageId, bool $print = true, bool $propagate = true, bool $useacl = true)
{
    $resolved = $propagate
        ? reskin_resolve_fragment($page, $preferred, $pageId, $useacl)
        : reskin_resolve_page($page, $preferred, true, $useacl);
    if (!$resolved['found'] || $resolved['id'] === '') return false;
    if ($resolved['fallback']) reskin_i18n_log_missing($page, $preferred, $resolved['lang'], 'include');
    return tpl_include_page($resolved['id'], $print, false, $useacl);
}

function reskin_i18n_include_page(string $page, bool $print = true, bool $propagate = true, bool $useacl = true)
{
    global $ID;
    return reskin_include_page($page, reskin_i18n_current_lang(), (string) $ID, $print, $propagate, $useacl);
}
