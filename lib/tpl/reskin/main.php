<?php
/**
 * Reskin template for DokuWiki
 */

if (!defined('DOKU_INC')) die();

global $ID, $ACT, $conf, $lang, $INPUT;

require_once __DIR__ . '/i18n.php';
reskin_i18n_handle_request();

$reskinParsedId = reskin_i18n_parse_id();
$reskinCurrentLang = $reskinParsedId['lang'];
$reskinFallbackNotice = reskin_i18n_fallback_notice();
$reskinVariant = reskin_variant_context();
$reskinLayout = $reskinVariant['layout'];
$reskinStyle = $reskinVariant['style'];

$hasSidebar = reskin_i18n_resolve_fragment($conf['sidebar'])['found'];
$showSidebar = $hasSidebar && ($ACT === 'show');
$showSidebarAside = $showSidebar && ($reskinLayout === 'left');
$showTopNavigation = $showSidebar && ($reskinLayout === 'top');
$showSidebarMobileTrigger = $showSidebar && in_array($reskinLayout, ['left', 'top'], true);
$showSidebarOffcanvas = $showSidebarMobileTrigger;
$isStart = ($reskinParsedId['base'] === $conf['start']);
$isNewsShowcasePage = ($reskinParsedId['base'] === 'variants:news');
$isNewsArchivePage = ($reskinParsedId['base'] === 'news:start');
$reskinNewsArticle = null;
$reskinNewsEntries = [];
if ($ACT === 'show' && $isNewsArchivePage && page_exists($ID) && auth_quickaclcheck($ID) >= AUTH_READ) {
    $reskinNewsEntries = reskin_i18n_news_articles($ID, $reskinCurrentLang);
    $reskinRequestedArticle = $INPUT->str('article');
    if ($reskinRequestedArticle !== '' && strlen($reskinRequestedArticle) <= 200) {
        foreach ($reskinNewsEntries as $reskinEntry) {
            if ($reskinEntry['anchor'] === $reskinRequestedArticle) {
                $reskinNewsArticle = $reskinEntry;
                break;
            }
        }
    }
}
$isElementsShowcasePage = ($reskinParsedId['base'] === 'variants:it4i-elements');
$isStartLeftInline = ($ACT === 'show') && $isStart && ($reskinLayout === 'left');
$showHeroSection = $isStart && ($ACT === 'show');
$showUpdatesSection = ($ACT === 'show') && $isStart && !$isStartLeftInline;
$showUpdatesInline = ($ACT === 'show') && ($isNewsShowcasePage || $isStartLeftInline);
$showMetricsSection = ($ACT === 'show') && $isStart && !$isStartLeftInline;
$showStoriesSection = ($ACT === 'show') && $isStart && !$isStartLeftInline;
$showMetricsInline = ($ACT === 'show') && ($isElementsShowcasePage || $isStartLeftInline);
$showStoriesInline = ($ACT === 'show') && ($isElementsShowcasePage || $isStartLeftInline);
$isHardwarePage = (preg_match('/^(cs|en):resources:hardware(?::start)?$/', (string) $ID) === 1);
$reskinHardwareCatalogUrl = $isHardwarePage
    ? (DOKU_BASE . 'lib/exe/fetch.php?media=' . rawurlencode('hardware:catalog.json'))
    : '';
$reskinCleanBreadcrumb = '';
if ($reskinLayout === 'clean' && $ACT === 'show') {
    $reskinOriginalYouAreHere = $conf['youarehere'];
    $conf['youarehere'] = 1;
    $reskinCleanBreadcrumb = tpl_youarehere(' / ', true);
    $conf['youarehere'] = $reskinOriginalYouAreHere;
    if (!is_string($reskinCleanBreadcrumb)) {
        $reskinCleanBreadcrumb = '';
    }

    if ($reskinCleanBreadcrumb !== '') {
        $reskinVariantParams = reskin_variant_url_params();
        if (!empty($reskinVariantParams)) {
            $reskinCleanBreadcrumb = preg_replace_callback(
                '/href="([^"]+)"/',
                static function (array $match) use ($reskinVariantParams): string {
                    $href = html_entity_decode($match[1], ENT_QUOTES, 'UTF-8');
                    if ($href === '' || $href[0] === '#' || preg_match('/^(mailto:|tel:|javascript:)/i', $href)) {
                        return $match[0];
                    }

                    $parts = parse_url($href);
                    if (!is_array($parts)) {
                        return $match[0];
                    }

                    $query = [];
                    if (!empty($parts['query'])) {
                        parse_str($parts['query'], $query);
                    }
                    foreach ($reskinVariantParams as $key => $value) {
                        $query[$key] = $value;
                    }

                    $rebuilt = '';
                    if (isset($parts['scheme'])) $rebuilt .= $parts['scheme'] . '://';
                    if (isset($parts['user'])) {
                        $rebuilt .= $parts['user'];
                        if (isset($parts['pass'])) $rebuilt .= ':' . $parts['pass'];
                        $rebuilt .= '@';
                    }
                    if (isset($parts['host'])) $rebuilt .= $parts['host'];
                    if (isset($parts['port'])) $rebuilt .= ':' . $parts['port'];
                    if (isset($parts['path'])) $rebuilt .= $parts['path'];

                    $queryString = http_build_query($query, '', '&');
                    if ($queryString !== '') $rebuilt .= '?' . $queryString;
                    if (!empty($parts['fragment'])) $rebuilt .= '#' . $parts['fragment'];

                    return 'href="' . hsc($rebuilt) . '"';
                },
                $reskinCleanBreadcrumb
            ) ?? $reskinCleanBreadcrumb;
        }
    }
}
$reskinPageIdClass = preg_replace('/[^a-z0-9_-]+/i', '-', strtolower((string) $ID));
$reskinPageIdClass = trim((string) $reskinPageIdClass, '-');
if ($reskinPageIdClass === '') {
    $reskinPageIdClass = 'start';
}
$reskinPageIdClass = 'reskin-pageid-' . $reskinPageIdClass;
?>
<!DOCTYPE html>
<html lang="<?php echo hsc($reskinCurrentLang); ?>" dir="<?php echo hsc($lang['direction']); ?>" class="no-js">
<?php require __DIR__ . '/head.php'; ?>

