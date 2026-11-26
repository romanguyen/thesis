<?php
if (!defined('DOKU_INC')) die();
/** @var array{title:string,description:string,cards:array<int,array{title:string,url:string,icon:string}>,highlights:array<int,array{number:string,label:string}>} $model */
/** @var array{inline?:bool} $options */
if ($model['cards'] === []) return;
?>
<section class="reskin-metrics reskin-section<?php echo empty($options['inline']) ? '' : ' reskin-metrics--inline'; ?>" aria-label="<?php echo hsc($model['title']); ?>">
    <div class="reskin-section-head">
        <div>
            <h2><?php echo hsc($model['title']); ?></h2>
            <p><?php echo hsc($model['description']); ?></p>
        </div>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-5 g-4">
        <?php foreach ($model['cards'] as $card) : ?>
            <div class="col">
                <a class="mc-card reskin-metric-link" href="<?php echo hsc($card['url']); ?>"><h3 class="mc-card__title"><?php echo hsc($card['title']); ?></h3></a>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ($model['highlights'] !== []) : ?>
        <div class="row row-cols-1 row-cols-sm-3 g-4 reskin-metric-values">
            <?php foreach ($model['highlights'] as $metric) : ?>
                <div class="col">
                    <article class="mc-card">
                        <p class="mc-card__title"><?php echo hsc($metric['number']); ?></p>
                        <p><?php echo hsc($metric['label']); ?></p>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
