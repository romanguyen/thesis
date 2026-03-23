<?php
/**
 * Reskin template for DokuWiki
 */

if (!defined('DOKU_INC')) die();

global $ID, $ACT, $conf, $lang;

$hasSidebar = page_findnearest($conf['sidebar']);
$showSidebar = $hasSidebar && ($ACT === 'show');
$isStart = ($ID === $conf['start']);
?>
<!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="no-js">
<?php require __DIR__ . '/head.php'; ?>

<body class="reskin-body">
    <div id="dokuwiki__site" class="reskin-site <?php echo tpl_classes(); ?> <?php echo $showSidebar ? 'has-sidebar' : ''; ?>">
        <?php require __DIR__ . '/header.php'; ?>

        <main id="reskin-main" class="reskin-main">
            <?php if ($isStart && $ACT === 'show') : ?>
                <section class="reskin-hero" aria-label="Hero">
                    <div class="container-xl">
                        <div class="row align-items-center g-4">
                            <div class="col-12 col-lg-7">
                                <div class="reskin-hero-content">
                                    <?php tpl_include_page('hero', true, true); ?>
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

            <?php if ($showSidebar) : ?>
                <div class="container-xl d-lg-none reskin-sidebar-mobile">
                    <button
                        class="btn reskin-sidebar-toggle"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#reskinSidebar"
                        aria-controls="reskinSidebar"
                    >
                        Browse sections
                    </button>
                </div>
            <?php endif; ?>

            <div class="container-xl reskin-content">
                <div class="row g-4">
                    <?php if ($showSidebar) : ?>
                        <aside class="col-lg-3 d-none d-lg-block">
                            <div class="reskin-sidebar sticky-top">
                                <?php $reskinSidebarInstance = 'desktop'; ?>
                                <?php require __DIR__ . '/sidebar.php'; ?>
                            </div>
                        </aside>
                    <?php endif; ?>

                    <div class="<?php echo $showSidebar ? 'col-12 col-lg-9' : 'col-12'; ?>">
                        <?php html_msgarea(); ?>

                        <div class="reskin-page">
                            <?php tpl_includeFile('pageheader.html'); ?>
                            <?php tpl_content(); ?>
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

    <?php if ($showSidebar) : ?>
        <div class="offcanvas offcanvas-start reskin-offcanvas" tabindex="-1" id="reskinSidebar" aria-labelledby="reskinSidebarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="reskinSidebarLabel">Navigation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
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
    <?php $reskinJsVersion = @filemtime(__DIR__ . '/js/reskin.js'); ?>
    <script src="<?php echo tpl_basedir(); ?>js/reskin.js?v=<?php echo $reskinJsVersion ?: '1'; ?>"></script>
</body>
</html>
