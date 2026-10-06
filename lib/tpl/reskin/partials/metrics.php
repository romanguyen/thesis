<?php
if (!defined('DOKU_INC')) die();
/** @var array{title:string,description:string,cards:array<int,array{title:string,url:string,icon:string}>,highlights:array<int,array{number:string,label:string}>} $model */
/** @var array{inline?:bool} $options */
if ($model['cards'] === []) return;
$inline = $options['inline'] ?? false;
?>
<section class="reskin-metrics<?php echo $inline ? ' reskin-metrics--inline' : ''; ?>" aria-label="<?php echo hsc($model['title']); ?>">
    <?php if (!$inline) : ?><div class="container-xl"><?php endif; ?>
    <div class="reskin-section-head reskin-section-head--metrics">
        <div>
            <h2><?php echo hsc($model['title']); ?></h2>
            <p><?php echo hsc($model['description']); ?></p>
        </div>
    </div>

    <div class="reskin-metric-links" role="list">
        <?php foreach ($model['cards'] as $card) : ?>
            <a class="reskin-metric-link" href="<?php echo hsc($card['url']); ?>" role="listitem">
                <span class="reskin-metric-link-icon"><i class="bi <?php echo hsc($card['icon']); ?>" aria-hidden="true"></i></span>
                <span class="reskin-metric-link-title"><?php echo hsc($card['title']); ?></span>
                <span class="reskin-metric-link-arrow" aria-hidden="true">&#8594;</span>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($model['highlights'] !== []) : ?>
        <div class="reskin-metric-hex-wrap">
            <?php foreach (array_values($model['highlights']) as $index => $metric) : ?>
                <article class="reskin-metric-hex<?php echo $index === 1 ? ' is-offset' : ''; ?>">
                    <span class="reskin-metric-hex-number"><?php echo hsc($metric['number']); ?></span>
                    <span class="reskin-metric-hex-label"><?php echo hsc($metric['label']); ?></span>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (!$inline) : ?></div><?php endif; ?>
</section>
