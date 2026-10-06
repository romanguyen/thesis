<?php
if (!defined('DOKU_INC')) die();
/** @var array{content_html:string,entries:array,archive?:bool,title?:string,anchor?:string,labels?:array<string,string>} $model See reskin_page_content_model(). */
?>
<?php if ($model['entries'] !== []) : ?>
    <?php if ($model['archive']) : ?>
        <h1 class="reskin-news-archive-title" id="<?php echo hsc($model['anchor']); ?>"><?php echo hsc($model['title']); ?></h1>
    <?php endif; ?>
    <?php foreach ($model['entries'] as $entry) : ?>
        <?php reskin_render_partial('news-entry', $entry + ['labels' => $model['labels']], ['archive' => $model['archive']]); ?>
    <?php endforeach; ?>
<?php else : ?>
    <?php echo $model['content_html']; ?>
<?php endif; ?>
