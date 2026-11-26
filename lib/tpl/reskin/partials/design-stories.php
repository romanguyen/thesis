<?php
if (!defined('DOKU_INC')) die();
/** @var array{title:string,description:string,archive_url:string,archive_label:string,cta_label:string,cards:array<int,array{date:string,year:string,title:string,url:string}>} $model */
/** @var array{inline?:bool} $options */
if ($model['cards'] === []) return;
?>
<section class="reskin-stories reskin-section<?php echo empty($options['inline']) ? '' : ' reskin-stories--inline'; ?>" aria-label="<?php echo hsc($model['title']); ?>">
    <div class="reskin-section-head">
        <div>
            <h2><?php echo hsc($model['title']); ?></h2>
            <p><?php echo hsc($model['description']); ?></p>
        </div>
        <a class="mc-btn cds--btn cds--btn--sm cds--layout--size-sm cds--btn--ghost" href="<?php echo hsc($model['archive_url']); ?>"><?php echo hsc($model['archive_label']); ?></a>
    </div>
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        <?php foreach ($model['cards'] as $card) : ?>
            <div class="col">
                <article class="mc-card reskin-story-item">
                    <div class="mc-card__header"><h3 class="mc-card__title"><?php echo hsc($card['title']); ?></h3></div>
                    <p><?php echo hsc($card['date'] . '/' . $card['year']); ?></p>
                    <a class="mc-btn cds--btn cds--btn--sm cds--layout--size-sm cds--btn--tertiary" href="<?php echo hsc($card['url']); ?>" aria-label="<?php echo hsc($model['cta_label'] . ': ' . $card['title']); ?>"><?php echo hsc($model['cta_label']); ?></a>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>
