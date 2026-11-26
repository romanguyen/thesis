<?php
/**
 * Reskin template for DokuWiki
 */

if (!defined('DOKU_INC')) die();

global $ID, $ACT, $conf, $lang, $INPUT, $INFO;

require_once __DIR__ . '/inc/bootstrap.php';
reskin_register_combo_guards();
reskin_register_translation_guards();
reskin_register_search_snippets();
$reskinRequest = reskin_request_state();
reskin_handle_request($reskinRequest);
$reskinContext = reskin_page_context($reskinRequest);

$reskinCurrentLang = $reskinContext['page']['lang'];
$reskinFallbackNotice = $reskinContext['fallback_notice'];
$reskinVariant = $reskinContext['variant'];
// Rendered frame (top|left|clean) and look; Bootstrap 5 reaches this shell only as the CESNET fallback.
$reskinLayout = reskin_variant_frame($reskinVariant);
$reskinStyle = $reskinLayout === 'clean' ? 'metacentrum' : 'cesnet';
$reskinDesign = $reskinStyle === 'metacentrum';
if (!$reskinDesign) reskin_register_form_classes();
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
$reskinNewsEntries = $reskinContext['news_entries'];
$isHardwarePage = $reskinContext['hardware'];
$reskinHardwareCatalogUrl = $reskinContext['hardware_catalog_url'];
$reskinPageIdClass = $reskinContext['page_class'];
?>
<!DOCTYPE html>
<html lang="<?php echo hsc($reskinCurrentLang); ?>" dir="<?php echo hsc($lang['direction']); ?>" class="no-js">
<?php require __DIR__ . '/head.php'; ?>

