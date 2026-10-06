<?php
if (!defined('DOKU_INC')) die();
/** @var array{label:string,cards:array<int,array>} $model Prepared update-card models. */
/** @var array{inline?:bool,stacked?:bool} $options Inline placement; stack the narrow left-layout cards. */
$inline = $options['inline'] ?? false;
$stacked = $inline && ($options['stacked'] ?? false);
?>
<section class="reskin-updates<?php echo $inline ? ' reskin-updates--inline' : ''; ?>" aria-label="<?php echo hsc($model['label']); ?>">
    <?php if (!$inline) : ?><div class="container-xl"><?php endif; ?>
    <div class="row g-4">
        <?php foreach ($model['cards'] as $card) : ?>
            <div class="<?php echo $stacked ? 'col-12' : 'col-12 col-xl-6'; ?>">
                <?php reskin_render_partial('update-card', $card); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (!$inline) : ?></div><?php endif; ?>
</section>
