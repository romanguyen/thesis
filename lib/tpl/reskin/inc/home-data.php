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
