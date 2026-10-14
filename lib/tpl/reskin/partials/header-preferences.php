<?php
if (!defined('DOKU_INC')) die();
/** @var array{labels:array<string,string>,layouts:list<array>} $model */
?>
<details class="reskin-header-preferences" data-header-preferences>
    <summary class="reskin-preferences-toggle" aria-label="<?php echo hsc($model['labels']['preferences']); ?>" title="<?php echo hsc($model['labels']['preferences']); ?>">
        <i class="bi bi-sliders" aria-hidden="true"></i>
    </summary>
    <div class="reskin-preferences-panel" role="group" aria-label="<?php echo hsc($model['labels']['layout_switcher']); ?>">
        <p class="reskin-preferences-label"><?php echo hsc($model['labels']['preference_layout']); ?></p>
        <div class="reskin-preferences-layouts">
            <?php foreach ($model['layouts'] as $layoutLink) : ?>
                <a class="reskin-layout-btn<?php echo $layoutLink['current'] ? ' is-current' : ''; ?>" href="<?php echo hsc($layoutLink['url']); ?>" title="<?php echo hsc($layoutLink['title']); ?>" <?php if ($layoutLink['current']) echo 'aria-current="page"'; ?>>
                    <i class="bi <?php echo hsc($layoutLink['icon']); ?>" aria-hidden="true"></i>
                    <span><?php echo hsc($layoutLink['title']); ?></span>
                    <?php if ($layoutLink['current']) : ?><i class="bi bi-check-lg reskin-preferences-check" aria-hidden="true"></i><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</details>
