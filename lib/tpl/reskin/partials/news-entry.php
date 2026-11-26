<?php
if (!defined('DOKU_INC')) die();
/** @var array{article:array{title:string,date:string,date_iso:string,url:string},labels:array{eyebrow:string,read:string}} $model One card of the news start page; the article itself is an ordinary wiki page. */
$article = $model['article'];
?>
<article class="reskin-news-article reskin-news-article--archive card">
    <header class="reskin-news-article-header">
        <span class="reskin-news-eyebrow d-inline-flex align-items-center fw-bold text-uppercase"><?php echo hsc($model['labels']['eyebrow']); ?></span>
        <h2><a class="reskin-news-heading-link" href="<?php echo hsc($article['url']); ?>"><?php echo hsc($article['title']); ?></a></h2>
        <?php if ($article['date'] !== '') : ?>
            <div class="reskin-news-meta d-flex align-items-center flex-wrap">
                <time datetime="<?php echo hsc($article['date_iso']); ?>"><?php echo hsc($article['date']); ?></time>
            </div>
        <?php endif; ?>
    </header>
    <footer class="reskin-news-footer d-flex align-items-center justify-content-between flex-wrap gap-3">
        <a class="reskin-news-back" href="<?php echo hsc($article['url']); ?>">
            <?php echo hsc($model['labels']['read']); ?>
            <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </a>
    </footer>
</article>
