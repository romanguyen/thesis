<?php
if (!defined('DOKU_INC')) die();

/** Pure presentation-independent extraction from prepared sections. @return array<int,array<string,mixed>> */
function reskin_news_articles_from_sections(array $sections, string $lang): array
{
    $articles = [];
    foreach ($sections as $section) {
        if ($section['index'] === 0) continue;
        $body = trim($section['body']);
        $dateIso = reskin_i18n_heading_date($body, 'news:start', $lang);
        $author = $permalink = '';
        $byline = '/^\s*\[\[(https?:\/\/[^\]|]+)\|permalink\]\]\s*\/\/([^\/\r\n]+)\/\/,\s*[^\r\n]*$/mi';
        if (preg_match($byline, $body, $meta)) {
            if (filter_var($meta[1], FILTER_VALIDATE_URL)) $permalink = $meta[1];
            $author = trim($meta[2]);
            $body = trim((string) preg_replace($byline, '', $body, 1));
        }
        $externalUrl = $externalTitle = '';
        if (preg_match('/(?:Více informací|More (?:info|information))[^\r\n]*?\[\[(https?:\/\/[^\]|]+)(?:\|([^\]]+))?\]\]/iu', $body, $link)) {
            if (filter_var($link[1], FILTER_VALIDATE_URL)) {
                $externalUrl = $link[1];
                $externalTitle = trim($link[2] ?? '') ?: $externalUrl;
            }
        }
        $articles[] = [
            'index' => $section['index'], 'anchor' => $section['anchor'], 'title' => $section['title'],
            'date' => reskin_i18n_format_update_date($dateIso, $lang), 'date_iso' => $dateIso,
            'author' => $author, 'body' => $body, 'permalink' => $permalink,
            'external_url' => $externalUrl, 'external_title' => $externalTitle,
        ];
    }
    return $articles;
}

/** Authorized native read for new request-context callers. */
function reskin_news_articles(string $pageId, string $lang): array
{
    return reskin_news_articles_from_sections(reskin_read_wiki_sections($pageId), $lang);
}

/** Compatibility API: its original contract requires the caller to authorize the page. */
function reskin_i18n_news_articles(string $pageId, string $lang): array
{
    return reskin_news_articles_from_sections(reskin_read_wiki_sections($pageId, false, false, false), $lang);
}

/** Select from already prepared data; missing/overlong IDs retain archive fallback. */
function reskin_select_news_article(array $articles, string $articleId): ?array
{
    if ($articleId === '' || strlen($articleId) > 200) return null;
    foreach ($articles as $article) {
        if ($article['anchor'] === $articleId) return $article;
    }
    return null;
}

function reskin_i18n_news_article(string $pageId, string $articleId, string $lang): ?array
{
    if ($articleId === '' || strlen($articleId) > 200) return null;
    return reskin_select_news_article(reskin_i18n_news_articles($pageId, $lang), $articleId);
}

function reskin_section_anchor_at_index(array $sections, int $index): string
{
    if ($index < 1) return '';
    foreach ($sections as $section) {
        if ($section['index'] === $index) return $section['anchor'];
    }
    return '';
}

function reskin_i18n_news_anchor_at_index(string $pageId, int $index): string
{
    if ($index < 1) return '';
    return reskin_section_anchor_at_index(reskin_read_wiki_sections($pageId, false, false, false), $index);
}

/** Recent-update adapter: preserve source trimming, empty-title policy and display-language dates. */
function reskin_i18n_recent_headings(string $base, int $limit = 3, ?string $preferredLang = null): array
{
    $limit = max(1, $limit);
    $preferred = $preferredLang ?: reskin_i18n_current_lang();
    $resolved = reskin_resolve_page($base, $preferred, true, true);
    if (!$resolved['found'] || $resolved['id'] === '') return [];
    $sections = reskin_read_wiki_sections($resolved['id'], true, true);
    $items = [];
    $skipTitle = true;
    foreach ($sections as $section) {
        if ($skipTitle) {
            $skipTitle = false;
            continue;
        }
        $dateIso = reskin_i18n_heading_date($section['body'], $base, $resolved['lang']);
        $items[] = [
            'title' => trim((string) preg_replace('/\s+/u', ' ', $section['title'])),
            'url' => reskin_update_link($resolved['id'], $base, $section['anchor'], reskin_variant_context()),
            'date_iso' => $dateIso, 'date' => reskin_i18n_format_update_date($dateIso, $preferred),
        ];
        if (count($items) >= $limit) break;
    }
    return $items;
}

/** Existing imported publication/outage date formats; no new interpretation. */
function reskin_i18n_heading_date(string $section, string $base, string $sourceLang): string
{
    $year = $month = $day = 0;
    if ($base === 'news:start') {
        if (!preg_match('/\b(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun)\s+(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s+(\d{1,2})\s+\d{2}:\d{2}:\d{2}\s+[A-Z]+\s+(\d{4})\b/', $section, $match)) return '';
        $months = ['Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12];
        $month = $months[$match[1]];
        $day = (int) $match[2];
        $year = (int) $match[3];
    } elseif ($base === 'outages:start' && $sourceLang === 'cs') {
        if (!preg_match('/\*\*Term[ií]n:\*\*[^\r\n]*?(\d{1,2})\.\s*(\d{1,2})\.\s*(\d{4})/ui', $section, $match)) return '';
        $day = (int) $match[1];
        $month = (int) $match[2];
        $year = (int) $match[3];
    } elseif ($base === 'outages:start') {
        if (!preg_match('/\*\*Window:\*\*\s*(?:[A-Za-z]{3}\s+)?(\d{4})-(\d{2})-(\d{2})/', $section, $match)) return '';
        $year = (int) $match[1];
        $month = (int) $match[2];
        $day = (int) $match[3];
    }
    return checkdate($month, $day, $year) ? sprintf('%04d-%02d-%02d', $year, $month, $day) : '';
}

function reskin_i18n_format_update_date(string $iso, string $lang): string
{
    if ($iso === '') return '';
    $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $iso);
    if (!$date) return '';
    if ($lang !== 'cs') return $date->format('j F Y');
    $months = [1 => 'ledna', 'února', 'března', 'dubna', 'května', 'června', 'července', 'srpna', 'září', 'října', 'listopadu', 'prosince'];
    return $date->format('j') . '. ' . $months[(int) $date->format('n')] . ' ' . $date->format('Y');
}
