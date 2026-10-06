<?php
if (!defined('DOKU_INC')) die();

require_once __DIR__ . '/inc/bootstrap.php';
/** @var array $reskinContext Prepared once by main; shared with header.php. */
$reskinLangTargets = $reskinContext['language_targets'];
?>
<head>
    <meta charset="utf-8" />
    <?php
    $reskinTitle = reskin_page_title();
    if ($reskinContext['news_article'] !== null) {
        $reskinTitle = $reskinContext['news_article']['title'] . ' · ' . $reskinTitle;
    }
    ?>
    <title><?php echo hsc($reskinTitle); ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <?php tpl_metaheaders() ?>
    <?php echo tpl_favicon(['favicon', 'mobile']) ?>
    <link rel="icon" type="image/svg+xml" href="<?php echo tpl_basedir(); ?>img/favicon.svg" />
    <?php tpl_includeFile('meta.html') ?>

    <?php foreach ($reskinLangTargets as $code => $target) : ?>
        <?php if ($target['available']) : ?>
            <link rel="alternate" hreflang="<?php echo hsc($code); ?>" href="<?php echo hsc($target['url_abs']); ?>" />
        <?php endif; ?>
    <?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?php echo hsc(reskin_i18n_link('start', [], true, RESKIN_I18N_PRIMARY_LANG)); ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <?php foreach (reskin_stylesheet_assets($reskinContext['variant']) as $reskinAsset) : ?>
        <link rel="stylesheet" href="<?php echo hsc($reskinAsset['url']); ?>"
            <?php if (isset($reskinAsset['integrity'])) : ?>integrity="<?php echo hsc($reskinAsset['integrity']); ?>"<?php endif; ?>
            <?php if (isset($reskinAsset['crossorigin'])) : ?>crossorigin="<?php echo hsc($reskinAsset['crossorigin']); ?>"<?php endif; ?>
        />
    <?php endforeach; ?>
    <script>
      (function() {
        var host = window.location.hostname;
        if (host === 'localhost' || host === '127.0.0.1') return;

        var _paq = window._paq = window._paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);

        var u = 'https://metacentrum-matomo.duckdns.org/';
        _paq.push(['setTrackerUrl', u + 'matomo.php']);
        _paq.push(['setSiteId', '1']);

        var d = document;
        var g = d.createElement('script');
        var s = d.getElementsByTagName('script')[0];
        g.async = true;
        g.src = u + 'matomo.js';
        s.parentNode.insertBefore(g, s);
      })();
    </script>
</head>
