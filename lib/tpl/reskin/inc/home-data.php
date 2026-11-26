<?php
if (!defined('DOKU_INC')) die();

/**
 * @param string|null $lang
 * @return string
 */
function reskin_i18n_home_lang(?string $lang = null): string
{
    $lang = $lang ?: reskin_i18n_current_lang();
    if (!isset(RESKIN_I18N_LANGS[$lang])) $lang = RESKIN_I18N_FALLBACK_LANG;
    return $lang;
}

/**
 * @param string $target
 * @return string
 */
function reskin_i18n_home_link(string $target): string
{
    $target = trim($target);
    if ($target === '') return '#';

    if (preg_match('/[\x00-\x20\x7f]/', $target) && preg_match('/^[a-z][a-z0-9+.-]*:/i', $target)) return '#';
    if (preg_match('/^https?:\/\//i', $target) || str_starts_with($target, '/')) {
        return $target;
    }
    if (preg_match('/^(?:javascript|vbscript|data|file|blob):/i', $target) || str_contains($target, '://')) return '#';

    return reskin_i18n_link($target);
}

/**
 * Read "|" separated rows from a localized fragment page
 *
 * @param string $base
 * @param int $columns
 * @param string|null $preferredLang
 * @return array<int, array<int, string>>
 */
function reskin_i18n_home_fragment_rows(string $base, int $columns, ?string $preferredLang = null): array
{
    $columns = max(1, $columns);
    $preferred = reskin_i18n_home_lang($preferredLang);
    $resolved = reskin_i18n_resolve_exact($base, $preferred);

    if (!$resolved['found'] || $resolved['id'] === '') return [];

    static $cache = [];
    $cacheKey = $resolved['id'] . '|' . $columns;
    if (isset($cache[$cacheKey])) return $cache[$cacheKey];

    $cache[$cacheKey] = reskin_parse_home_fragment_rows((string) rawWiki($resolved['id']), $columns);
    return $cache[$cacheKey];
}

/**
 * Parse explicit fragment source; page resolution, ACL and request-local reuse stay in the reader.
 * The final column retains additional pipes, and every required column must be non-empty.
 * @return list<list<string>>
 */
function reskin_parse_home_fragment_rows(string $source, int $columns): array
{
    $columns = max(1, $columns);
    if (trim($source) === '') return [];

    $rows = [];
    $lines = preg_split('/\R/u', $source) ?: [];
    foreach ($lines as $line) {
        $line = trim((string) $line);
        if ($line === '') continue;
        if (str_starts_with($line, '#') || str_starts_with($line, '//')) continue;

        if (!preg_match('/^\s*[*\-]\s*(.+)$/u', $line, $match)) continue;

        $line = trim((string) ($match[1] ?? ''));
        if ($line === '' || !str_contains($line, '|')) continue;

        $parts = array_map('trim', explode('|', $line, $columns));
        if (count($parts) !== $columns) continue;

        $isValid = true;
        foreach ($parts as $part) {
            if ($part === '') {
                $isValid = false;
                break;
            }
        }
        if (!$isValid) continue;

        $rows[] = $parts;
    }

    return $rows;
}

/**
 * @param string $lang
 * @return array<int, array{title:string,url:string,icon:string}>
 */
function reskin_i18n_default_start_metric_cards(string $lang): array
{
    $language = $lang === 'cs' ? 'cs' : 'en';
    $definitions = [
        ['title' => ['cs' => 'Superpočítače', 'en' => 'Supercomputers'], 'target' => 'resources:hardware:start', 'icon' => 'bi-diagram-3'],
        ['title' => ['cs' => 'Výzkum', 'en' => 'Research'], 'target' => 'projekty:start', 'icon' => 'bi-search'],
        ['title' => ['cs' => 'Spolupráce s průmyslem', 'en' => 'Industry cooperation'], 'target' => 'sluzby:start', 'icon' => 'bi-gear-wide-connected'],
        ['title' => ['cs' => 'Pro uživatele', 'en' => 'For users'], 'target' => 'vo:start', 'icon' => 'bi-people'],
        ['title' => ['cs' => 'Právě hledáme', 'en' => 'Open positions'], 'target' => 'about:start', 'icon' => 'bi-briefcase'],
    ];
    $cards = [];
    foreach ($definitions as $definition) {
        $cards[] = [
            'title' => $definition['title'][$language],
            'url' => reskin_i18n_link($definition['target']),
            'icon' => $definition['icon'],
        ];
    }
    return $cards;
}

/**
 * @param string $lang
 * @return array<int, array{number:string,label:string}>
 */
function reskin_i18n_default_start_metric_highlights(string $lang): array
{
    return [
        ['number' => $lang === 'cs' ? "2\u{00A0}000+" : '2,000+', 'label' => $lang === 'cs' ? 'uživatelů' : 'users'],
        ['number' => '25+', 'label' => $lang === 'cs' ? 'mezinárodních projektů' : 'international projects'],
        ['number' => '100+', 'label' => $lang === 'cs' ? 'projektů pro průmysl' : 'industry projects'],
    ];
}

