<?php
if (!defined('DOKU_INC')) die();

require_once __DIR__ . '/i18n.php';

$footerContent = reskin_i18n_include_page('footer', false, true);
?>
<footer class="reskin-footer">
    <div class="container-xl py-5">
        <?php if ($footerContent) : ?>
            <div class="reskin-footer-content">
                <?php echo $footerContent; ?>
            </div>
        <?php else : ?>
            <div class="row g-4">
                <div class="col-12 col-lg-3">
                    <a class="reskin-brand d-flex align-items-center text-decoration-none" href="<?php echo hsc(reskin_i18n_link('start')); ?>">
                        <span class="reskin-brand-mark">
                            <img class="reskin-brand-logo" src="<?php echo tpl_basedir(); ?>img/metacentrum_RGB.svg" alt="MetaCentrum logo">
                        </span>
                    </a>
                    <p class="mt-3 text-muted"><?php echo hsc(reskin_i18n_t('footer_tagline')); ?></p>
                </div>
                <div class="col-6 col-lg-3">
                    <h5 class="reskin-footer-title"><?php echo hsc(reskin_i18n_t('quick_links')); ?></h5>
                    <ul class="list-unstyled reskin-footer-list">
                        <li><a href="https://www.cesnet.cz" target="_blank" rel="noopener">CESNET</a></li>
                        <li><a href="https://pki.cesnet.cz" target="_blank" rel="noopener">CESNET PKI</a></li>
                        <li><a href="https://www.egi.eu" target="_blank" rel="noopener">EGI.eu</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-3">
                    <h5 class="reskin-footer-title"><?php echo hsc(reskin_i18n_t('contact')); ?></h5>
                    <address class="reskin-footer-text">
                        <div>CESNET, z. s. p. o</div>
                        <div>Zikova 4, 160 00 Praha 6</div>
                        <a href="mailto:info@cesnet.cz">info@cesnet.cz</a>
                    </address>
                </div>
                <div class="col-12 col-lg-3">
                    <h5 class="reskin-footer-title"><?php echo hsc(reskin_i18n_t('user_support')); ?></h5>
                    <div class="reskin-footer-text">
                        <div>Tel: +420 234 680 222</div>
                        <div>GSM: +420 602 252 531</div>
                        <a href="mailto:support@cesnet.cz">support@cesnet.cz</a>
                    </div>
                </div>
            </div>
            <div class="reskin-footer-bottom mt-4 pt-4">
                <div class="d-flex flex-column flex-md-row gap-2 justify-content-between">
                    <span>&copy; 1991-2026 CESNET, z. s. p. o.</span>
                    <span><?php echo hsc(reskin_i18n_t('last_changed')); ?>: Tue Jan 5 13:46:28 CET 2026</span>
                </div>
            </div>
        <?php endif; ?>
    </div>
</footer>
