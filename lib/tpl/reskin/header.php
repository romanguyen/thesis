<?php
if (!defined('DOKU_INC')) die();

require_once __DIR__ . '/i18n.php';

global $ID, $INPUT, $lang;

$reskinLangTargets = reskin_i18n_language_targets();
$reskinVariant = reskin_variant_context();
$reskinCleanUrl = wl($ID, ['layout' => 'clean'], false, '&');
$reskinLeftUrl = wl($ID, ['layout' => 'left'], false, '&');
$reskinTopUrl = wl($ID, [], false, '&');
?>
<header class="reskin-header">
    <a class="reskin-skip" href="#reskin-main"><?php echo hsc(reskin_i18n_t('skip_to_content')); ?></a>

    <div class="reskin-header-bar">
        <div class="container-xl">
            <div class="d-flex align-items-center justify-content-between gap-3 py-3">
                <a class="reskin-brand d-flex align-items-center text-decoration-none" href="<?php echo hsc(reskin_i18n_link('start')); ?>">
                    <img class="reskin-brand-logo reskin-brand-logo--header" src="<?php echo tpl_basedir(); ?>img/metacentrum_RGB.svg" alt="MetaCentrum logo">
                </a>

                <div class="d-flex align-items-center gap-2 reskin-header-actions">
                    <div class="reskin-header-meta d-flex align-items-center gap-2">
                        <div class="reskin-lang-switcher" role="group" aria-label="<?php echo hsc(reskin_i18n_t('language_switcher')); ?>">
                            <?php foreach ($reskinLangTargets as $code => $target) : ?>
                                <?php
                                $classes = 'reskin-lang-btn';
                                if ($target['current']) $classes .= ' is-current';
                                if (!$target['available']) $classes .= ' is-unavailable';
                                ?>
                                <a
                                    class="<?php echo hsc($classes); ?>"
                                    href="<?php echo hsc($target['url']); ?>"
                                    lang="<?php echo hsc($code); ?>"
                                    hreflang="<?php echo hsc($code); ?>"
                                    title="<?php echo hsc($target['available'] ? $target['label'] : reskin_i18n_t('translation_missing')); ?>"
                                    <?php if ($target['current']) echo 'aria-current="page"'; ?>
                                >
                                    <?php echo strtoupper($code); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($INPUT->server->str('REMOTE_USER')) : ?>
                            <a class="reskin-nav-link" href="<?php echo wl($ID, reskin_variant_url_params(['do' => 'logout', 'sectok' => getSecurityToken()]), true, '&'); ?>">
                                <?php echo hsc($lang['btn_logout'] ?? 'Logout'); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="reskin-search" data-search-modal>
                        <?php tpl_searchform(); ?>
                        <button class="btn reskin-search-close" type="button" data-search-close aria-label="<?php echo hsc(reskin_i18n_t('close_search')); ?>">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                        </button>
                    </div>
                    <button class="btn reskin-search-toggle reskin-control-icon" type="button" data-search-open aria-label="<?php echo hsc(reskin_i18n_t('open_search')); ?>">
                        <i class="bi bi-search" aria-hidden="true"></i>
                    </button>
                    <button
                        class="btn reskin-theme-toggle reskin-control-icon"
                        type="button"
                        aria-pressed="false"
                        data-theme-toggle
                        title="<?php echo hsc(reskin_i18n_t('toggle_theme')); ?>"
                    >
                        <span class="reskin-theme-icon reskin-theme-icon-sun" aria-hidden="true">
                            <i class="bi bi-sun"></i>
                        </span>
                        <span class="reskin-theme-icon reskin-theme-icon-moon" aria-hidden="true">
                            <i class="bi bi-moon"></i>
                        </span>
                        <span class="visually-hidden"><?php echo hsc(reskin_i18n_t('toggle_theme')); ?></span>
                    </button>

                    <div class="reskin-layout-switcher" role="group" aria-label="<?php echo hsc(reskin_i18n_t('layout_switcher')); ?>">
                        <a
                            class="reskin-layout-btn<?php echo $reskinVariant['layout'] === 'left' ? ' is-current' : ''; ?>"
                            href="<?php echo hsc($reskinLeftUrl); ?>"
                            title="<?php echo hsc(reskin_i18n_t('layout_left_hint')); ?>"
                            <?php if ($reskinVariant['layout'] === 'left') echo 'aria-current="page"'; ?>
                        >
                            <i class="bi bi-layout-sidebar" aria-hidden="true"></i>
                            <span class="reskin-layout-label"><?php echo hsc(reskin_i18n_t('layout_left')); ?></span>
                        </a>
                        <a
                            class="reskin-layout-btn<?php echo $reskinVariant['layout'] === 'top' ? ' is-current' : ''; ?>"
                            href="<?php echo hsc($reskinTopUrl); ?>"
                            title="<?php echo hsc(reskin_i18n_t('layout_top_hint')); ?>"
                            <?php if ($reskinVariant['layout'] === 'top') echo 'aria-current="page"'; ?>
                        >
                            <i class="bi bi-menu-button-wide" aria-hidden="true"></i>
                            <span class="reskin-layout-label"><?php echo hsc(reskin_i18n_t('layout_top')); ?></span>
                        </a>
                        <a
                            class="reskin-layout-btn<?php echo $reskinVariant['layout'] === 'clean' ? ' is-current' : ''; ?>"
                            href="<?php echo hsc($reskinCleanUrl); ?>"
                            title="<?php echo hsc(reskin_i18n_t('layout_clean_hint')); ?>"
                            <?php if ($reskinVariant['layout'] === 'clean') echo 'aria-current="page"'; ?>
                        >
                            <i class="bi bi-border-all" aria-hidden="true"></i>
                            <span class="reskin-layout-label"><?php echo hsc(reskin_i18n_t('layout_clean')); ?></span>
                        </a>
                    </div>

                    <a class="reskin-nav-link d-none d-sm-inline-flex" href="https://www.cesnet.cz" target="_blank" rel="noopener">CESNET</a>
                </div>
            </div>
        </div>
    </div>
</header>
