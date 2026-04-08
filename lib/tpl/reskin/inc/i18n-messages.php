<?php
if (!defined('DOKU_INC')) die();

/**
 * @return array<string, array<string, string>>
 */
function reskin_i18n_messages(): array
{
    return [
        'cs' => [
            'skip_to_content' => 'Preskocit na obsah',
            'menu' => 'Menu',
            'close' => 'Zavrit',
            'open_search' => 'Otevrit hledani',
            'close_search' => 'Zavrit hledani',
            'toggle_theme' => 'Prepnout motiv',
            'toggle_navigation' => 'Prepnout navigaci',
            'browse_sections' => 'Prochazet sekce',
            'navigation' => 'Navigace',
            'toc' => 'Obsah',
            'updates_block' => 'Novinky a odstavky',
            'updates_news_title' => 'Novinky',
            'updates_news_desc' => 'Posledni oznameni a udalosti z MetaCentra.',
            'updates_outages_title' => 'Odstavky',
            'updates_outages_desc' => 'Planovane a aktualni provozni omezeni.',
            'updates_empty' => 'Zatim nejsou dostupne zadne zaznamy.',
            'updates_all_news' => 'Vsechny novinky',
            'updates_all_outages' => 'Vsechny odstavky',
            'quick_tiles_block' => 'Hlavni rozcestnik',
            'quick_tiles_desc' => 'Ikony s odkazy na klicove oblasti + hexagonalni metriky.',
            'stories_block' => 'Akce a starsi zpravy',
            'stories_desc' => 'Vetsi karty ve slideru, sipkami lze prejit na starsi sady.',
            'stories_all_news' => 'Archiv novinek',
            'stories_prev' => 'Predchozi sada',
            'stories_next' => 'Dalsi sada',
            'stories_cta' => 'Zjistit vice',
            'language_switcher' => 'Prepinac jazyka',
            'translation_missing' => 'Preklad neni dostupny, zobrazuje se nahradni jazyk',
            'fallback_notice' => 'Pozadovana stranka neni dostupna v jazyce %s. Zobrazuje se jazyk %s.',
            'quick_links' => 'Rychle odkazy',
            'contact' => 'Kontakt',
            'user_support' => 'Uzivatelska podpora',
            'footer_tagline' => 'Narodni Grid infrastruktura provozovana CESNETem pro ceskou akademickou komunitu.',
            'last_changed' => 'Posledni zmena',
        ],
        'en' => [
            'skip_to_content' => 'Skip to content',
            'menu' => 'Menu',
            'close' => 'Close',
            'open_search' => 'Open search',
            'close_search' => 'Close search',
            'toggle_theme' => 'Toggle theme',
            'toggle_navigation' => 'Toggle navigation',
            'browse_sections' => 'Browse sections',
            'navigation' => 'Navigation',
            'toc' => 'Contents',
            'updates_block' => 'News and outages',
            'updates_news_title' => 'News',
            'updates_news_desc' => 'Latest announcements and updates from MetaCentrum.',
            'updates_outages_title' => 'Outages',
            'updates_outages_desc' => 'Planned and active service interruptions.',
            'updates_empty' => 'No entries are available yet.',
            'updates_all_news' => 'All news',
            'updates_all_outages' => 'All outages',
            'quick_tiles_block' => 'Main entry points',
            'quick_tiles_desc' => 'Icon links to key areas plus hex-style highlight metrics.',
            'stories_block' => 'Events and older updates',
            'stories_desc' => 'Large cards in a slider, with arrows for older sets.',
            'stories_all_news' => 'News archive',
            'stories_prev' => 'Previous set',
            'stories_next' => 'Next set',
            'stories_cta' => 'Find out more',
            'language_switcher' => 'Language switcher',
            'translation_missing' => 'Translation is missing, showing fallback language',
            'fallback_notice' => 'Requested page is not available in %s. Showing %s instead.',
            'quick_links' => 'Quick Links',
            'contact' => 'Contact',
            'user_support' => 'User Support',
            'footer_tagline' => 'National Grid Infrastructure operated by CESNET for the Czech academic community.',
            'last_changed' => 'Last changed',
        ],
    ];
}

/**
 * @param string $key
 * @param array $vars
 * @param string|null $lang
 * @return string
 */
function reskin_i18n_t(string $key, array $vars = [], ?string $lang = null): string
{
    $lang = $lang ?: reskin_i18n_current_lang();
    if (!isset(RESKIN_I18N_LANGS[$lang])) $lang = RESKIN_I18N_FALLBACK_LANG;

    $messages = reskin_i18n_messages();

    $message = $messages[$lang][$key] ?? ($messages[RESKIN_I18N_FALLBACK_LANG][$key] ?? $key);
    if (!empty($vars)) {
        $message = vsprintf($message, $vars);
    }

    return $message;
}
