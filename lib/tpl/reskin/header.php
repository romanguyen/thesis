<?php
if (!defined('DOKU_INC')) die();

require_once __DIR__ . '/inc/bootstrap.php';

/** @var array $reskinContext Prepared once by main; shared with head.php. */
$reskinHeader = reskin_header_model($reskinContext);
?>
<header class="reskin-header">
    <a class="reskin-skip" href="#reskin-main"><?php echo hsc($reskinHeader['labels']['skip_to_content']); ?></a>

    <div class="reskin-header-bar">
        <div class="container-xl">
            <div class="d-flex align-items-center justify-content-between gap-3 py-3">
                <a class="reskin-brand d-flex align-items-center text-decoration-none" href="<?php echo hsc($reskinHeader['brand_url']); ?>">
                    <img class="reskin-brand-logo reskin-brand-logo--header" src="<?php echo hsc($reskinHeader['logo_url']); ?>" alt="MetaCentrum logo">
                </a>

                <div class="d-flex align-items-center gap-2 reskin-header-actions">
                    <div class="reskin-header-meta d-flex align-items-center gap-2">
                        <div class="reskin-lang-switcher" role="group" aria-label="<?php echo hsc($reskinHeader['labels']['language_switcher']); ?>">
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

                    <div class="reskin-search" data-search-modal>
                        <?php tpl_searchform(); ?>
                        <button class="btn reskin-search-close" type="button" data-search-close aria-label="<?php echo hsc($reskinHeader['labels']['close_search']); ?>">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                        </button>
                    </div>
                    <button class="btn reskin-search-toggle reskin-control-icon" type="button" data-search-open aria-label="<?php echo hsc($reskinHeader['labels']['open_search']); ?>">
                        <i class="bi bi-search" aria-hidden="true"></i>
                    </button>
                    <button
                        class="btn reskin-theme-toggle reskin-control-icon"
                        type="button"
                        aria-pressed="false"
                        data-theme-toggle
                        title="<?php echo hsc($reskinHeader['labels']['toggle_theme']); ?>"
                    >
                        <span class="reskin-theme-icon reskin-theme-icon-sun" aria-hidden="true">
                            <i class="bi bi-sun"></i>
                        </span>
                        <span class="reskin-theme-icon reskin-theme-icon-moon" aria-hidden="true">
                            <i class="bi bi-moon"></i>
                        </span>
                        <span class="visually-hidden"><?php echo hsc($reskinHeader['labels']['toggle_theme']); ?></span>
                    </button>

                    <div class="reskin-layout-switcher" role="group" aria-label="<?php echo hsc($reskinHeader['labels']['layout_switcher']); ?>">
                        <?php foreach ($reskinHeader['layouts'] as $layoutLink) : ?>
                            <a
                                class="reskin-layout-btn<?php echo $layoutLink['current'] ? ' is-current' : ''; ?>"
                                href="<?php echo hsc($layoutLink['url']); ?>"
                                title="<?php echo hsc($layoutLink['title']); ?>"
                                <?php if ($layoutLink['current']) echo 'aria-current="page"'; ?>
                            >
                                <i class="bi <?php echo hsc($layoutLink['icon']); ?>" aria-hidden="true"></i>
                                <span class="reskin-layout-label"><?php echo hsc($layoutLink['label']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <a class="reskin-nav-link d-none d-sm-inline-flex" href="https://www.cesnet.cz" target="_blank" rel="noopener">CESNET</a>
                </div>
            </div>
        </div>
    </div>
</header>