/**
 * @param string $lang
 * @return array<int, array{date:string,date_iso:string,year:string,title:string,url:string,image:string}>
 */
function reskin_i18n_default_start_story_cards(string $lang): array
{
    $language = $lang === 'cs' ? 'cs' : 'en';
    $definitions = [
        [
            'date' => '08/04',
            'title' => ['cs' => 'Workshop AI Confidential', 'en' => 'Workshop AI Confidential'],
            'image' => 'lib/tpl/reskin/img/stories/ai.svg',
        ],
        [
            'date' => '09/04',
            'title' => ['cs' => 'AdvanceMed 2026', 'en' => 'AdvanceMed 2026'],
            'image' => 'lib/tpl/reskin/img/stories/medicine.svg',
        ],
        [
            'date' => '28/04',
            'title' => ['cs' => 'Superpočítače a kvantový počítač VLQ zblízka', 'en' => 'Supercomputers and VLQ quantum computing excursion'],
            'image' => 'lib/tpl/reskin/img/stories/quantum.svg',
        ],
        [
            'date' => '05/05',
            'title' => ['cs' => 'Školení: Jak připravit GPU úlohy', 'en' => 'Training: how to run GPU workloads'],
            'image' => 'lib/tpl/reskin/img/stories/gpu.svg',
        ],
        [
            'date' => '13/05',
            'title' => ['cs' => 'Open Access Day pro výzkumníky', 'en' => 'Open Access Day for researchers'],
            'image' => 'lib/tpl/reskin/img/stories/open-access.svg',
        ],
        [
            'date' => '26/05',
            'title' => ['cs' => 'MetaCentrum community meetup', 'en' => 'MetaCentrum community meetup'],
            'image' => 'lib/tpl/reskin/img/stories/community.svg',
        ],
    ];
    $cards = [];
    foreach ($definitions as $definition) {
        $cards[] = [
            'year' => '2026',
            'title' => $definition['title'][$language],
            'url' => reskin_i18n_link('news:start'),
            'image' => reskin_home_story_image_url($definition['image']),
        ] + reskin_home_story_date($definition['date'], '2026', $language);
    }
    return $cards;
}

/**
 * @param string|null $lang
 * @return array<int, array{title:string,url:string,icon:string}>
 */
function reskin_i18n_start_metric_cards(?string $lang = null): array
{
    $lang = reskin_i18n_home_lang($lang);

    $rows = reskin_i18n_home_fragment_rows('fragments:home_metric_cards', 3, $lang);
    $cards = [];
    foreach ($rows as $row) {
        $cards[] = [
            'title' => $row[0],
            'url' => reskin_i18n_home_link($row[1]),
            'icon' => $row[2],
        ];
    }

    if (!empty($cards)) return $cards;
    return reskin_i18n_default_start_metric_cards($lang);
}

/**
 * @param string|null $lang
 * @return array<int, array{number:string,label:string}>
 */
function reskin_i18n_start_metric_highlights(?string $lang = null): array
{
    $lang = reskin_i18n_home_lang($lang);

    $rows = reskin_i18n_home_fragment_rows('fragments:home_metric_highlights', 2, $lang);
    $items = [];
    foreach ($rows as $row) {
        $items[] = [
            'number' => $row[0],
            'label' => $row[1],
        ];
    }

    if (!empty($items)) return $items;
    return reskin_i18n_default_start_metric_highlights($lang);
}

/**
 * Story row date (`DD/MM` and `YYYY`) in the template's date format; an unparsable value stays as written.
 * @return array{date:string,date_iso:string}
 */
function reskin_home_story_date(string $dayMonth, string $year, string $lang): array
{
    $iso = preg_match('~^(\d{1,2})/(\d{1,2})$~', trim($dayMonth), $m) && preg_match('/^\d{4}$/', trim($year)) && checkdate((int) $m[2], (int) $m[1], (int) $year)
        ? sprintf('%04d-%02d-%02d', (int) $year, (int) $m[2], (int) $m[1]) : '';
    return ['date' => $iso !== '' ? reskin_i18n_format_card_date($iso, $lang) : trim($dayMonth . '/' . $year, '/'), 'date_iso' => $iso];
}

/** Installation-base boundary for project-relative illustration paths; external URLs remain untouched. */
function reskin_home_story_image_url(string $image): string
{
    return str_starts_with($image, 'lib/tpl/reskin/img/') ? DOKU_BASE . $image : $image;
}

/**
 * @param string|null $lang
 * @return array<int, array{date:string,date_iso:string,year:string,title:string,url:string,image:string}>
 */
function reskin_i18n_start_story_cards(?string $lang = null): array
{
    $lang = reskin_i18n_home_lang($lang);

    $rows = reskin_i18n_home_fragment_rows('fragments:home_stories', 5, $lang);
    $cards = [];
    foreach ($rows as $row) {
        $cards[] = [
            'year' => $row[1],
            'title' => $row[2],
            'url' => reskin_i18n_home_link($row[3]),
            'image' => reskin_home_story_image_url($row[4]),
        ] + reskin_home_story_date($row[0], $row[1], $lang);
    }

    if (!empty($cards)) return $cards;
    return reskin_i18n_default_start_story_cards($lang);
}

