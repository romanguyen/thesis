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

    if (preg_match('/^[a-z][a-z0-9+.-]*:\/\//i', $target) || str_starts_with($target, '/')) {
        return $target;
    }

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
    $resolved = reskin_i18n_resolve_exact($base, $preferred, true, true);

    if (!$resolved['found'] || $resolved['id'] === '') return [];

    static $cache = [];
    $cacheKey = $resolved['id'] . '|' . $columns;
    if (isset($cache[$cacheKey])) return $cache[$cacheKey];

    $raw = (string) rawWiki($resolved['id']);
    if (trim($raw) === '') {
        $cache[$cacheKey] = [];
        return [];
    }

    $rows = [];
    $lines = preg_split('/\R/u', $raw) ?: [];
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

    $cache[$cacheKey] = $rows;
    return $rows;
}

/**
 * @param string $lang
 * @return array<int, array{title:string,url:string,icon:string}>
 */
function reskin_i18n_default_start_metric_cards(string $lang): array
{
    if ($lang === 'cs') {
        return [
            [
                'title' => 'Superpocitace',
                'url' => reskin_i18n_link('resources:hardware:start'),
                'icon' => 'bi-diagram-3',
            ],
            [
                'title' => 'Vyzkum',
                'url' => reskin_i18n_link('projekty:start'),
                'icon' => 'bi-search',
            ],
            [
                'title' => 'Spoluprace s prumyslem',
                'url' => reskin_i18n_link('sluzby:start'),
                'icon' => 'bi-gear-wide-connected',
            ],
            [
                'title' => 'Pro uzivatele',
                'url' => reskin_i18n_link('vo:start'),
                'icon' => 'bi-people-fill',
            ],
            [
                'title' => 'Prave hledame',
                'url' => reskin_i18n_link('about:start'),
                'icon' => 'bi-briefcase-fill',
            ],
        ];
    }

    return [
        [
            'title' => 'Supercomputers',
            'url' => reskin_i18n_link('resources:hardware:start'),
            'icon' => 'bi-diagram-3',
        ],
        [
            'title' => 'Research',
            'url' => reskin_i18n_link('projekty:start'),
            'icon' => 'bi-search',
        ],
        [
            'title' => 'Industry cooperation',
            'url' => reskin_i18n_link('sluzby:start'),
            'icon' => 'bi-gear-wide-connected',
        ],
        [
            'title' => 'For users',
            'url' => reskin_i18n_link('vo:start'),
            'icon' => 'bi-people-fill',
        ],
        [
            'title' => 'Open positions',
            'url' => reskin_i18n_link('about:start'),
            'icon' => 'bi-briefcase-fill',
        ],
    ];
}

/**
 * @param string $lang
 * @return array<int, array{number:string,label:string}>
 */
function reskin_i18n_default_start_metric_highlights(string $lang): array
{
    if ($lang === 'cs') {
        return [
            ['number' => '2 000+', 'label' => 'uzivatelu'],
            ['number' => '25+', 'label' => 'mezinarodnich projektu'],
            ['number' => '100+', 'label' => 'projektu pro prumysl'],
        ];
    }

    return [
        ['number' => '2,000+', 'label' => 'users'],
        ['number' => '25+', 'label' => 'international projects'],
        ['number' => '100+', 'label' => 'industry projects'],
    ];
}

/**
 * @param string $lang
 * @return array<int, array{date:string,year:string,title:string,url:string,image:string}>
 */