<body class="reskin-body<?php if ($reskinDesign) echo ' cds--white'; ?>" data-reskin-layout="<?php echo hsc($reskinLayout); ?>" data-reskin-style="<?php echo hsc($reskinStyle); ?>">
    <div
        id="dokuwiki__site"
        class="reskin-site <?php echo tpl_classes(); ?> <?php echo hsc($reskinPageIdClass); ?> <?php echo $showSidebarAside ? 'has-sidebar' : ''; ?> <?php echo hsc('reskin-layout-' . $reskinLayout); ?> <?php echo hsc('reskin-style-' . $reskinStyle); ?>"
        <?php if ($isHardwarePage) : ?>data-hw-catalog-url="<?php echo hsc($reskinHardwareCatalogUrl); ?>"<?php endif; ?>
    >
        <div class="reskin-header-shell" data-header-shell>
        <?php require __DIR__ . '/header.php'; ?>
        </div>
        <?php if ($showTopNavigation) : ?>
            <?php require __DIR__ . '/topnav.php'; ?>
        <?php endif; ?>

        <main id="reskin-main" class="reskin-main" tabindex="-1"><?php if ($reskinDesign) echo '<div class="mc-page-container">'; ?>

            <?php if ($showHeroSection) : ?>
                <?php
                $reskinHeroHtml = (string) reskin_i18n_include_page('hero', false, true);
                $reskinHeroModel = [
                    'content_html' => $reskinHeroHtml,
                    'image_url' => 'https://www.cesnet.cz/gimg/default/2/8/4/284-580-326.jpg',
                    'image_alt' => 'MetaCentrum infrastructure',
                ];
                // Left keeps its sidebar menu on the first screen, so its hero sits beside it (below).
                if ($reskinLayout !== 'left') reskin_render_partial($reskinDesign ? 'design-hero' : 'hero', $reskinHeroModel); ?>
            <?php endif; ?>

            <?php if ($showUpdatesSection || $showUpdatesInline) : ?>
                <?php
                // Standalone showcases use demos; homepages read live headings.
                // Empty full-width feeds remain empty, while inline feeds use demos.
                $reskinUpdatesModel = reskin_home_updates_model($reskinCurrentLang, $isStart, $showUpdatesInline);
                ?>
            <?php endif; ?>
            <?php if ($showUpdatesSection) : ?>
                <?php reskin_render_partial($reskinDesign ? 'design-updates' : 'updates', $reskinUpdatesModel); ?>
            <?php endif; ?>

            <?php if ($showMetricsSection || $showStoriesSection || $showMetricsInline || $showStoriesInline) : ?>
                <?php $reskinHomeModels = reskin_home_feature_models($reskinCurrentLang); ?>
            <?php endif; ?>
            <?php if ($showMetricsSection) : ?>
                <?php reskin_render_partial($reskinDesign ? 'design-metrics' : 'metrics', $reskinHomeModels['metrics']); ?>
            <?php endif; ?>
            <?php if ($showStoriesSection) : ?>
                <?php reskin_render_partial($reskinDesign ? 'design-stories' : 'stories', $reskinHomeModels['stories'], ['track_id' => 'reskinStoryTrack']); ?>
            <?php endif; ?>

            <div class="<?php if (!$reskinDesign) echo 'container-xl '; ?>reskin-content">
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
                        <?php if ($showHeroSection && $reskinLayout === 'left') reskin_render_partial('hero', $reskinHeroModel, ['inline' => true]); ?>
                        <?php if ($showUpdatesInline) : ?>
                            <?php reskin_render_partial($reskinDesign ? 'design-updates' : 'updates', $reskinUpdatesModel, [
                                'inline' => true,
                                'stacked' => $reskinLayout === 'left',
                            ]); ?>
                        <?php endif; ?>

                        <?php if ($showMetricsInline) : ?>
                            <?php reskin_render_partial($reskinDesign ? 'design-metrics' : 'metrics', $reskinHomeModels['metrics'], ['inline' => true]); ?>
                        <?php endif; ?>

                        <?php if ($showStoriesInline) : ?>
                            <?php reskin_render_partial($reskinDesign ? 'design-stories' : 'stories', $reskinHomeModels['stories'], [
                                'inline' => true,
                                'track_id' => 'reskinStoryTrackInline',
                            ]); ?>
                        <?php endif; ?>

                        <?php html_msgarea(); ?>
                        <?php echo $reskinContext['outdated_notice']; // trusted plugin HTML (inc/translation.php) ?>

                        <?php if ($reskinFallbackNotice && $reskinDesign) : ?>
                            <?php reskin_render_partial('design-alert', ['title' => $reskinFallbackNotice]); ?>
                        <?php elseif ($reskinFallbackNotice) : ?>
                            <div class="alert alert-warning reskin-i18n-fallback" role="status">
                                <?php echo hsc($reskinFallbackNotice); ?>
                            </div>
                        <?php endif; ?>

                        <?php
                        ob_start();
                        tpl_content($ACT !== 'show');
                        $reskinContentHtml = (string) ob_get_clean();
                        if ($ACT === 'show' && empty($INFO['exists']) && $INPUT->server->str('REMOTE_USER') === '') {
                            $reskinContentHtml = reskin_missing_page_html(); // native text asks visitors to create the page
                        }
                        $reskinTocHtml = '';
                        // The homepage TOC would list only the wiki text below the home sections, so it has none.
                        if ($ACT === 'show' && !$isStart && !preg_match('/^(?:cs|en):(?:variants:news|outages:start)$/', (string) $ID)) {
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
                                        $reskinContext['variant'],
                                        isset($reskinHeroHtml) && str_contains($reskinHeroHtml, '<h1')
                                    )); ?>
                                    <?php tpl_includeFile('pagefooter.html'); ?>
                                </div>

                                <div class="reskin-page-info">
                                    <?php echo reskin_page_info_html($reskinCurrentLang); ?>
                                </div>

                                <nav class="reskin-pagetools" aria-label="<?php echo hsc(reskin_i18n_t('page_tools')); ?>">
                                    <ul class="list-inline mb-0">
                                        <?php echo reskin_page_tools_html(); ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php if ($reskinDesign) echo '</div>'; ?></main>

        <?php require __DIR__ . '/footer.php'; ?>
    </div>

    <div class="no"><?php tpl_indexerWebBug() ?></div>
    <div id="screen__mode" class="no"></div>

    <?php if ($showSidebarOffcanvas) : ?>
        <div class="offcanvas offcanvas-start reskin-offcanvas<?php echo ' reskin-offcanvas--' . ($reskinLayout === 'left' ? 'top' : $reskinLayout) . '-cesnet'; ?>" tabindex="-1" id="reskinSidebar" aria-labelledby="reskinSidebarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="reskinSidebarLabel"><?php echo hsc(reskin_i18n_t('navigation')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="<?php echo hsc(reskin_i18n_t('close')); ?>"></button>
            </div>
            <div class="offcanvas-body">
                <?php $reskinSidebarInstance = 'offcanvas'; ?>
                <?php require __DIR__ . '/sidebar.php'; ?>
                <div class="reskin-mobile-preferences" data-header-preferences-mobile></div>
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
