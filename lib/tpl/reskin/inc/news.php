<?php
if (!defined('DOKU_INC')) die();

/**
 * News articles of one language, newest first: the readable pages directly in `<lang>:news:` except its start page.
 * An article is the page `news:YYYY-MM-DD-name` in each language, so the same name pairs the translations like any
 * other page, and the date sorts and displays without reading the source. The archive adds each card's text with
 * reskin_news_article_card(); the homepage rows need only these fields.
 * @return list<array{id:string,title:string,date_iso:string,date:string,url:string}>
 */
function reskin_news_articles(string $lang, array $variant, int $limit = 0): array
{
    global $conf;
    $found = [];
    search($found, $conf['datadir'], 'search_allpages', [], $lang . '/news'); // checks read access per page
    $articles = [];
    foreach ($found as $item) {
        if (getNS($item['id']) !== $lang . ':news' || noNS($item['id']) === $conf['start']) continue;
        $date = preg_match('/^(\d{4})-(\d{2})-(\d{2})-/', noNS($item['id']), $day) && checkdate((int) $day[2], (int) $day[3], (int) $day[1])
            ? "{$day[1]}-{$day[2]}-{$day[3]}" : '';
        $articles[] = [
            'id' => $item['id'],
            'title' => (string) (p_get_first_heading($item['id'], METADATA_RENDER_UNLIMITED) ?: noNS($item['id'])), // a list is short; a missing title must not depend on the per-request render limit
            'date_iso' => $date,
            'date' => reskin_i18n_format_card_date($date, $lang),
            'url' => wl($item['id'], reskin_url_variant_params($variant), false, '&'),
        ];
    }
    usort($articles, static fn(array $a, array $b): int => [$b['date_iso'], $b['id']] <=> [$a['date_iso'], $a['id']]);
    return $limit > 0 ? array_slice($articles, 0, $limit) : $articles;
}

/**
 * Archive-card text of one (already authorized) article, as on the MetaCentrum news archive: the article's wiki
 * source without its page title, which the card shows itself. A first line that is the imported byline
 * (`//Name, date//`) leaves the body and supplies the card's author; the date already comes from the page name.
 * The source stays unrendered here: the card partial renders it natively at its body slot.
 * @return array{body:string,author:string}
 */
function reskin_news_article_card(string $id): array
{
    $source = (string) preg_replace('/\A\s*={2,}[^\r\n]*={2,}[ \t]*(?:\r?\n|\z)/u', '', (string) rawWiki($id), 1);
    $author = '';
    if (preg_match('~\A\s*//([^/\r\n]+),[ \t]*([^/,\r\n]*\d[^/,\r\n]*)//[ \t]*(?:\r?\n|\z)~u', $source, $byline)) {
        $author = trim($byline[1]);
        $source = substr($source, strlen($byline[0]));
    }
    return ['body' => trim($source), 'author' => $author];
}

/** Archive cards per page of `news:start`. */
const RESKIN_NEWS_PAGE_SIZE = 10;

/**
 * Archive page links (`page` request parameter, 1-based; out-of-range values clamp to the nearest page).
 * @return array{current:int,total:int,pages:list<array{number:int,url:string,current:bool}>,prev:string,next:string}
 */
function reskin_news_paging(string $id, int $count, int $requested, array $variant): array
{
    $total = max(1, (int) ceil($count / RESKIN_NEWS_PAGE_SIZE));
    $current = min(max(1, $requested), $total);
    $url = static fn(int $number): string => wl($id, reskin_url_variant_params($variant, $number > 1 ? ['page' => $number] : []), false, '&');
    $pages = [];
    for ($number = 1; $number <= $total; $number++) $pages[] = ['number' => $number, 'url' => $url($number), 'current' => $number === $current];
    return [
        'current' => $current, 'total' => $total, 'pages' => $pages,
        'prev' => $current > 1 ? $url($current - 1) : '',
        'next' => $current < $total ? $url($current + 1) : '',
    ];
}

/** Homepage update rows: the newest news of the preferred language, else of the other one. */
function reskin_news_update_items(string $preferred, int $limit): array
{
    $articles = [];
    foreach (array_unique([$preferred, RESKIN_I18N_FALLBACK_LANG, RESKIN_I18N_PRIMARY_LANG]) as $lang) {
        $articles = reskin_news_articles($lang, reskin_variant_context(), $limit);
        if ($articles !== []) break;
    }
    return array_map(static fn(array $article): array => [
        'title' => $article['title'],
        'url' => $article['url'],
    ] + reskin_update_item_dates($article['date_iso'], '', $preferred), $articles);
}

/** Recent-update adapter: preserve source trimming, empty-title policy and display-language dates. */
function reskin_i18n_recent_headings(string $base, int $limit = 3, ?string $preferredLang = null): array
{
    $limit = max(1, $limit);
    $preferred = $preferredLang ?: reskin_i18n_current_lang();
    $resolved = reskin_resolve_page($base, $preferred);
    if (!$resolved['found'] || $resolved['id'] === '') return [];
    $sections = reskin_read_wiki_sections($resolved['id'], true, true);
    $items = [];
    $skipTitle = true;
    foreach ($sections as $section) {
        if ($skipTitle) {
            $skipTitle = false;
            continue;
        }
        $dates = reskin_i18n_heading_date_range($section['body'], $base, $resolved['lang']);
        $items[] = [
            'title' => trim((string) preg_replace('/\s+/u', ' ', $section['title'])),
            'url' => reskin_update_link($resolved['id'], $section['anchor'], reskin_variant_context()),
        ] + reskin_update_item_dates($dates['start'], $dates['end'], $preferred);
        if (count($items) >= $limit) break;
    }
    return $items;
}

