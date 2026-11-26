<?php
if (!defined('DOKU_INC')) die();

require_once __DIR__ . '/inc/bootstrap.php';

/** @var array $reskinContext Prepared once by main; shared with head.php. */
$reskinHeader = reskin_header_model($reskinContext);
?>
<?php if ($reskinHeader['design_layout']) : ?>
    <?php reskin_render_partial('design-header', $reskinHeader); ?>
    <?php return; ?>
<?php endif; ?>
<header class="reskin-header position-relative<?php echo $reskinHeader['navigation_header'] ? ' reskin-header--top' : ''; ?>">
    <a class="reskin-skip position-absolute text-decoration-none" href="#reskin-main"><?php echo hsc($reskinHeader['labels']['skip_to_content']); ?></a>

    <div class="reskin-header-bar position-relative">
        <div class="container-xl">
            <div class="d-flex align-items-center justify-content-between gap-3 py-3 reskin-header-row">
                <a class="reskin-brand d-flex align-items-center text-decoration-none" href="<?php echo hsc($reskinHeader['brand_url']); ?>">
                    <img class="reskin-brand-logo reskin-brand-logo--header" src="<?php echo hsc($reskinHeader['logo_url']); ?>" alt="MetaCentrum logo">
                </a>

                <div class="d-flex align-items-center gap-2 reskin-header-actions">
                    <div class="reskin-header-meta d-flex align-items-center gap-2">
                        <div class="reskin-lang-switcher overflow-hidden" role="group" aria-label="<?php echo hsc($reskinHeader['labels']['language_switcher']); ?>">
                            <?php foreach ($reskinHeader['languages'] as $target) : ?>
                                <a
                                    class="<?php echo hsc($target['class']); ?>"
                                    href="<?php echo hsc($target['url']); ?>"
                                    lang="<?php echo hsc($target['code']); ?>"
                                    hreflang="<?php echo hsc($target['code']); ?>"
                                    title="<?php echo hsc($target['title']); ?>"
                                    <?php if ($target['current']) echo 'aria-current="page"'; ?>
                                >
                                    <?php echo hsc($target['label']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($reskinHeader['logout'] !== null) : ?>
                            <a class="reskin-nav-link" href="<?php echo hsc($reskinHeader['logout']['url']); ?>">
                                <?php echo hsc($reskinHeader['logout']['label']); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="reskin-search" id="reskinSearchDialog" data-search-modal tabindex="-1">
                        <?php tpl_searchform(); ?>
                        <button class="btn reskin-search-close align-items-center justify-content-center" type="button" data-search-close aria-label="<?php echo hsc($reskinHeader['labels']['close_search']); ?>">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                        </button>
                    </div>
                    <button class="btn reskin-search-toggle align-items-center justify-content-center reskin-control-icon" type="button" data-search-open aria-controls="reskinSearchDialog" aria-expanded="false" aria-label="<?php echo hsc($reskinHeader['labels']['open_search']); ?>">
                        <i class="bi bi-search" aria-hidden="true"></i>
                    </button>
                    <button
                        class="btn reskin-theme-toggle d-inline-flex align-items-center position-relative overflow-hidden reskin-control-icon"
                        type="button"
                        aria-pressed="false"
                        data-theme-toggle
                        title="<?php echo hsc($reskinHeader['labels']['toggle_theme']); ?>"
                    >
                        <span class="reskin-theme-icon reskin-theme-icon-sun" aria-hidden="true">
                            <i class="bi bi-sun"></i>
                        </span>
                        <span class="reskin-theme-icon reskin-theme-icon-moon position-absolute" aria-hidden="true">
                            <i class="bi bi-moon"></i>
                        </span>
                        <span class="visually-hidden"><?php echo hsc($reskinHeader['labels']['toggle_theme']); ?></span>
                    </button>

                    <div class="reskin-preferences-home" data-header-preferences-home>
                        <?php reskin_render_partial('header-preferences', $reskinHeader); ?>
                    </div>
                    <?php if ($reskinHeader['navigation_header'] && $reskinHeader['mobile_navigation']) : ?>
                        <button class="btn reskin-header-menu" type="button" data-bs-toggle="offcanvas" data-bs-target="#reskinSidebar" aria-controls="reskinSidebar" aria-expanded="false" aria-label="<?php echo hsc($reskinHeader['labels']['menu']); ?>" data-header-menu>
                            <i class="bi bi-list" aria-hidden="true"></i>
                            <span><?php echo hsc($reskinHeader['labels']['menu']); ?></span>
                        </button>
                    <?php endif; ?>

                    <a class="reskin-nav-link d-none d-sm-inline-flex" href="https://www.cesnet.cz" target="_blank" rel="noopener">CESNET</a>
                </div>
            </div>
        </div>
    </div>
</header>
