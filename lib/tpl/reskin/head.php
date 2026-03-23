<?php
if (!defined('DOKU_INC')) die();
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
    />

    <?php $reskinCssVersion = @filemtime(__DIR__ . '/css/reskin.css'); ?>
    <link rel="stylesheet" href="<?php echo tpl_basedir(); ?>css/reskin.css?v=<?php echo $reskinCssVersion ?: '1'; ?>" />
    <!-- Matomo -->
    <script>
      var _paq = window._paq = window._paq || [];
      /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
      (function() {
        var u="//metacentrum-matomo.duckdns.org/";
        _paq.push(['setTrackerUrl', u+'matomo.php']);
        _paq.push(['setSiteId', '1']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
      })();
    </script>
    <!-- End Matomo Code -->
</head>
