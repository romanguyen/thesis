<?php
if (!defined('DOKU_INC')) die();
/** @var array{labels:array<string,string>,layouts:list<array>,styles:list<array>} $model Empty layouts hide that group (MetaCentrum style). */
?>
<details class="reskin-header-preferences position-relative" data-header-preferences>
    <summary class="reskin-preferences-toggle align-items-center gap-2 justify-content-center rounded fw-semibold" aria-label="<?php echo hsc($model['labels']['preferences']); ?>" title="<?php echo hsc($model['labels']['preferences']); ?>">
        <i class="bi bi-sliders" aria-hidden="true"></i>
    </summary>
    <div class="reskin-preferences-panel position-absolute" role="group" aria-label="<?php echo hsc($model['labels']['layout_switcher']); ?>">
        <?php foreach (['layouts' => 'preference_layout', 'styles' => 'preference_style'] as $group => $label) : ?>
            <?php if ($model[$group] === []) continue; ?>
            <p class="reskin-preferences-label fw-semibold"><?php echo hsc($model['labels'][$label]); ?></p>
            <div class="reskin-preferences-layouts d-grid reskin-preferences-group--<?php echo $group; ?>">
                <?php foreach ($model[$group] as $choice) : ?>
                    <a class="reskin-layout-btn d-inline-flex align-items-center text-decoration-none<?php echo $choice['current'] ? ' is-current' : ''; ?>" href="<?php echo hsc($choice['url']); ?>" title="<?php echo hsc($choice['title']); ?>" <?php if ($choice['current']) echo 'aria-current="page"'; ?>>
                        <i class="bi <?php echo hsc($choice['icon']); ?>" aria-hidden="true"></i>
                        <span><?php echo hsc($choice['title']); ?></span>
                        <?php if ($choice['current']) : ?><i class="bi bi-check-lg reskin-preferences-check ms-auto" aria-hidden="true"></i><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</details>