function reskin_i18n_default_start_story_cards(string $lang): array
{
    if ($lang === 'cs') {
        return [
            [
                'date' => '08/04',
                'year' => '2026',
                'title' => 'Workshop AI Confidential',
                'url' => reskin_i18n_link('news:start'),
                'image' => 'https://www.metacentrum.cz/export/sites/metacentrum/images/EGI_history.png',
            ],
            [
                'date' => '09/04',
                'year' => '2026',
                'title' => 'AdvanceMed 2026',
                'url' => reskin_i18n_link('news:start'),
                'image' => 'https://www.metacentrum.cz/export/sites/metacentrum/images/ceritsc.png_1995693183.png',
            ],
            [
                'date' => '28/04',
                'year' => '2026',
                'title' => 'Superpocitace a kvantovy pocitac VLQ zblizka',
                'url' => reskin_i18n_link('news:start'),
                'image' => 'https://www.metacentrum.cz/export/sites/metacentrum/cs/devel/PBSMon/pbsmon1.png',
            ],
            [
                'date' => '05/05',
                'year' => '2026',
                'title' => 'Skoleni: Jak pripravit GPU ulohy',
                'url' => reskin_i18n_link('news:start'),
                'image' => 'https://www.metacentrum.cz/export/sites/metacentrum/images/mapkaMC_2016_oranz.png_20180039.png',
            ],
            [
                'date' => '13/05',
                'year' => '2026',
                'title' => 'Open Access Day pro vyzkumniky',
                'url' => reskin_i18n_link('news:start'),
                'image' => 'https://www.cesnet.cz/wp-content/uploads/2022/10/Foto-1035-1-scaled.jpg',
            ],
            [
                'date' => '26/05',
                'year' => '2026',
                'title' => 'MetaCentrum community meetup',
                'url' => reskin_i18n_link('news:start'),
                'image' => 'https://www.metacentrum.cz/export/sites/metacentrum/en/devel/plan-based-scheduler/screenshot3.png',
            ],
        ];
    }

    return [
        [
            'date' => '08/04',
            'year' => '2026',
            'title' => 'Workshop AI Confidential',
            'url' => reskin_i18n_link('news:start'),
            'image' => 'https://www.metacentrum.cz/export/sites/metacentrum/images/EGI_history.png',
        ],
        [
            'date' => '09/04',
            'year' => '2026',
            'title' => 'AdvanceMed 2026',
            'url' => reskin_i18n_link('news:start'),
            'image' => 'https://www.metacentrum.cz/export/sites/metacentrum/images/ceritsc.png_1995693183.png',
        ],
        [
            'date' => '28/04',
            'year' => '2026',
            'title' => 'Supercomputers and VLQ quantum computing excursion',
            'url' => reskin_i18n_link('news:start'),
            'image' => 'https://www.metacentrum.cz/export/sites/metacentrum/cs/devel/PBSMon/pbsmon1.png',
        ],
        [
            'date' => '05/05',
            'year' => '2026',
            'title' => 'Training: how to run GPU workloads',
            'url' => reskin_i18n_link('news:start'),
            'image' => 'https://www.metacentrum.cz/export/sites/metacentrum/images/mapkaMC_2016_oranz.png_20180039.png',
        ],
        [
            'date' => '13/05',
            'year' => '2026',
            'title' => 'Open Access Day for researchers',
            'url' => reskin_i18n_link('news:start'),
            'image' => 'https://www.cesnet.cz/wp-content/uploads/2022/10/Foto-1035-1-scaled.jpg',
        ],
        [
            'date' => '26/05',
            'year' => '2026',
            'title' => 'MetaCentrum community meetup',
            'url' => reskin_i18n_link('news:start'),
            'image' => 'https://www.metacentrum.cz/export/sites/metacentrum/en/devel/plan-based-scheduler/screenshot3.png',
        ],
    ];
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
 * @param string|null $lang
 * @return array<int, array{date:string,year:string,title:string,url:string,image:string}>
 */
function reskin_i18n_start_story_cards(?string $lang = null): array
{
    $lang = reskin_i18n_home_lang($lang);

    $rows = reskin_i18n_home_fragment_rows('fragments:home_stories', 5, $lang);
    $cards = [];
    foreach ($rows as $row) {
        $cards[] = [
            'date' => $row[0],
            'year' => $row[1],
            'title' => $row[2],
            'url' => reskin_i18n_home_link($row[3]),
            'image' => $row[4],
        ];
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
            ['Plánovaná odstávka: frontendy a scheduler', '2026-04-14'],
            ['Údržba úložišť: metadata scratch', '2026-04-16'],
            ['Kde sledovat aktuální stav', ''],
        ] : [
            ['Planned outage: frontends and scheduler', '2026-04-14'],
            ['Storage maintenance: metadata scratch', '2026-04-16'],
            ['Where to track current status', ''],
        ];
    }
    $items = [];
    foreach ($rows as [$title, $dateIso]) {
        $items[] = [
            'title' => $title,
            'url' => reskin_i18n_link($base),
            'date_iso' => $dateIso,
            'date' => reskin_i18n_format_update_date($dateIso, $lang),
        ];
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
        'news' => reskin_i18n_recent_headings('news:start', 3, $lang),
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
                return $item + ['date' => '', 'date_iso' => ''];
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
