<?php
if (!defined('DOKU_INC')) die();
/** @var array{article:array{title:string,date:string,date_iso:string,url:string},labels:array{eyebrow:string,read:string}} $model One Card of the news start page; the article itself is an ordinary wiki page. */
$article = $model['article'];
$ghost = 'mc-btn cds--btn cds--btn--sm cds--layout--size-sm cds--btn--ghost';
?>
<article class="reskin-news-article mc-card reskin-news-article--archive">
    <div class="mc-card__header">
        <h2 class="mc-card__title"><a class="reskin-news-heading-link cds--link" href="<?php echo hsc($article['url']); ?>"><?php echo hsc($article['title']); ?></a></h2>
        <div class="mc-card__actions">
            <a class="<?php echo $ghost; ?>" href="<?php echo hsc($article['url']); ?>" aria-label="<?php echo hsc($model['labels']['read'] . ': ' . $article['title']); ?>"><?php echo hsc($model['labels']['read']); ?></a>
        </div>
    </div>
    <?php if ($article['date'] !== '') : ?>
        <div class="reskin-news-meta"><time datetime="<?php echo hsc($article['date_iso']); ?>"><?php echo hsc($article['date']); ?></time></div>
    <?php endif; ?>
</article>