<body class="reskin-body">
    <div
        id="dokuwiki__site"
        class="reskin-site <?php echo tpl_classes(); ?> <?php echo hsc($reskinPageIdClass); ?> <?php echo $showSidebarAside ? 'has-sidebar' : ''; ?> <?php echo hsc('reskin-layout-' . $reskinLayout); ?> <?php echo hsc('reskin-style-' . $reskinStyle); ?>"
        <?php if ($isHardwarePage) : ?>data-hw-catalog-url="<?php echo hsc($reskinHardwareCatalogUrl); ?>"<?php endif; ?>
    >
        <?php require __DIR__ . '/header.php'; ?>
        <?php if ($showTopNavigation) : ?>
            <?php require __DIR__ . '/topnav.php'; ?>
        <?php endif; ?>

        <main id="reskin-main" class="reskin-main">
            <?php if ($showHeroSection || $showUpdatesSection || $showMetricsSection || $showStoriesSection || $showMetricsInline || $showStoriesInline) : ?>
                <?php if ($showHeroSection) : ?>
                <section class="reskin-hero" aria-label="Hero">
                    <div class="container-xl">
                        <div class="row align-items-center g-4">
                            <div class="col-12 col-lg-7">
                                <div class="reskin-hero-content">
                                    <?php reskin_i18n_include_page('hero', true, true); ?>
                                </div>
                            </div>
                            <div class="col-12 col-lg-5">
                                <div class="reskin-hero-card">
                                    <img
                                        class="reskin-hero-image"
                                        src="https://www.cesnet.cz/gimg/default/2/8/4/284-580-326.jpg"
                                        alt="MetaCentrum infrastructure"
                                    >
                                    <div class="reskin-hero-rings"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($showUpdatesSection || $showUpdatesInline) : ?>
                <?php
                $reskinNewsItems = reskin_i18n_recent_headings('news:start', 3, $reskinCurrentLang);
                $reskinOutageItems = reskin_i18n_recent_headings('outages:start', 3, $reskinCurrentLang);
                if ($isNewsShowcasePage && empty($reskinNewsItems)) {
                    if ($reskinCurrentLang === 'cs') {
                        $reskinNewsItems = [
                            ['title' => 'EGI.eu vyhlašuje výběrové řízení na nového ředitele', 'url' => reskin_i18n_link('news:start'), 'date_iso' => '2013-11-14', 'date' => reskin_i18n_format_update_date('2013-11-14', 'cs')],
                            ['title' => 'EGI Inspired Newsletter – Léto 2012', 'url' => reskin_i18n_link('news:start'), 'date_iso' => '2012-09-06', 'date' => reskin_i18n_format_update_date('2012-09-06', 'cs')],
                            ['title' => 'EGI Inspired Newsletter – Jaro 2012', 'url' => reskin_i18n_link('news:start'), 'date_iso' => '2012-05-24', 'date' => reskin_i18n_format_update_date('2012-05-24', 'cs')],
                        ];
                    } else {
                        $reskinNewsItems = [
                            ['title' => 'EGI.eu is now seeking to employ a Director', 'url' => reskin_i18n_link('news:start'), 'date_iso' => '2013-11-14', 'date' => reskin_i18n_format_update_date('2013-11-14', 'en')],
                            ['title' => 'EGI Inspired Newsletter - Summer 2012', 'url' => reskin_i18n_link('news:start'), 'date_iso' => '2012-09-06', 'date' => reskin_i18n_format_update_date('2012-09-06', 'en')],
                            ['title' => 'EGI Inspired Newsletter - Spring 2012', 'url' => reskin_i18n_link('news:start'), 'date_iso' => '2012-05-24', 'date' => reskin_i18n_format_update_date('2012-05-24', 'en')],
                        ];
                    }
                }
                if ($isNewsShowcasePage && empty($reskinOutageItems)) {
                    if ($reskinCurrentLang === 'cs') {
                        $reskinOutageItems = [
                            ['title' => 'Plánovaná odstávka: frontendy a scheduler', 'url' => reskin_i18n_link('outages:start'), 'date_iso' => '2026-04-14', 'date' => reskin_i18n_format_update_date('2026-04-14', 'cs')],
                            ['title' => 'Údržba úložišť: metadata scratch', 'url' => reskin_i18n_link('outages:start'), 'date_iso' => '2026-04-16', 'date' => reskin_i18n_format_update_date('2026-04-16', 'cs')],
                            ['title' => 'Kde sledovat aktuální stav', 'url' => reskin_i18n_link('outages:start')],
                        ];
                    } else {
                        $reskinOutageItems = [
                            ['title' => 'Planned outage: frontends and scheduler', 'url' => reskin_i18n_link('outages:start'), 'date_iso' => '2026-04-14', 'date' => reskin_i18n_format_update_date('2026-04-14', 'en')],
                            ['title' => 'Storage maintenance: metadata scratch', 'url' => reskin_i18n_link('outages:start'), 'date_iso' => '2026-04-16', 'date' => reskin_i18n_format_update_date('2026-04-16', 'en')],
                            ['title' => 'Where to track current status', 'url' => reskin_i18n_link('outages:start')],
                        ];
                    }
                }
                ?>
                <?php endif; ?>

                <?php if ($showUpdatesSection) : ?>
                <section class="reskin-updates" aria-label="<?php echo hsc(reskin_i18n_t('updates_block')); ?>">
                    <div class="container-xl">
                        <div class="row g-4">
                            <div class="col-12 col-xl-6">
                                <article class="reskin-update-card">
                                    <header class="reskin-update-header">
                                        <h2 class="reskin-update-title">
                                            <i class="bi bi-journal-text" aria-hidden="true"></i>
                                            <?php echo hsc(reskin_i18n_t('updates_news_title')); ?>
                                        </h2>
                                        <a class="reskin-update-more" href="<?php echo hsc(reskin_i18n_link('news:start')); ?>">
                                            <?php echo hsc(reskin_i18n_t('updates_news_archive')); ?>
                                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </header>

                                    <?php if (!empty($reskinNewsItems)) : ?>
                                        <ul class="reskin-update-list">
                                            <?php foreach ($reskinNewsItems as $item) : ?>
                                                <li>
                                                    <a class="reskin-update-row<?php echo empty($item['date']) ? ' reskin-update-row--undated' : ''; ?>" href="<?php echo hsc($item['url']); ?>">
                                                        <?php if (!empty($item['date'])) : ?>
                                                            <time class="reskin-update-date" datetime="<?php echo hsc($item['date_iso']); ?>"><?php echo hsc($item['date']); ?></time>
                                                        <?php endif; ?>
                                                        <span class="reskin-update-item-title"><?php echo hsc($item['title']); ?></span>
                                                        <i class="bi bi-chevron-right reskin-update-chevron" aria-hidden="true"></i>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <p class="reskin-update-empty"><?php echo hsc(reskin_i18n_t('updates_empty')); ?></p>
                                    <?php endif; ?>
                                </article>
                            </div>

                            <div class="col-12 col-xl-6">
                                <article class="reskin-update-card">
                                    <header class="reskin-update-header">
                                        <h2 class="reskin-update-title">
                                            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                                            <?php echo hsc(reskin_i18n_t('updates_outages_title')); ?>
                                        </h2>
                                        <a class="reskin-update-more" href="<?php echo hsc(reskin_i18n_link('outages:start')); ?>">
                                            <?php echo hsc(reskin_i18n_t('updates_outages_archive')); ?>
                                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </header>

                                    <?php if (!empty($reskinOutageItems)) : ?>
                                        <ul class="reskin-update-list">
                                            <?php foreach ($reskinOutageItems as $item) : ?>
                                                <li>
                                                    <a class="reskin-update-row<?php echo empty($item['date']) ? ' reskin-update-row--undated' : ''; ?>" href="<?php echo hsc($item['url']); ?>">
                                                        <?php if (!empty($item['date'])) : ?>
                                                            <time class="reskin-update-date" datetime="<?php echo hsc($item['date_iso']); ?>"><?php echo hsc($item['date']); ?></time>
                                                        <?php endif; ?>
                                                        <span class="reskin-update-item-title"><?php echo hsc($item['title']); ?></span>
                                                        <i class="bi bi-chevron-right reskin-update-chevron" aria-hidden="true"></i>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <p class="reskin-update-empty"><?php echo hsc(reskin_i18n_t('updates_empty')); ?></p>
                                    <?php endif; ?>
                                </article>
                            </div>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($showMetricsSection || $showStoriesSection || $showMetricsInline || $showStoriesInline) : ?>
                <?php
                $reskinMetricCards = reskin_i18n_start_metric_cards($reskinCurrentLang);
                $reskinMetricHighlights = reskin_i18n_start_metric_highlights($reskinCurrentLang);
                $reskinStoryCards = reskin_i18n_start_story_cards($reskinCurrentLang);
                ?>
                <?php endif; ?>

                <?php if ($showMetricsSection && !empty($reskinMetricCards)) : ?>
                    <section class="reskin-metrics" aria-label="<?php echo hsc(reskin_i18n_t('quick_tiles_block')); ?>">
                        <div class="container-xl">
                            <div class="reskin-section-head reskin-section-head--metrics">
                                <div>
                                    <h2><?php echo hsc(reskin_i18n_t('quick_tiles_block')); ?></h2>
                                    <p><?php echo hsc(reskin_i18n_t('quick_tiles_desc')); ?></p>
                                </div>
                            </div>

                            <div class="reskin-metric-links" role="list">
                                <?php foreach ($reskinMetricCards as $card) : ?>
                                    <a class="reskin-metric-link" href="<?php echo hsc($card['url']); ?>" role="listitem">
                                        <span class="reskin-metric-link-icon"><i class="bi <?php echo hsc($card['icon']); ?>" aria-hidden="true"></i></span>
                                        <span class="reskin-metric-link-title"><?php echo hsc($card['title']); ?></span>
                                        <span class="reskin-metric-link-arrow" aria-hidden="true">&#8594;</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>

                            <?php if (!empty($reskinMetricHighlights)) : ?>
                                <div class="reskin-metric-hex-wrap">
                                    <?php foreach (array_values($reskinMetricHighlights) as $index => $metric) : ?>
                                        <article class="reskin-metric-hex<?php echo $index === 1 ? ' is-offset' : ''; ?>">
                                            <span class="reskin-metric-hex-number"><?php echo hsc($metric['number']); ?></span>
                                            <span class="reskin-metric-hex-label"><?php echo hsc($metric['label']); ?></span>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ($showStoriesSection && !empty($reskinStoryCards)) : ?>
                    <section class="reskin-stories" aria-label="<?php echo hsc(reskin_i18n_t('stories_block')); ?>">
                        <div class="container-xl">
                            <div class="reskin-section-head reskin-section-head--stories">
                                <div>
                                    <h2><?php echo hsc(reskin_i18n_t('stories_block')); ?></h2>
                                    <p><?php echo hsc(reskin_i18n_t('stories_desc')); ?></p>
                                </div>
                                <a class="reskin-stories-archive" href="<?php echo hsc(reskin_i18n_link('news:start')); ?>">
                                    <?php echo hsc(reskin_i18n_t('stories_all_news')); ?>
                                </a>
                            </div>

                            <div class="reskin-story-carousel" data-story-slider>
                                <div class="reskin-story-viewport">
                                    <div class="reskin-story-track" id="reskinStoryTrack">
                                        <?php foreach ($reskinStoryCards as $card) : ?>
                                            <article class="reskin-story-item">
                                                <a class="reskin-story-card" href="<?php echo hsc($card['url']); ?>">
                                                    <span class="reskin-story-image" style="background-image: url('<?php echo hsc($card['image']); ?>');"></span>
                                                    <span class="reskin-story-date"><?php echo hsc($card['date'] . '/' . $card['year']); ?></span>
                                                    <span class="reskin-story-title"><?php echo hsc($card['title']); ?></span>
                                                    <span class="reskin-story-cta">
                                                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                                        <?php echo hsc(reskin_i18n_t('stories_cta')); ?>
                                                    </span>
                                                </a>
                                            </article>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <?php if (count($reskinStoryCards) > 1) : ?>
                                    <div class="reskin-story-controls">
                                        <button class="btn reskin-story-control" type="button" data-story-prev aria-controls="reskinStoryTrack" aria-label="<?php echo hsc(reskin_i18n_t('stories_prev')); ?>">
                                            <i class="bi bi-arrow-left" aria-hidden="true"></i>
                                        </button>
                                        <button class="btn reskin-story-control" type="button" data-story-next aria-controls="reskinStoryTrack" aria-label="<?php echo hsc(reskin_i18n_t('stories_next')); ?>">
                                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($showSidebarMobileTrigger) : ?>
                <div class="d-lg-none reskin-sidebar-mobile">
                    <button
                        class="btn reskin-sidebar-fab"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#reskinSidebar"
                        aria-controls="reskinSidebar"
                        aria-label="<?php echo hsc(reskin_i18n_t('browse_sections')); ?>"
                    >
                        <i class="bi bi-layout-text-sidebar-reverse" aria-hidden="true"></i>
                        <span class="reskin-sidebar-fab-label"><?php echo hsc(reskin_i18n_t('browse_sections')); ?></span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="container-xl reskin-content">
                <div class="row g-4">
                    <?php if ($showSidebarAside) : ?>
                        <aside class="col-lg-3 d-none d-lg-block">
                            <div class="reskin-sidebar sticky-top">
                                <?php $reskinSidebarInstance = 'desktop'; ?>
                                <?php require __DIR__ . '/sidebar.php'; ?>
                            </div>
                        </aside>
                    <?php endif; ?>

                    <div class="<?php echo $showSidebarAside ? 'col-12 col-lg-9' : 'col-12'; ?>">
                        <?php if ($reskinCleanBreadcrumb !== '') : ?>
                            <nav class="reskin-clean-breadcrumbs" aria-label="<?php echo hsc($lang['youarehere']); ?>">
                                <?php echo $reskinCleanBreadcrumb; ?>
                            </nav>
                        <?php endif; ?>

                        <?php if ($showUpdatesInline) : ?>
                            <?php
                            $reskinInlineNewsItems = $reskinNewsItems ?? [];
                            $reskinInlineOutageItems = $reskinOutageItems ?? [];

                            if (empty($reskinInlineNewsItems)) {
                                if ($reskinCurrentLang === 'cs') {
                                    $reskinInlineNewsItems = [
                                        ['title' => 'EGI.eu vyhlašuje výběrové řízení na nového ředitele', 'url' => reskin_i18n_link('news:start'), 'date_iso' => '2013-11-14', 'date' => reskin_i18n_format_update_date('2013-11-14', 'cs')],
                                        ['title' => 'EGI Inspired Newsletter – Léto 2012', 'url' => reskin_i18n_link('news:start'), 'date_iso' => '2012-09-06', 'date' => reskin_i18n_format_update_date('2012-09-06', 'cs')],
                                        ['title' => 'EGI Inspired Newsletter – Jaro 2012', 'url' => reskin_i18n_link('news:start'), 'date_iso' => '2012-05-24', 'date' => reskin_i18n_format_update_date('2012-05-24', 'cs')],
                                    ];
                                } else {
                                    $reskinInlineNewsItems = [
                                        ['title' => 'EGI.eu is now seeking to employ a Director', 'url' => reskin_i18n_link('news:start'), 'date_iso' => '2013-11-14', 'date' => reskin_i18n_format_update_date('2013-11-14', 'en')],
                                        ['title' => 'EGI Inspired Newsletter - Summer 2012', 'url' => reskin_i18n_link('news:start'), 'date_iso' => '2012-09-06', 'date' => reskin_i18n_format_update_date('2012-09-06', 'en')],
                                        ['title' => 'EGI Inspired Newsletter - Spring 2012', 'url' => reskin_i18n_link('news:start'), 'date_iso' => '2012-05-24', 'date' => reskin_i18n_format_update_date('2012-05-24', 'en')],
                                    ];
                                }
                            }

                            if (empty($reskinInlineOutageItems)) {
                                if ($reskinCurrentLang === 'cs') {
                                    $reskinInlineOutageItems = [
                                        ['title' => 'Plánovaná odstávka: frontendy a scheduler', 'url' => reskin_i18n_link('outages:start'), 'date_iso' => '2026-04-14', 'date' => reskin_i18n_format_update_date('2026-04-14', 'cs')],
                                        ['title' => 'Údržba úložišť: metadata scratch', 'url' => reskin_i18n_link('outages:start'), 'date_iso' => '2026-04-16', 'date' => reskin_i18n_format_update_date('2026-04-16', 'cs')],
                                        ['title' => 'Kde sledovat aktuální stav', 'url' => reskin_i18n_link('outages:start')],
                                    ];
                                } else {
                                    $reskinInlineOutageItems = [
                                        ['title' => 'Planned outage: frontends and scheduler', 'url' => reskin_i18n_link('outages:start'), 'date_iso' => '2026-04-14', 'date' => reskin_i18n_format_update_date('2026-04-14', 'en')],
                                        ['title' => 'Storage maintenance: metadata scratch', 'url' => reskin_i18n_link('outages:start'), 'date_iso' => '2026-04-16', 'date' => reskin_i18n_format_update_date('2026-04-16', 'en')],
                                        ['title' => 'Where to track current status', 'url' => reskin_i18n_link('outages:start')],
                                    ];
                                }
                            }
                            ?>
                            <section class="reskin-updates reskin-updates--inline" aria-label="<?php echo hsc(reskin_i18n_t('updates_block')); ?>">
                                <div class="row g-4">
                                    <div class="<?php echo $reskinLayout === 'left' ? 'col-12' : 'col-12 col-xl-6'; ?>">
                                        <article class="reskin-update-card">
                                            <header class="reskin-update-header">
                                                <h2 class="reskin-update-title">
                                                    <i class="bi bi-journal-text" aria-hidden="true"></i>
                                                    <?php echo hsc(reskin_i18n_t('updates_news_title')); ?>
                                                </h2>
                                                <a class="reskin-update-more" href="<?php echo hsc(reskin_i18n_link('news:start')); ?>">
                                                    <?php echo hsc(reskin_i18n_t('updates_news_archive')); ?>
                                                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                                </a>
                                            </header>

                                            <?php if (!empty($reskinInlineNewsItems)) : ?>
                                                <ul class="reskin-update-list">
                                                    <?php foreach ($reskinInlineNewsItems as $item) : ?>
                                                        <li>
                                                            <a class="reskin-update-row<?php echo empty($item['date']) ? ' reskin-update-row--undated' : ''; ?>" href="<?php echo hsc($item['url']); ?>">
                                                                <?php if (!empty($item['date'])) : ?>
                                                                    <time class="reskin-update-date" datetime="<?php echo hsc($item['date_iso']); ?>"><?php echo hsc($item['date']); ?></time>
                                                                <?php endif; ?>
                                                                <span class="reskin-update-item-title"><?php echo hsc($item['title']); ?></span>
                                                                <i class="bi bi-chevron-right reskin-update-chevron" aria-hidden="true"></i>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else : ?>
                                                <p class="reskin-update-empty"><?php echo hsc(reskin_i18n_t('updates_empty')); ?></p>
                                            <?php endif; ?>
                                        </article>
                                    </div>

                                    <div class="<?php echo $reskinLayout === 'left' ? 'col-12' : 'col-12 col-xl-6'; ?>">
                                        <article class="reskin-update-card">
                                            <header class="reskin-update-header">
                                                <h2 class="reskin-update-title">
                                                    <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                                                    <?php echo hsc(reskin_i18n_t('updates_outages_title')); ?>
                                                </h2>
                                                <a class="reskin-update-more" href="<?php echo hsc(reskin_i18n_link('outages:start')); ?>">
                                                    <?php echo hsc(reskin_i18n_t('updates_outages_archive')); ?>
                                                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                                </a>
                                            </header>

                                            <?php if (!empty($reskinInlineOutageItems)) : ?>
                                                <ul class="reskin-update-list">
                                                    <?php foreach ($reskinInlineOutageItems as $item) : ?>
                                                        <li>
                                                            <a class="reskin-update-row<?php echo empty($item['date']) ? ' reskin-update-row--undated' : ''; ?>" href="<?php echo hsc($item['url']); ?>">
                                                                <?php if (!empty($item['date'])) : ?>
                                                                    <time class="reskin-update-date" datetime="<?php echo hsc($item['date_iso']); ?>"><?php echo hsc($item['date']); ?></time>
                                                                <?php endif; ?>
                                                                <span class="reskin-update-item-title"><?php echo hsc($item['title']); ?></span>
                                                                <i class="bi bi-chevron-right reskin-update-chevron" aria-hidden="true"></i>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else : ?>
                                                <p class="reskin-update-empty"><?php echo hsc(reskin_i18n_t('updates_empty')); ?></p>
                                            <?php endif; ?>
                                        </article>
                                    </div>
                                </div>
                            </section>
                        <?php endif; ?>

                        <?php if ($showMetricsInline && !empty($reskinMetricCards)) : ?>
                            <section class="reskin-metrics reskin-metrics--inline" aria-label="<?php echo hsc(reskin_i18n_t('quick_tiles_block')); ?>">
                                <div class="reskin-section-head reskin-section-head--metrics">
                                    <div>
                                        <h2><?php echo hsc(reskin_i18n_t('quick_tiles_block')); ?></h2>
                                        <p><?php echo hsc(reskin_i18n_t('quick_tiles_desc')); ?></p>
                                    </div>
                                </div>

                                <div class="reskin-metric-links" role="list">
                                    <?php foreach ($reskinMetricCards as $card) : ?>
                                        <a class="reskin-metric-link" href="<?php echo hsc($card['url']); ?>" role="listitem">
                                            <span class="reskin-metric-link-icon"><i class="bi <?php echo hsc($card['icon']); ?>" aria-hidden="true"></i></span>
                                            <span class="reskin-metric-link-title"><?php echo hsc($card['title']); ?></span>
                                            <span class="reskin-metric-link-arrow" aria-hidden="true">&#8594;</span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (!empty($reskinMetricHighlights)) : ?>
                                    <div class="reskin-metric-hex-wrap">
                                        <?php foreach (array_values($reskinMetricHighlights) as $index => $metric) : ?>
                                            <article class="reskin-metric-hex<?php echo $index === 1 ? ' is-offset' : ''; ?>">
                                                <span class="reskin-metric-hex-number"><?php echo hsc($metric['number']); ?></span>
                                                <span class="reskin-metric-hex-label"><?php echo hsc($metric['label']); ?></span>
                                            </article>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </section>
                        <?php endif; ?>

                        <?php if ($showStoriesInline && !empty($reskinStoryCards)) : ?>
                            <section class="reskin-stories reskin-stories--inline" aria-label="<?php echo hsc(reskin_i18n_t('stories_block')); ?>">
                                <div class="reskin-section-head reskin-section-head--stories">
                                    <div>
                                        <h2><?php echo hsc(reskin_i18n_t('stories_block')); ?></h2>
                                        <p><?php echo hsc(reskin_i18n_t('stories_desc')); ?></p>
                                    </div>
                                    <a class="reskin-stories-archive" href="<?php echo hsc(reskin_i18n_link('news:start')); ?>">
                                        <?php echo hsc(reskin_i18n_t('stories_all_news')); ?>
                                    </a>
                                </div>

                                <div class="reskin-story-carousel" data-story-slider>
                                    <div class="reskin-story-viewport">
                                        <div class="reskin-story-track" id="reskinStoryTrackInline">
                                            <?php foreach ($reskinStoryCards as $card) : ?>
                                                <article class="reskin-story-item">
                                                    <a class="reskin-story-card" href="<?php echo hsc($card['url']); ?>">
                                                        <span class="reskin-story-image" style="background-image: url('<?php echo hsc($card['image']); ?>');"></span>
                                                        <span class="reskin-story-date"><?php echo hsc($card['date'] . '/' . $card['year']); ?></span>
                                                        <span class="reskin-story-title"><?php echo hsc($card['title']); ?></span>
                                                        <span class="reskin-story-cta">
                                                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                                            <?php echo hsc(reskin_i18n_t('stories_cta')); ?>
                                                        </span>
                                                    </a>
                                                </article>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <?php if (count($reskinStoryCards) > 1) : ?>
                                        <div class="reskin-story-controls">
                                            <button class="btn reskin-story-control" type="button" data-story-prev aria-controls="reskinStoryTrackInline" aria-label="<?php echo hsc(reskin_i18n_t('stories_prev')); ?>">
                                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                                            </button>
                                            <button class="btn reskin-story-control" type="button" data-story-next aria-controls="reskinStoryTrackInline" aria-label="<?php echo hsc(reskin_i18n_t('stories_next')); ?>">
                                                <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </section>
                        <?php endif; ?>

                        <?php html_msgarea(); ?>

                        <?php if ($reskinFallbackNotice) : ?>
                            <div class="alert alert-warning reskin-i18n-fallback" role="status">
                                <?php echo hsc($reskinFallbackNotice); ?>
                            </div>
                        <?php endif; ?>

                        <?php
                        $reskinContentHtml = '';
                        if ($reskinNewsEntries === []) {
                            ob_start();
                            tpl_content($ACT !== 'show');
                            $reskinContentHtml = (string) ob_get_clean();
                        }
                        $reskinTocHtml = '';
                        if ($ACT === 'show' && $reskinNewsArticle === null && !preg_match('/^(?:cs|en):(?:variants:news|outages:start)$/', (string) $ID)) {
                            $reskinTocHtml = tpl_toc(true);
                            $reskinTocHtml = preg_replace(
                                '/<h3 class="toggle">.*?<\/h3>/su',
                                '<p class="reskin-toc-title">' . hsc(reskin_i18n_t('page_toc')) . '</p>',
                                $reskinTocHtml,
                                1
                            ) ?? $reskinTocHtml;
                        } elseif ($ACT !== 'show') {
                            $reskinContentHtml = preg_replace(
                                '/(<div[^>]*id="dw__toc"[^>]*>\s*<h3[^>]*class="toggle"[^>]*>)(.*?)(<\/h3>)/su',
                                '$1' . hsc(reskin_i18n_t('toc')) . '$3',
                                $reskinContentHtml,
                                1
                            ) ?? $reskinContentHtml;
                        }
                        ?>
                        <div class="reskin-article-layout<?php echo $reskinTocHtml !== '' ? ' has-toc' : ''; ?>">
                            <?php if ($reskinTocHtml !== '') : ?>
                                <aside class="reskin-page-toc" aria-label="<?php echo hsc(reskin_i18n_t('page_toc')); ?>">
                                    <?php echo $reskinTocHtml; ?>
                                </aside>
                            <?php endif; ?>
                            <div class="reskin-article">
                                <div class="reskin-page">
                                    <?php tpl_includeFile('pageheader.html'); ?>
                                    <?php if ($reskinNewsEntries !== []) : ?>
                                        <?php if ($reskinNewsArticle === null) : ?>
                                            <?php $reskinArchiveAnchors = []; ?>
                                            <h1 class="reskin-news-archive-title" id="<?php echo hsc(sectionID(reskin_i18n_t('updates_news_title'), $reskinArchiveAnchors)); ?>"><?php echo hsc(reskin_i18n_t('updates_news_title')); ?></h1>
                                        <?php endif; ?>
                                        <?php foreach ($reskinNewsArticle === null ? $reskinNewsEntries : [$reskinNewsArticle] as $reskinEntry) : ?>
                                        <?php $reskinEntryUrl = wl($ID, reskin_variant_url_params(['article' => $reskinEntry['anchor']]), false, '&'); ?>
                                        <article class="reskin-news-article <?php echo $reskinNewsArticle === null ? 'reskin-news-article--archive' : 'reskin-news-article--detail'; ?>">
                                            <header class="reskin-news-article-header">
                                                <span class="reskin-news-eyebrow"><?php echo hsc(reskin_i18n_t('updates_news_title')); ?></span>
                                                <?php if ($reskinNewsArticle === null) : ?>
                                                    <h2 id="<?php echo hsc($reskinEntry['anchor']); ?>"><a class="reskin-news-heading-link" href="<?php echo hsc($reskinEntryUrl); ?>"><?php echo hsc($reskinEntry['title']); ?></a></h2>
                                                <?php else : ?>
                                                    <h1><?php echo hsc($reskinEntry['title']); ?></h1>
                                                <?php endif; ?>
                                                <?php if ($reskinEntry['date'] !== '' || $reskinEntry['author'] !== '') : ?>
                                                    <div class="reskin-news-meta">
                                                        <?php if ($reskinEntry['date'] !== '') : ?>
                                                            <time datetime="<?php echo hsc($reskinEntry['date_iso']); ?>"><?php echo hsc($reskinEntry['date']); ?></time>
                                                        <?php endif; ?>
                                                        <?php if ($reskinEntry['author'] !== '') : ?>
                                                            <span><?php echo hsc($reskinEntry['author']); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </header>
                                            <div class="reskin-news-body">
                                                <?php
                                                $reskinNewsRenderInfo = [];
                                                echo p_render('xhtml', p_get_instructions($reskinEntry['body']), $reskinNewsRenderInfo);
                                                ?>
                                            </div>
                                            <?php if ($reskinEntry['external_url'] !== '') : ?>
                                                <a class="reskin-news-external" href="<?php echo hsc($reskinEntry['external_url']); ?>">
                                                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                                                    <span>
                                                        <span class="reskin-news-external-label"><?php echo hsc(reskin_i18n_t('news_article_more')); ?></span>
                                                        <span class="reskin-news-external-title"><?php echo hsc($reskinEntry['external_title']); ?></span>
                                                    </span>
                                                </a>
                                            <?php endif; ?>
                                            <footer class="reskin-news-footer">
                                                <?php if ($reskinEntry['permalink'] !== '') : ?>
                                                    <a class="reskin-news-permalink" href="<?php echo hsc($reskinEntry['permalink']); ?>"><?php echo hsc(reskin_i18n_t('news_article_permalink')); ?></a>
                                                <?php endif; ?>
                                                <a class="reskin-news-back" href="<?php echo hsc($reskinNewsArticle === null ? $reskinEntryUrl : reskin_i18n_link('news:start')); ?>">
                                                    <?php if ($reskinNewsArticle !== null) : ?><i class="bi bi-arrow-left" aria-hidden="true"></i><?php endif; ?>
                                                    <?php echo hsc(reskin_i18n_t($reskinNewsArticle === null ? 'news_article_read' : 'news_article_back')); ?>
                                                    <?php if ($reskinNewsArticle === null) : ?><i class="bi bi-arrow-right" aria-hidden="true"></i><?php endif; ?>
                                                </a>
                                            </footer>
                                        </article>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <?php echo $reskinContentHtml; ?>
                                    <?php endif; ?>
                                    <?php tpl_includeFile('pagefooter.html'); ?>
                                </div>

                                <?php if ($reskinNewsArticle === null) : ?>
                                    <div class="reskin-page-info">
                                        <?php tpl_pageinfo(); ?>
                                    </div>

                                    <nav class="reskin-pagetools" aria-label="Page tools">
                                        <ul class="list-inline mb-0">
                                            <?php echo (new \dokuwiki\Menu\PageMenu())->getListItems(); ?>
                                        </ul>
                                    </nav>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php require __DIR__ . '/footer.php'; ?>
    </div>

    <div class="no"><?php tpl_indexerWebBug() ?></div>
    <div id="screen__mode" class="no"></div>

    <?php if ($showSidebarOffcanvas) : ?>
        <div class="offcanvas offcanvas-start reskin-offcanvas<?php echo $reskinLayout === 'top' && $reskinStyle === 'cesnet' ? ' reskin-offcanvas--top-cesnet' : ''; ?>" tabindex="-1" id="reskinSidebar" aria-labelledby="reskinSidebarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="reskinSidebarLabel"><?php echo hsc(reskin_i18n_t('navigation')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="<?php echo hsc(reskin_i18n_t('close')); ?>"></button>
            </div>
            <div class="offcanvas-body">
                <?php $reskinSidebarInstance = 'offcanvas'; ?>
                <?php require __DIR__ . '/sidebar.php'; ?>
            </div>
        </div>
    <?php endif; ?>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"
    ></script>
    <?php
    $reskinJsFiles = [
        'theme.js',
        'search.js',
    ];
    if ($reskinTocHtml !== '') {
        $reskinJsFiles[] = 'page-toc.js';
    }
    if ($showTopNavigation) {
        $reskinJsFiles[] = 'topnav-overflow.js';
    }
    if (($isStart && $ACT === 'show') || $showStoriesInline) {
        $reskinJsFiles[] = 'story-slider.js';
    }
    if ($isHardwarePage) {
        $reskinJsFiles[] = 'hardware-drawer.js';
    }
    ?>
    <?php foreach ($reskinJsFiles as $reskinJsFile) : ?>
        <?php $reskinJsVersion = @filemtime(__DIR__ . '/js/' . $reskinJsFile); ?>
        <script src="<?php echo tpl_basedir(); ?>js/<?php echo hsc($reskinJsFile); ?>?v=<?php echo $reskinJsVersion ?: '1'; ?>"></script>
    <?php endforeach; ?>
</body>
</html>
