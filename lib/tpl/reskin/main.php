<?php
/**
 * Reskin template for DokuWiki
 */

if (!defined('DOKU_INC')) die();

global $ID, $ACT, $conf, $lang, $INPUT;

require_once __DIR__ . '/inc/bootstrap.php';
$reskinRequest = reskin_request_state();
reskin_handle_request($reskinRequest);
$reskinContext = reskin_page_context($reskinRequest);

$reskinCurrentLang = $reskinContext['page']['lang'];
$reskinFallbackNotice = $reskinContext['fallback_notice'];
$reskinVariant = $reskinContext['variant'];
$reskinLayout = $reskinVariant['layout'];
$reskinStyle = $reskinVariant['style'];
$showSidebarAside = $reskinContext['show']['sidebar_aside'];
$showTopNavigation = $reskinContext['show']['top_navigation'];
$showSidebarMobileTrigger = $reskinContext['show']['sidebar_mobile_trigger'];
$showSidebarOffcanvas = $reskinContext['show']['sidebar_offcanvas'];
$isStart = $reskinContext['show']['start'];
$showHeroSection = $reskinContext['show']['hero'];
$showUpdatesSection = $reskinContext['show']['updates_section'];
$showUpdatesInline = $reskinContext['show']['updates_inline'];
$showMetricsSection = $reskinContext['show']['metrics_section'];
$showStoriesSection = $reskinContext['show']['stories_section'];
$showMetricsInline = $reskinContext['show']['metrics_inline'];
$showStoriesInline = $reskinContext['show']['stories_inline'];
$reskinNewsArticle = $reskinContext['news_article'];
$reskinNewsEntries = $reskinContext['news_entries'];
$isHardwarePage = $reskinContext['hardware'];
$reskinHardwareCatalogUrl = $reskinContext['hardware_catalog_url'];
$reskinCleanBreadcrumb = $reskinContext['breadcrumb_html'];
$reskinPageIdClass = $reskinContext['page_class'];
?>
<!DOCTYPE html>
<html lang="<?php echo hsc($reskinCurrentLang); ?>" dir="<?php echo hsc($lang['direction']); ?>" class="no-js">
<?php require __DIR__ . '/head.php'; ?>

<body class="reskin-body" data-reskin-layout="<?php echo hsc($reskinLayout); ?>" data-reskin-style="<?php echo hsc($reskinStyle); ?>">
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
            <?php if ($showHeroSection) : ?>
                <?php reskin_render_partial('hero', [
                    'content_html' => (string) reskin_i18n_include_page('hero', false, true),
                    'image_url' => 'https://www.cesnet.cz/gimg/default/2/8/4/284-580-326.jpg',
                    'image_alt' => 'MetaCentrum infrastructure',
                ]); ?>
            <?php endif; ?>

            <?php if ($showUpdatesSection || $showUpdatesInline) : ?>
                <?php
                // Standalone showcases use demos; homepages read live headings.
                // Empty full-width feeds remain empty, while inline feeds use demos.
                $reskinUpdatesModel = reskin_home_updates_model($reskinCurrentLang, $isStart, $showUpdatesInline);
                ?>
            <?php endif; ?>
            <?php if ($showUpdatesSection) : ?>
                <?php reskin_render_partial('updates', $reskinUpdatesModel); ?>
            <?php endif; ?>

            <?php if ($showMetricsSection || $showStoriesSection || $showMetricsInline || $showStoriesInline) : ?>
                <?php $reskinHomeModels = reskin_home_feature_models($reskinCurrentLang); ?>
            <?php endif; ?>
            <?php if ($showMetricsSection) : ?>
                <?php reskin_render_partial('metrics', $reskinHomeModels['metrics']); ?>
            <?php endif; ?>
            <?php if ($showStoriesSection) : ?>
                <?php reskin_render_partial('stories', $reskinHomeModels['stories'], ['track_id' => 'reskinStoryTrack']); ?>
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
                            <?php reskin_render_partial('updates', $reskinUpdatesModel, [
                                'inline' => true,
                                'stacked' => $reskinLayout === 'left',
                            ]); ?>
                        <?php endif; ?>

                        <?php if ($showMetricsInline) : ?>
                            <?php reskin_render_partial('metrics', $reskinHomeModels['metrics'], ['inline' => true]); ?>
                        <?php endif; ?>

                        <?php if ($showStoriesInline) : ?>
                            <?php reskin_render_partial('stories', $reskinHomeModels['stories'], [
                                'inline' => true,
                                'track_id' => 'reskinStoryTrackInline',
                            ]); ?>
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
                                    <?php reskin_render_partial('page-content', reskin_page_content_model(
                                        (string) $ID,
                                        $reskinContentHtml,
                                        $reskinNewsEntries,
                                        $reskinNewsArticle,
                                        $reskinContext['variant']
                                    )); ?>
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

    <?php foreach (reskin_script_assets($reskinContext, $reskinTocHtml !== '') as $reskinAsset) : ?>
        <script src="<?php echo hsc($reskinAsset['url']); ?>"
            <?php if (isset($reskinAsset['integrity'])) : ?>integrity="<?php echo hsc($reskinAsset['integrity']); ?>"<?php endif; ?>
            <?php if (isset($reskinAsset['crossorigin'])) : ?>crossorigin="<?php echo hsc($reskinAsset['crossorigin']); ?>"<?php endif; ?>
        ></script>
    <?php endforeach; ?>
</body>
</html>
