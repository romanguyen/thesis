<?php
if (!defined('DOKU_INC')) die();
/** @var array{title:string,icon:string,archive_url:string,archive_label:string,empty_label:string,items:array<int,array{title:string,url:string,date:string,date_iso:string}>} $model */
?>
<article class="reskin-update-card">
    <header class="reskin-update-header">
        <h2 class="reskin-update-title">
            <i class="bi <?php echo hsc($model['icon']); ?>" aria-hidden="true"></i>
            <?php echo hsc($model['title']); ?>
        </h2>
        <a class="reskin-update-more" href="<?php echo hsc($model['archive_url']); ?>">
            <?php echo hsc($model['archive_label']); ?>
            <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </a>
    </header>

    <?php if ($model['items'] !== []) : ?>
        <ul class="reskin-update-list">
            <?php foreach ($model['items'] as $item) : ?>
                <li>
                    <a class="reskin-update-row<?php echo empty($item['date']) ? ' reskin-update-row--undated' : ''; ?>" href="<?php echo hsc($item['url']); ?>">
                        <?php if (!empty($item['date'])) : ?>
                            <time class="reskin-update-date" datetime="<?php echo hsc($item['date_iso']); ?>"><?php echo hsc($item['date']); ?></time>
                        <?php endif; ?>
                        <span class="reskin-update-item-title"><?php echo hsc($item['title']); ?></span>
                        <i class="bi bi-chevron-right reskin-update-chevron" aria-hidden="true"></i>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p class="reskin-update-empty"><?php echo hsc($model['empty_label']); ?></p>
    <?php endif; ?>
</article>
