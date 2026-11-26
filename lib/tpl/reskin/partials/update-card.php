<?php
if (!defined('DOKU_INC')) die();
/** @var array{title:string,icon:string,archive_url:string,archive_label:string,empty_label:string,items:array<int,array{title:string,url:string,date:string,date_iso:string,end_date:string,end_date_iso:string,date_range_start:string,date_range_separator:string}>} $model */
?>
<article class="reskin-update-card card h-100">
    <header class="reskin-update-header d-flex align-items-center justify-content-between flex-wrap">
        <h2 class="reskin-update-title d-inline-flex align-items-center m-0">
            <i class="bi <?php echo hsc($model['icon']); ?>" aria-hidden="true"></i>
            <?php echo hsc($model['title']); ?>
        </h2>
        <a class="reskin-update-more d-inline-flex align-items-center text-decoration-none fw-medium text-nowrap" href="<?php echo hsc($model['archive_url']); ?>">
            <?php echo hsc($model['archive_label']); ?>
            <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </a>
    </header>

    <?php if ($model['items'] !== []) : ?>
        <ul class="reskin-update-list list-group list-group-flush border-0 m-0">
            <?php foreach ($model['items'] as $item) : ?>
                <li class="list-group-item">
                    <a class="reskin-update-row<?php echo empty($item['date']) ? ' reskin-update-row--undated' : ''; ?>" href="<?php echo hsc($item['url']); ?>">
                        <?php if (!empty($item['date']) && !empty($item['end_date'])) : ?>
                            <span class="reskin-update-date d-inline-flex reskin-update-date--range"><span class="reskin-update-date-range"><time datetime="<?php echo hsc($item['date_iso']); ?>"><?php echo hsc($item['date_range_start']); ?></time><?php echo hsc($item['date_range_separator']); ?><time datetime="<?php echo hsc($item['end_date_iso']); ?>"><?php echo hsc($item['end_date']); ?></time></span></span>
                        <?php elseif (!empty($item['date'])) : ?>
                            <time class="reskin-update-date d-inline-flex" datetime="<?php echo hsc($item['date_iso']); ?>"><?php echo hsc($item['date']); ?></time>
                        <?php endif; ?>
                        <span class="reskin-update-item-title fw-semibold"><?php echo hsc($item['title']); ?></span>
                        <i class="bi bi-chevron-right reskin-update-chevron" aria-hidden="true"></i>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p class="reskin-update-empty m-0"><?php echo hsc($model['empty_label']); ?></p>
    <?php endif; ?>
</article>