/**
 * Existing demonstration datasets, shared by showcase and empty inline feeds.
 * @param 'news'|'outages' $kind
 * @return array<int,array{title:string,url:string,date:string,date_iso:string}>
 */
function reskin_home_default_update_items(string $kind, string $lang): array
{
    if ($kind === 'news') {
        $base = 'news:start';
        $rows = $lang === 'cs' ? [
            ['EGI.eu vyhlašuje výběrové řízení na nového ředitele', '2013-11-14'],
            ['EGI Inspired Newsletter – Léto 2012', '2012-09-06'],
            ['EGI Inspired Newsletter – Jaro 2012', '2012-05-24'],
        ] : [
            ['EGI.eu is now seeking to employ a Director', '2013-11-14'],
            ['EGI Inspired Newsletter - Summer 2012', '2012-09-06'],
            ['EGI Inspired Newsletter - Spring 2012', '2012-05-24'],
        ];
    } else {
        $base = 'outages:start';
        $rows = $lang === 'cs' ? [
            ['Obnova provozu S3 úložiště CL4', '2026-10-09', '2026-10-12'],
            ['Omezená dostupnost služeb (DDoS útok)', '2026-10-07'],
            ['Kde sledovat aktuální stav', ''],
        ] : [
            ['Recovery of CL4 S3 Storage Service', '2026-10-09', '2026-10-12'],
            ['Limited Service Availability (DDoS Attack)', '2026-10-07'],
            ['Where to follow current incidents', ''],
        ];
    }
    $items = [];
    foreach ($rows as $row) {
        $items[] = [
            'title' => $row[0],
            'url' => reskin_i18n_link($base),
        ] + reskin_update_item_dates($row[1], $row[2] ?? '', $lang);
    }
    return $items;
}

/**
 * Prepare both update cards with their current distinct content policies.
 *
 * @param bool $readRecent Homepage paths read live headings; the standalone showcase does not.
 * @param bool $allowDemo Empty inline feeds use demos; full-width homepage feeds stay empty.
 * @return array{label:string,cards:array<int,array{title:string,icon:string,archive_url:string,archive_label:string,empty_label:string,items:array}>}
 */
function reskin_home_updates_model(string $lang, bool $readRecent, bool $allowDemo): array
{
    $items = $readRecent ? [
        'news' => reskin_news_update_items($lang, 3),
        'outages' => reskin_i18n_recent_headings('outages:start', 3, $lang),
    ] : ['news' => [], 'outages' => []];
    $groups = [
        'news' => ['base' => 'news:start', 'icon' => 'bi-journal-text'],
        'outages' => ['base' => 'outages:start', 'icon' => 'bi-exclamation-triangle'],
    ];
    $cards = [];
    foreach ($groups as $kind => $group) {
        if ($items[$kind] === [] && $allowDemo) {
            $items[$kind] = reskin_home_default_update_items($kind, $lang);
        }
        $cards[] = [
            'title' => reskin_i18n_t('updates_' . $kind . '_title', [], $lang),
            'icon' => $group['icon'],
            'archive_url' => reskin_i18n_link($group['base']),
            'archive_label' => reskin_i18n_t('updates_' . $kind . '_archive', [], $lang),
            'empty_label' => reskin_i18n_t('updates_empty', [], $lang),
            'items' => array_map(static function (array $item): array {
                return $item + ['date' => '', 'date_iso' => '', 'end_date' => '', 'end_date_iso' => '', 'date_range_start' => '', 'date_range_separator' => ''];
            }, $items[$kind]),
        ];
    }
    return ['label' => reskin_i18n_t('updates_block', [], $lang), 'cards' => $cards];
}

/**
 * Prepare data once for the shared metrics and stories presentations.
 * @return array{metrics:array{title:string,description:string,cards:array,highlights:array},stories:array{title:string,description:string,archive_url:string,archive_label:string,cta_label:string,prev_label:string,next_label:string,cards:array}}
 */
function reskin_home_feature_models(string $lang): array
{
    $cards = reskin_i18n_start_metric_cards($lang);
    $highlights = reskin_i18n_start_metric_highlights($lang);
    $stories = reskin_i18n_start_story_cards($lang);
    return [
        'metrics' => [
            'title' => reskin_i18n_t('quick_tiles_block', [], $lang),
            'description' => reskin_i18n_t('quick_tiles_desc', [], $lang),
            'cards' => $cards,
            'highlights' => $highlights,
        ],
        'stories' => [
            'title' => reskin_i18n_t('stories_block', [], $lang),
            'description' => reskin_i18n_t('stories_desc', [], $lang),
            'archive_url' => reskin_i18n_link('news:start'),
            'archive_label' => reskin_i18n_t('stories_all_news', [], $lang),
            'cta_label' => reskin_i18n_t('stories_cta', [], $lang),
            'prev_label' => reskin_i18n_t('stories_prev', [], $lang),
            'next_label' => reskin_i18n_t('stories_next', [], $lang),
            'cards' => $stories,
        ],
    ];
}
