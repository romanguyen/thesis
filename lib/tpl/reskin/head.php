<?php
if (!defined('DOKU_INC')) die();

require_once __DIR__ . '/i18n.php';
$reskinLangTargets = reskin_i18n_language_targets();
?>
<head>
    <meta charset="utf-8" />
    <?php
    $reskinTitle = tpl_pagetitle(null, true);
    if ($reskinTitle !== '') {
        $reskinTitle = str_replace(':', ' - ', $reskinTitle);
        $reskinTitle = utf8_ucfirst($reskinTitle);
    }
    ?>
    <title><?php echo $reskinTitle; ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <?php tpl_metaheaders() ?>
    <?php echo tpl_favicon(['favicon', 'mobile']) ?>
    <?php tpl_includeFile('meta.html') ?>

    <?php foreach ($reskinLangTargets as $code => $target) : ?>
        <?php if ($target['available']) : ?>
            <link rel="alternate" hreflang="<?php echo hsc($code); ?>" href="<?php echo hsc($target['url_abs']); ?>" />
        <?php endif; ?>
    <?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?php echo hsc(reskin_i18n_link('start', [], true, RESKIN_I18N_PRIMARY_LANG)); ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;600&display=swap"
        rel="stylesheet"
    />

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
        integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+"
        crossorigin="anonymous"
    />

    <?php
    $reskinCssFiles = [
        'reskin.css',
        'base.css',
        'home.css',
        'components.css',
        'layout.css',
        'responsive.css',
    ];
    $reskinCssVersion = 1;
    foreach ($reskinCssFiles as $reskinCssFile) {
        $mtime = @filemtime(__DIR__ . '/css/' . $reskinCssFile);
        if ($mtime && $mtime > $reskinCssVersion) {
            $reskinCssVersion = $mtime;
        }
    }
    ?>
    <link rel="stylesheet" href="<?php echo tpl_basedir(); ?>css/reskin.css?v=<?php echo $reskinCssVersion ?: '1'; ?>" />
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