/** Existing imported outage date formats; start date only. */
function reskin_i18n_heading_date(string $section, string $base, string $sourceLang): string
{
    return reskin_i18n_heading_date_range($section, $base, $sourceLang)['start'];
}

/**
 * Outage start plus an optional explicit end date; other pages have none.
 *
 * Outage ranges are recognized only directly after the field label:
 * English `2026-04-28 08:00 – 2026-05-02 16:00` (or `to`), Czech
 * `28. 4. 2026 08:00 – 2. 5. 2026`, `9.–12. 10. 2026` and `26. 6.–29. 6. 2026`.
 * A time-only range (`07:00–11:00`) is a single-day window. An invalid end,
 * or one not after the start, is dropped; the start is kept.
 *
 * @return array{start:string,end:string}
 */
function reskin_i18n_heading_date_range(string $section, string $base, string $sourceLang): array
{
    $none = ['start' => '', 'end' => ''];
    $iso = static function (int $year, int $month, int $day): string {
        return checkdate($month, $day, $year) ? sprintf('%04d-%02d-%02d', $year, $month, $day) : '';
    };
    $range = static function (string $start, string $end): array {
        if ($start === '') return ['start' => '', 'end' => ''];
        return ['start' => $start, 'end' => ($end !== '' && $end > $start) ? $end : ''];
    };
    $time = '(?:,?\s*\d{1,2}:\d{2})?';
    $dash = '\s*(?:[-–—]|to)\s*';

    if ($base !== 'outages:start') return $none;

    if ($sourceLang === 'cs') {
        // Start month/year may be omitted when shared with the end date.
        if (preg_match('/\*\*Term[ií]n:\*\*[^\r\n\d]*(\d{1,2})\.?(?:\s*(\d{1,2})\.)?(?:\s*(\d{4}))?' . $time . $dash . '(\d{1,2})\.\s*(\d{1,2})\.\s*(\d{4})/u', $section, $match)) {
            $endYear = (int) $match[6];
            $endMonth = (int) $match[5];
            $startMonth = $match[2] !== '' ? (int) $match[2] : $endMonth;
            $startYear = $match[3] !== '' ? (int) $match[3] : $endYear;
            return $range($iso($startYear, $startMonth, (int) $match[1]), $iso($endYear, $endMonth, (int) $match[4]));
        }
        if (!preg_match('/\*\*Term[ií]n:\*\*[^\r\n]*?(\d{1,2})\.\s*(\d{1,2})\.\s*(\d{4})/ui', $section, $match)) return $none;
        return $range($iso((int) $match[3], (int) $match[2], (int) $match[1]), '');
    }

    $day = '(?:[A-Za-z]{3}\s+)?(\d{4})-(\d{2})-(\d{2})';
    if (!preg_match('/\*\*Window:\*\*\s*' . $day . '(?:' . $time . $dash . $day . ')?/u', $section, $match)) return $none;
    $end = isset($match[4]) && $match[4] !== '' ? $iso((int) $match[4], (int) $match[5], (int) $match[6]) : '';
    return $range($iso((int) $match[1], (int) $match[2], (int) $match[3]), $end);
}

/**
 * Update-card row date fields, numeric (see reskin_i18n_format_update_range).
 * Range rows add `date_range_start`, `end_date` and the dash between them.
 *
 * @return array{date:string,date_iso:string,end_date:string,end_date_iso:string,date_range_start:string,date_range_separator:string}
 */
function reskin_update_item_dates(string $startIso, string $endIso, string $lang): array
{
    $range = reskin_i18n_format_update_range($startIso, $endIso, $lang);
    $hasEnd = $range['end'] !== '';
    return [
        'date' => reskin_i18n_format_card_date($startIso, $lang),
        'date_iso' => $startIso,
        'end_date' => $range['end'],
        'end_date_iso' => $hasEnd ? $endIso : '',
        'date_range_start' => $hasEnd ? $range['start'] : '',
        'date_range_separator' => $range['separator'],
    ];
}

/** The template's one date format: ISO `2026-04-14` in English, `14. 4. 2026` in Czech. */
function reskin_i18n_format_card_date(string $iso, string $lang): string
{
    if ($iso === '') return '';
    $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $iso);
    if (!$date) return '';
    return $lang === 'cs' ? $date->format('j. n. Y') : $date->format('Y-m-d');
}

/**
 * Numeric card range labels. Czech shares trailing parts with the end:
 * `16.–17. 4. 2026`, `28. 4. – 2. 5. 2026`; English stays full ISO:
 * `2026-04-28 – 2026-05-02`. Without a valid later end, only `start` is set.
 *
 * @return array{start:string,end:string,separator:string}
 */
function reskin_i18n_format_update_range(string $startIso, string $endIso, string $lang): array
{
    $full = reskin_i18n_format_card_date($startIso, $lang);
    $start = \DateTimeImmutable::createFromFormat('!Y-m-d', $startIso);
    $end = \DateTimeImmutable::createFromFormat('!Y-m-d', $endIso);
    if ($full === '' || !$start || !$end || $end <= $start) return ['start' => $full, 'end' => '', 'separator' => ''];

    $endLabel = reskin_i18n_format_card_date($endIso, $lang);
    if ($lang !== 'cs') return ['start' => $full, 'end' => $endLabel, 'separator' => ' – '];
    if ($start->format('Y-n') === $end->format('Y-n')) return ['start' => $start->format('j.'), 'end' => $endLabel, 'separator' => '–'];
    $label = $start->format('Y') === $end->format('Y') ? $start->format('j. n.') : $full;
    return ['start' => $label, 'end' => $endLabel, 'separator' => ' – '];
}
