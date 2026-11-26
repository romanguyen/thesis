<?php
if (!defined('DOKU_INC')) die();

/**
 * @return array<string, array<string, string>>
 */
function reskin_i18n_messages(): array
{
    return [
        'cs' => [
            'skip_to_content' => 'Přeskočit na obsah',
            'close' => 'Zavřít',
            'open_search' => 'Otevřít hledání',
            'close_search' => 'Zavřít hledání',
            'toggle_theme' => 'Přepnout motiv',
            'toggle_navigation' => 'Přepnout navigaci',
            'browse_sections' => 'Procházet sekce',
            'navigation' => 'Navigace',
            'more_navigation' => 'Více',
            'toc' => 'Obsah',
            'page_toc' => 'Na této stránce',
            'page_tools' => 'Nástroje stránky',
            'updates_block' => 'Novinky a odstávky',
            'updates_news_title' => 'Novinky',
            'updates_news_desc' => 'Poslední oznámení a události z MetaCentra.',
            'updates_outages_title' => 'Odstávky',
            'updates_outages_desc' => 'Plánovaná a aktuální provozní omezení.',
            'updates_empty' => 'Zatím nejsou dostupné žádné záznamy.',
            'updates_all_news' => 'Všechny novinky',
            'updates_all_outages' => 'Všechny odstávky',
            'updates_news_archive' => 'Archiv novinek',
            'updates_outages_archive' => 'Archiv odstávek',
            'news_article_read' => 'Číst článek',
            'quick_tiles_block' => 'Hlavní rozcestník',
            'quick_tiles_desc' => 'Ukázkové metriky prototypu, nikoli ověřený aktuální provozní stav.',
            'stories_block' => 'Akce a starší zprávy',
            'stories_desc' => 'Demonstrační karty s ukázkovými daty odkazují na archiv, nikoli na odpovídající zprávy o akcích.',
            'stories_all_news' => 'Archiv novinek',
            'stories_prev' => 'Předchozí sada',
            'stories_next' => 'Další sada',
            'stories_cta' => 'Zjistit více',
            'language_switcher' => 'Přepínač jazyka',
            'layout_switcher' => 'Přepínač rozložení a stylu',
            'preferences' => 'Předvolby',
            'menu' => 'Menu',
            'appearance' => 'Vzhled',
            'dark_theme' => 'Tmavý motiv',
            'preference_language' => 'Jazyk',
            'preference_layout' => 'Rozložení',
            'layout_left' => 'Vlevo',
            'layout_top' => 'Nahoře',
            'layout_left_hint' => 'Levé menu',
            'layout_top_hint' => 'Horní menu',
            'preference_style' => 'Styl',
            'style_cesnet' => 'CESNET',
            'style_metacentrum' => 'MetaCentrum',
            'style_bootstrap5' => 'Bootstrap 5',
            'style_cesnet_hint' => 'Vzhled CESNET',
            'style_metacentrum_hint' => 'Design MetaCentrum',
            'style_bootstrap5_hint' => 'Bootstrap 5 (ComboStrap)',
            'translation_missing' => 'Překlad není dostupný, zobrazuje se náhradní jazyk',
            'fallback_notice' => 'Požadovaný obsah není dostupný v jazyce %s. Zobrazuje se jazyk %s.',
            'quick_links' => 'Rychlé odkazy',
            'contact' => 'Kontakt',
            'user_support' => 'Uživatelská podpora',
            'footer_tagline' => 'Národní gridová infrastruktura provozovaná CESNETem pro českou akademickou komunitu.',
            'table_date' => 'Datum',
            'table_title' => 'Název',
        ],
        'en' => [
            'skip_to_content' => 'Skip to content',
            'close' => 'Close',
            'open_search' => 'Open search',
            'close_search' => 'Close search',
            'toggle_theme' => 'Toggle theme',
            'toggle_navigation' => 'Toggle navigation',
            'browse_sections' => 'Browse sections',
            'navigation' => 'Navigation',
            'more_navigation' => 'More',
            'toc' => 'Contents',
            'page_toc' => 'On this page',
            'page_tools' => 'Page tools',
            'updates_block' => 'News and outages',
            'updates_news_title' => 'News',
            'updates_news_desc' => 'Latest announcements and updates from MetaCentrum.',
            'updates_outages_title' => 'Outages',
            'updates_outages_desc' => 'Planned and active service interruptions.',
            'updates_empty' => 'No entries are available yet.',
            'updates_all_news' => 'All news',
            'updates_all_outages' => 'All outages',
            'updates_news_archive' => 'News archive',
            'updates_outages_archive' => 'Outages archive',
            'news_article_read' => 'Read article',
            'quick_tiles_block' => 'Main entry points',
            'quick_tiles_desc' => 'Prototype sample metrics, not a verified current operational snapshot.',
            'stories_block' => 'Events and older updates',
            'stories_desc' => 'Demonstration cards retain sample dates and link to the archive, not corresponding event reports.',
            'stories_all_news' => 'News archive',
            'stories_prev' => 'Previous set',
            'stories_next' => 'Next set',
            'stories_cta' => 'Find out more',
            'language_switcher' => 'Language switcher',
            'layout_switcher' => 'Layout and style switcher',
            'preferences' => 'Preferences',
            'menu' => 'Menu',
            'appearance' => 'Appearance',
            'dark_theme' => 'Dark theme',
            'preference_language' => 'Language',
            'preference_layout' => 'Layout',
            'layout_left' => 'Left',
            'layout_top' => 'Top',
            'layout_left_hint' => 'Left menu',
            'layout_top_hint' => 'Top menu',
            'preference_style' => 'Style',
            'style_cesnet' => 'CESNET',
            'style_metacentrum' => 'MetaCentrum',
            'style_bootstrap5' => 'Bootstrap 5',
            'style_cesnet_hint' => 'CESNET look',
            'style_metacentrum_hint' => 'MetaCentrum design',
            'style_bootstrap5_hint' => 'Bootstrap 5 (ComboStrap)',
            'translation_missing' => 'Translation is missing, showing fallback language',
            'fallback_notice' => 'Requested content is not available in %s. Showing %s instead.',
            'quick_links' => 'Quick Links',
            'contact' => 'Contact',
            'user_support' => 'User Support',
            'footer_tagline' => 'National Grid Infrastructure operated by CESNET for the Czech academic community.',
            'table_date' => 'Date',
            'table_title' => 'Title',
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
