<?php
if (!defined('DOKU_INC')) die();
/** @var array{content_html:string,entries:array,design:bool,labels:array<string,string>} $model See reskin_page_content_model(). */
?>
<?php echo $model['content_html']; ?>
<?php foreach ($model['entries'] as $entry) : ?>
    <?php reskin_render_partial($model['design'] ? 'design-news-entry' : 'news-entry', ['article' => $entry, 'labels' => $model['labels']]); ?>
<?php endforeach; ?>
