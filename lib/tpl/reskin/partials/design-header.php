<?php
if (!defined('DOKU_INC')) die();
/** @var array $model Native header model; JSON is escaped as an HTML attribute. */
$config = [
    'user' => $model['user'],
    'logout' => $model['logout'],
    'languages' => $model['languages'],
    'labels' => $model['labels'],
];
$ghost = 'mc-btn cds--btn cds--btn--sm cds--layout--size-sm cds--btn--ghost';
?>
<div class="reskin-design-shell" data-design-header data-elements-url="<?php echo hsc($model['elements_url']); ?>" data-design-config="<?php echo hsc(json_encode($config, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)); ?>">
    <mc-header app-title="MetaCentrum" open-menu-label="<?php echo hsc($model['labels']['menu']); ?>" close-menu-label="<?php echo hsc($model['labels']['close']); ?>">
        <a slot="logo" href="<?php echo hsc($model['brand_url']); ?>"><img class="mc-header__logo" src="<?php echo hsc($model['logo_url']); ?>" alt="MetaCentrum"></a>
        <svg slot="action-icon-0" viewBox="0 0 20 20" aria-hidden="true"><path d="m18.03 17.14-4.82-4.82a6.25 6.25 0 1 0-.89.89l4.82 4.82ZM2.5 8.125a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z"/></svg>
        <svg slot="action-icon-1" viewBox="0 0 16 16" aria-hidden="true"><path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1z"/></svg>
    </mc-header>
</div>
<?php // Native fallback: complete without JavaScript or the element bundle; js/design-header.js moves its search and preferences. ?>
<header class="reskin-header">
    <a class="reskin-skip mc-btn cds--btn cds--btn--md cds--layout--size-md cds--btn--primary" href="#reskin-main"><?php echo hsc($model['labels']['skip_to_content']); ?></a>
    <a class="reskin-brand" href="<?php echo hsc($model['brand_url']); ?>"><img class="reskin-brand-logo" src="<?php echo hsc($model['logo_url']); ?>" alt="MetaCentrum"></a>
    <div class="reskin-header-actions">
        <div class="reskin-lang-switcher" role="group" aria-label="<?php echo hsc($model['labels']['language_switcher']); ?>">
            <?php foreach ($model['languages'] as $target) : ?>
                <a class="<?php echo hsc($target['class'] . ' ' . $ghost); ?>" href="<?php echo hsc($target['url']); ?>" lang="<?php echo hsc($target['code']); ?>" hreflang="<?php echo hsc($target['code']); ?>" title="<?php echo hsc($target['title']); ?>" <?php if ($target['current']) echo 'aria-current="page"'; ?>><?php echo hsc($target['label']); ?></a>
            <?php endforeach; ?>
        </div>
        <?php if ($model['logout'] !== null) : ?>
            <a class="<?php echo $ghost; ?>" href="<?php echo hsc($model['logout']['url']); ?>"><?php echo hsc($model['logout']['label']); ?></a>
        <?php endif; ?>
        <div class="reskin-search" id="reskinSearchDialog" data-search-modal tabindex="-1">
            <?php tpl_searchform(); ?>
            <button class="mc-btn cds--btn cds--btn--md cds--layout--size-md cds--btn--ghost" type="button" data-search-close><?php echo hsc($model['labels']['close']); ?></button>
        </div>
        <button type="button" data-search-open hidden aria-controls="reskinSearchDialog" aria-expanded="false" aria-label="<?php echo hsc($model['labels']['open_search']); ?>"></button>
        <?php reskin_render_partial('design-preferences', $model); ?>
    </div>
</header>
