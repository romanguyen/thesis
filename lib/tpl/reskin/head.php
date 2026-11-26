<?php
if (!defined('DOKU_INC')) die();

require_once __DIR__ . '/inc/bootstrap.php';
/** @var array $reskinContext Prepared once by main; shared with header.php. */
$reskinLangTargets = $reskinContext['language_targets'];
?>
<head>
    <meta charset="utf-8" />
    <title><?php echo hsc(reskin_document_title()); ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="referrer" content="no-referrer" />
    <?php if ($reskinContext['variant']['style'] !== 'metacentrum') : ?>
        <?php // Stored colour mode before first paint; js/theme.js runs after the body and only handles the toggle. ?>
        <script>try{var t=localStorage.getItem("reskin-theme");if(t==="dark"||t==="light")document.documentElement.setAttribute("data-bs-theme",t)}catch(e){}</script>
    <?php endif; ?>
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

    <?php if ($reskinContext['variant']['style'] !== 'metacentrum') : ?><link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin /><?php endif; ?>

    <?php foreach (reskin_stylesheet_assets($reskinContext['variant']) as $reskinAsset) : ?>
        <link rel="stylesheet" href="<?php echo hsc($reskinAsset['url']); ?>"
            <?php if (isset($reskinAsset['integrity'])) : ?>integrity="<?php echo hsc($reskinAsset['integrity']); ?>"<?php endif; ?>
            <?php if (isset($reskinAsset['crossorigin'])) : ?>crossorigin="<?php echo hsc($reskinAsset['crossorigin']); ?>"<?php endif; ?>
        />
    <?php endforeach; ?>
    <?php if ($reskinContext['variant']['style'] === 'metacentrum') : ?>
        <?php // Paint the sidebar shell first instead of the native fallback; a failed load only unblocks rendering. ?>
        <script type="module" blocking="render" src="<?php echo hsc(reskin_local_asset('vendor/metacentrum/mc-elements.js')['url']); ?>"></script>
    <?php endif; ?>
    <?php if ($ACT === 'show' && empty($_SERVER['REMOTE_USER'])) : ?>
        <script defer src="<?php echo hsc(reskin_local_asset('js/analytics.js')['url']); ?>"></script>
    <?php endif; ?>
</head>
