<?php
if (!defined('DOKU_INC')) die();

/**
 * Parse explicit source, retaining the existing regex interpretation and byte boundaries.
 * The title participates in anchor collisions; nested/code-like matches are retained.
 * Recent-headings historically ignores empty titles, while article/index lookup does not.
 *
 * @return array<int,array{index:int,level:int,title:string,anchor:string,body:string}>
 */
function reskin_wiki_sections(string $source, bool $skipEmptyTitles = false): array
{
    if (!preg_match_all('/^(={2,6})\s*(.+?)\s*\1\s*$/mu', $source, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) return [];
    $anchors = [];
    $sections = [];
    foreach ($matches as $index => $match) {
        $title = trim((string) $match[2][0]);
        if ($skipEmptyTitles && $title === '') continue;
        $start = $match[0][1] + strlen($match[0][0]);
        $end = $matches[$index + 1][0][1] ?? strlen($source);
        $sections[] = [
            'index' => $index,
            'level' => 7 - strlen($match[1][0]),
            'title' => $title,
            'anchor' => sectionID($title, $anchors),
            'body' => substr($source, $start, $end - $start),
        ];
    }
    return $sections;
}

/**
 * File/ACL boundary with request-local reuse of parsed data, never persistent content caching.
 * Recheck permissions before lookup. Source hashes prevent stale data if a page changes in-process.
 * $useacl=false exists only for legacy caller-authorized article helper compatibility.
 *
 * @return array<int,array{index:int,level:int,title:string,anchor:string,body:string}>
 */
function reskin_read_wiki_sections(string $pageId, bool $trimSource = false, bool $skipEmptyTitles = false, bool $useacl = true): array
{
    if ($useacl && (!page_exists($pageId) || auth_quickaclcheck($pageId) < AUTH_READ)) return [];
    $source = (string) rawWiki($pageId);
    if ($trimSource) $source = trim($source);
    static $cache = [];
    $key = $pageId . '|' . (int) $trimSource . '|' . (int) $skipEmptyTitles . '|' . sha1($source);
    if (!isset($cache[$key])) $cache[$key] = reskin_wiki_sections($source, $skipEmptyTitles);
    return $cache[$key];
}
