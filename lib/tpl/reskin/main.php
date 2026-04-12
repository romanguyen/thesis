<?php
/**
 * Reskin template for DokuWiki
 */

if (!defined('DOKU_INC')) die();

global $ID, $ACT, $conf, $lang;

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
$isElementsShowcasePage = ($reskinParsedId['base'] === 'variants:it4i-elements');
$showHeroSection = $isStart && ($ACT === 'show');
$showUpdatesSection = ($ACT === 'show') && $isStart;
$showUpdatesInline = ($ACT === 'show') && $isNewsShowcasePage;
$showMetricsSection = ($ACT === 'show') && $isStart;
$showStoriesSection = ($ACT === 'show') && $isStart;
$showMetricsInline = ($ACT === 'show') && $isElementsShowcasePage;
$showStoriesInline = ($ACT === 'show') && $isElementsShowcasePage;
$isHardwarePage = (preg_match('/^(cs|en):resources:hardware(?::start)?$/', (string) $ID) === 1);
$reskinHardwareCatalogUrl = $isHardwarePage
    ? (DOKU_BASE . 'lib/exe/fetch.php?media=' . rawurlencode('hardware:catalog.json'))
    : '';
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
                            ['title' => 'EGI.eu vyhlasuje vyberove rizeni na noveho reditele', 'url' => reskin_i18n_link('news:start')],
                            ['title' => 'EGI Inspired Newsletter - Leto 2012', 'url' => reskin_i18n_link('news:start')],
                            ['title' => 'EGI Inspired Newsletter - Jaro 2012', 'url' => reskin_i18n_link('news:start')],
                        ];
                    } else {
                        $reskinNewsItems = [
                            ['title' => 'EGI.eu is now seeking to employ a Director', 'url' => reskin_i18n_link('news:start')],
                            ['title' => 'EGI Inspired Newsletter - Summer 2012', 'url' => reskin_i18n_link('news:start')],
                            ['title' => 'EGI Inspired Newsletter - Spring 2012', 'url' => reskin_i18n_link('news:start')],
                        ];
                    }
                }
                if ($isNewsShowcasePage && empty($reskinOutageItems)) {
                    if ($reskinCurrentLang === 'cs') {
                        $reskinOutageItems = [
                            ['title' => 'Planovana odstavka: frontendy a scheduler', 'url' => reskin_i18n_link('outages:start')],
                            ['title' => 'Udrzba ulozist: metadata scratch', 'url' => reskin_i18n_link('outages:start')],
                            ['title' => 'Kde sledovat aktualni stav', 'url' => reskin_i18n_link('outages:start')],
                        ];
                    } else {
                        $reskinOutageItems = [
                            ['title' => 'Planned outage: frontends and scheduler', 'url' => reskin_i18n_link('outages:start')],
                            ['title' => 'Storage maintenance: metadata scratch', 'url' => reskin_i18n_link('outages:start')],
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
                            <div class="col-12 col-lg-6">
                                <article class="reskin-update-card">
                                    <header class="reskin-update-header">
                                        <h2 class="reskin-update-title">
                                            <i class="bi bi-newspaper" aria-hidden="true"></i>
                                            <?php echo hsc(reskin_i18n_t('updates_news_title')); ?>
                                        </h2>
                                        <p class="reskin-update-desc"><?php echo hsc(reskin_i18n_t('updates_news_desc')); ?></p>
                                    </header>

                                    <?php if (!empty($reskinNewsItems)) : ?>
                                        <ul class="reskin-update-list">
                                            <?php foreach ($reskinNewsItems as $item) : ?>
                                                <li><a href="<?php echo hsc($item['url']); ?>"><?php echo hsc($item['title']); ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <p class="reskin-update-empty"><?php echo hsc(reskin_i18n_t('updates_empty')); ?></p>
                                    <?php endif; ?>

                                    <a class="reskin-update-more" href="<?php echo hsc(reskin_i18n_link('news:start')); ?>">
                                        <?php echo hsc(reskin_i18n_t('updates_all_news')); ?>
                                        <i class="bi bi-arrow-right-short" aria-hidden="true"></i>
                                    </a>
                                </article>
                            </div>

                            <div class="col-12 col-lg-6">
                                <article class="reskin-update-card">
                                    <header class="reskin-update-header">
                                        <h2 class="reskin-update-title">
                                            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                                            <?php echo hsc(reskin_i18n_t('updates_outages_title')); ?>
                                        </h2>
                                        <p class="reskin-update-desc"><?php echo hsc(reskin_i18n_t('updates_outages_desc')); ?></p>
                                    </header>

                                    <?php if (!empty($reskinOutageItems)) : ?>
                                        <ul class="reskin-update-list">
                                            <?php foreach ($reskinOutageItems as $item) : ?>
                                                <li><a href="<?php echo hsc($item['url']); ?>"><?php echo hsc($item['title']); ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <p class="reskin-update-empty"><?php echo hsc(reskin_i18n_t('updates_empty')); ?></p>
                                    <?php endif; ?>

                                    <a class="reskin-update-more" href="<?php echo hsc(reskin_i18n_link('outages:start')); ?>">
                                        <?php echo hsc(reskin_i18n_t('updates_all_outages')); ?>
                                        <i class="bi bi-arrow-right-short" aria-hidden="true"></i>
                                    </a>
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
                        <?php if ($showUpdatesInline) : ?>
                            <?php
                            $reskinInlineNewsItems = $reskinNewsItems ?? [];
                            $reskinInlineOutageItems = $reskinOutageItems ?? [];

                            if (empty($reskinInlineNewsItems)) {
                                if ($reskinCurrentLang === 'cs') {
                                    $reskinInlineNewsItems = [
                                        ['title' => 'EGI.eu vyhlasuje vyberove rizeni na noveho reditele', 'url' => reskin_i18n_link('news:start')],
                                        ['title' => 'EGI Inspired Newsletter - Leto 2012', 'url' => reskin_i18n_link('news:start')],
                                        ['title' => 'EGI Inspired Newsletter - Jaro 2012', 'url' => reskin_i18n_link('news:start')],
                                    ];
                                } else {
                                    $reskinInlineNewsItems = [
                                        ['title' => 'EGI.eu is now seeking to employ a Director', 'url' => reskin_i18n_link('news:start')],
                                        ['title' => 'EGI Inspired Newsletter - Summer 2012', 'url' => reskin_i18n_link('news:start')],
                                        ['title' => 'EGI Inspired Newsletter - Spring 2012', 'url' => reskin_i18n_link('news:start')],
                                    ];
                                }
                            }

                            if (empty($reskinInlineOutageItems)) {
                                if ($reskinCurrentLang === 'cs') {
                                    $reskinInlineOutageItems = [
                                        ['title' => 'Planovana odstavka: frontendy a scheduler', 'url' => reskin_i18n_link('outages:start')],
                                        ['title' => 'Udrzba ulozist: metadata scratch', 'url' => reskin_i18n_link('outages:start')],
                                        ['title' => 'Kde sledovat aktualni stav', 'url' => reskin_i18n_link('outages:start')],
                                    ];
                                } else {
                                    $reskinInlineOutageItems = [
                                        ['title' => 'Planned outage: frontends and scheduler', 'url' => reskin_i18n_link('outages:start')],
                                        ['title' => 'Storage maintenance: metadata scratch', 'url' => reskin_i18n_link('outages:start')],
                                        ['title' => 'Where to track current status', 'url' => reskin_i18n_link('outages:start')],
                                    ];
                                }
                            }
                            ?>
                            <section class="reskin-updates reskin-updates--inline" aria-label="<?php echo hsc(reskin_i18n_t('updates_block')); ?>">
                                <div class="row g-4">
                                    <div class="col-12 col-xl-6">
                                        <article class="reskin-update-card">
                                            <header class="reskin-update-header">
                                                <h2 class="reskin-update-title">
                                                    <i class="bi bi-newspaper" aria-hidden="true"></i>
                                                    <?php echo hsc(reskin_i18n_t('updates_news_title')); ?>
                                                </h2>
                                                <p class="reskin-update-desc"><?php echo hsc(reskin_i18n_t('updates_news_desc')); ?></p>
                                            </header>

                                            <?php if (!empty($reskinInlineNewsItems)) : ?>
                                                <ul class="reskin-update-list">
                                                    <?php foreach ($reskinInlineNewsItems as $item) : ?>
                                                        <li><a href="<?php echo hsc($item['url']); ?>"><?php echo hsc($item['title']); ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else : ?>
                                                <p class="reskin-update-empty"><?php echo hsc(reskin_i18n_t('updates_empty')); ?></p>
                                            <?php endif; ?>

                                            <a class="reskin-update-more" href="<?php echo hsc(reskin_i18n_link('news:start')); ?>">
                                                <?php echo hsc(reskin_i18n_t('updates_all_news')); ?>
                                                <i class="bi bi-arrow-right-short" aria-hidden="true"></i>
                                            </a>
                                        </article>
                                    </div>

                                    <div class="col-12 col-xl-6">
                                        <article class="reskin-update-card">
                                            <header class="reskin-update-header">
                                                <h2 class="reskin-update-title">
                                                    <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                                                    <?php echo hsc(reskin_i18n_t('updates_outages_title')); ?>
                                                </h2>
                                                <p class="reskin-update-desc"><?php echo hsc(reskin_i18n_t('updates_outages_desc')); ?></p>
                                            </header>

                                            <?php if (!empty($reskinInlineOutageItems)) : ?>
                                                <ul class="reskin-update-list">
                                                    <?php foreach ($reskinInlineOutageItems as $item) : ?>
                                                        <li><a href="<?php echo hsc($item['url']); ?>"><?php echo hsc($item['title']); ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else : ?>
                                                <p class="reskin-update-empty"><?php echo hsc(reskin_i18n_t('updates_empty')); ?></p>
                                            <?php endif; ?>

                                            <a class="reskin-update-more" href="<?php echo hsc(reskin_i18n_link('outages:start')); ?>">
                                                <?php echo hsc(reskin_i18n_t('updates_all_outages')); ?>
                                                <i class="bi bi-arrow-right-short" aria-hidden="true"></i>
                                            </a>
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

                        <div class="reskin-page">
                            <?php tpl_includeFile('pageheader.html'); ?>
                            <?php
                            ob_start();
                            tpl_content();
                            $reskinContentHtml = (string) ob_get_clean();
                            $reskinTocLabel = hsc(reskin_i18n_t('toc'));
                            $reskinContentHtml = preg_replace(
                                '/(<div[^>]*id="dw__toc"[^>]*>\s*<h3[^>]*class="toggle"[^>]*>)(.*?)(<\/h3>)/su',
                                '$1' . $reskinTocLabel . '$3',
                                $reskinContentHtml,
                                1
                            ) ?? $reskinContentHtml;
                            echo $reskinContentHtml;
                            ?>
                            <?php tpl_includeFile('pagefooter.html'); ?>
                        </div>

                        <div class="reskin-page-info">
                            <?php tpl_pageinfo(); ?>
                        </div>

                        <nav class="reskin-pagetools" aria-label="Page tools">
                            <ul class="list-inline mb-0">
                                <?php echo (new \dokuwiki\Menu\PageMenu())->getListItems(); ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </main>

        <?php require __DIR__ . '/footer.php'; ?>
    </div>

    <div class="no"><?php tpl_indexerWebBug() ?></div>
    <div id="screen__mode" class="no"></div>

    <?php if ($showSidebarOffcanvas) : ?>
        <div class="offcanvas offcanvas-start reskin-offcanvas" tabindex="-1" id="reskinSidebar" aria-labelledby="reskinSidebarLabel">
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
