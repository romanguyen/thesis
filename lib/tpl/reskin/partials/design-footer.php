<?php
if (!defined('DOKU_INC')) die();
/** @var array{content_html:string,tagline:string,support_label:string,copyright:string,asset_base:string} $model */
?>
<div class="reskin-design-footer" data-design-footer>
    <mc-footer>
        <div slot="info" class="reskin-design-footer-info">
            <p><?php echo hsc($model['tagline']); ?></p>
            <?php if ($model['content_html'] !== '') : ?>
                <div class="reskin-footer-wiki"><?php echo $model['content_html']; ?></div>
                <p><a href="https://pki.cesnet.cz">CESNET PKI</a> · <a href="https://www.egi.eu">EGI.eu</a></p>
            <?php else : ?>
                <p>CESNET, z. s. p. o. · Zikova 4, 160 00 Praha 6 · <a href="mailto:info@cesnet.cz">info@cesnet.cz</a> · <a href="https://pki.cesnet.cz">CESNET PKI</a> · <a href="https://www.egi.eu">EGI.eu</a></p>
                <p><?php echo hsc($model['support_label']); ?>: <a href="mailto:support@cesnet.cz">support@cesnet.cz</a> · <a href="tel:+420234680222">+420 234 680 222</a> · <a href="tel:+420602252531">+420 602 252 531</a></p>
            <?php endif; ?>
        </div>
        <div class="reskin-design-footer-fallback" aria-label="MetaCentrum, CESNET, e-INFRA CZ">
            <a href="https://metacentrum.cz"><img src="<?php echo hsc($model['asset_base']); ?>reference-metacentrum-logo.svg" alt="MetaCentrum"></a>
            <a href="https://www.cesnet.cz"><img src="<?php echo hsc($model['asset_base']); ?>reference-cesnet-logo.svg" alt="CESNET"></a>
            <a href="https://www.e-infra.cz"><img src="<?php echo hsc($model['asset_base']); ?>reference-einfra-logo.svg" alt="e-INFRA CZ"></a>
        </div>
        <span slot="copyright"><?php echo hsc($model['copyright']); ?></span>
    </mc-footer>
</div>
