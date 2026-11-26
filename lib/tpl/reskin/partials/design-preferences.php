<?php
if (!defined('DOKU_INC')) die();
/** @var array{labels:array<string,string>,styles:list<array>} $model Style choices only; this style brings its own frame. */
?>
<details class="reskin-header-preferences" data-header-preferences>
    <summary class="mc-btn cds--btn cds--btn--sm cds--layout--size-sm cds--btn--ghost"><?php echo hsc($model['labels']['preferences']); ?></summary>
    <div class="reskin-preferences-panel mc-card" role="group" aria-label="<?php echo hsc($model['labels']['preferences']); ?>">
        <div class="mc-card__header"><p class="mc-card__title reskin-preferences-label"><?php echo hsc($model['labels']['preference_style']); ?></p></div>
        <div class="reskin-preferences-group--styles">
            <?php foreach ($model['styles'] as $choice) : ?>
                <a class="reskin-layout-btn mc-btn mc-btn--full-width cds--btn cds--btn--md cds--layout--size-md cds--btn--ghost" href="<?php echo hsc($choice['url']); ?>" <?php if ($choice['current']) echo 'aria-current="page"'; ?>><?php echo hsc($choice['title']); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</details>
