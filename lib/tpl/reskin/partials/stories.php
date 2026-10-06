<?php
if (!defined('DOKU_INC')) die();
/** @var array{title:string,description:string,archive_url:string,archive_label:string,cta_label:string,prev_label:string,next_label:string,cards:array<int,array{date:string,year:string,title:string,url:string,image:string}>} $model */
/** @var array{inline?:bool,track_id:string} $options Track/control IDs are explicit to preserve existing instances. */
if ($model['cards'] === []) return;
$inline = $options['inline'] ?? false;
$trackId = $options['track_id'];
?>
<section class="reskin-stories<?php echo $inline ? ' reskin-stories--inline' : ''; ?>" aria-label="<?php echo hsc($model['title']); ?>">
    <?php if (!$inline) : ?><div class="container-xl"><?php endif; ?>
    <div class="reskin-section-head reskin-section-head--stories">
        <div>
            <h2><?php echo hsc($model['title']); ?></h2>
            <p><?php echo hsc($model['description']); ?></p>
        </div>
        <a class="reskin-stories-archive" href="<?php echo hsc($model['archive_url']); ?>">
            <?php echo hsc($model['archive_label']); ?>
        </a>
    </div>

    <div class="reskin-story-carousel" data-story-slider>
        <div class="reskin-story-viewport">
            <div class="reskin-story-track" id="<?php echo hsc($trackId); ?>">
                <?php foreach ($model['cards'] as $card) : ?>
                    <article class="reskin-story-item">
                        <a class="reskin-story-card" href="<?php echo hsc($card['url']); ?>">
                            <span class="reskin-story-image" style="background-image: url('<?php echo hsc($card['image']); ?>');"></span>
                            <span class="reskin-story-date"><?php echo hsc($card['date'] . '/' . $card['year']); ?></span>
                            <span class="reskin-story-title"><?php echo hsc($card['title']); ?></span>
                            <span class="reskin-story-cta">
                                <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                <?php echo hsc($model['cta_label']); ?>
                            </span>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (count($model['cards']) > 1) : ?>
            <div class="reskin-story-controls">
                <button class="btn reskin-story-control" type="button" data-story-prev aria-controls="<?php echo hsc($trackId); ?>" aria-label="<?php echo hsc($model['prev_label']); ?>">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                </button>
                <button class="btn reskin-story-control" type="button" data-story-next aria-controls="<?php echo hsc($trackId); ?>" aria-label="<?php echo hsc($model['next_label']); ?>">
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>
    <?php if (!$inline) : ?></div><?php endif; ?>
</section>
