<?php
if (!defined('DOKU_INC')) die();
/** @var array{label:string,cards:array<int,array>} $model Prepared update-card models; see reskin_home_updates_model(). */
/** @var array{inline?:bool} $options */
?>
<section class="reskin-updates reskin-section<?php echo empty($options['inline']) ? '' : ' reskin-updates--inline'; ?>" aria-label="<?php echo hsc($model['label']); ?>">
    <div class="row g-4">
        <?php foreach ($model['cards'] as $card) : ?>
            <div class="col-12 col-xxl-6">
                <section class="mc-card mc-card--flush reskin-update-card">
                    <div class="mc-card__header">
                        <h2 class="mc-card__title"><?php echo hsc($card['title']); ?></h2>
                        <div class="mc-card__actions"><a class="mc-btn cds--btn cds--btn--sm cds--layout--size-sm cds--btn--ghost" href="<?php echo hsc($card['archive_url']); ?>"><?php echo hsc($card['archive_label']); ?></a></div>
                    </div>
                    <?php if ($card['items'] !== []) : ?>
                        <div class="mc-data-table">
                            <div class="cds--data-table-container"><div class="cds--data-table-content">
                                <table class="cds--data-table cds--data-table--md">
                                    <thead><tr>
                                        <th scope="col" class="reskin-update-date"><div class="cds--table-header-label"><?php echo hsc(reskin_i18n_t('table_date')); ?></div></th>
                                        <th scope="col"><div class="cds--table-header-label"><?php echo hsc(reskin_i18n_t('table_title')); ?></div></th>
                                    </tr></thead>
                                    <tbody>
                                        <?php foreach ($card['items'] as $item) : ?>
                                            <tr>
                                                <td class="reskin-update-date">
                                                    <?php if (!empty($item['date']) && !empty($item['end_date'])) : ?>
                                                        <time datetime="<?php echo hsc($item['date_iso']); ?>"><?php echo hsc($item['date_range_start']); ?></time><?php echo hsc($item['date_range_separator']); ?><time datetime="<?php echo hsc($item['end_date_iso']); ?>"><?php echo hsc($item['end_date']); ?></time>
                                                    <?php elseif (!empty($item['date'])) : ?>
                                                        <time datetime="<?php echo hsc($item['date_iso']); ?>"><?php echo hsc($item['date']); ?></time>
                                                    <?php endif; ?>
                                                </td>
                                                <td><a class="cds--link reskin-update-item-title" href="<?php echo hsc($item['url']); ?>"><?php echo hsc($item['title']); ?></a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div></div>
                        </div>
                    <?php else : ?>
                        <?php reskin_render_partial('design-empty-state', ['title' => $card['empty_label']]); ?>
                    <?php endif; ?>
                </section>
            </div>
        <?php endforeach; ?>
    </div>
</section>
