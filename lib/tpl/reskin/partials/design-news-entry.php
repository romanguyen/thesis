<?php
if (!defined('DOKU_INC')) die();
/** @var array{article:array{title:string,date:string,date_iso:string,url:string,body?:string,author?:string},labels:array{eyebrow:string,read:string}} $model One Card of the news start page with the article's date, author and text; the article itself is an ordinary wiki page. */
$article = $model['article'];
$author = $article['author'] ?? '';
$ghost = 'mc-btn cds--btn cds--btn--sm cds--layout--size-sm cds--btn--ghost';
?>
<article class="reskin-news-article mc-card reskin-news-article--archive">
    <div class="mc-card__header">
        <h2 class="mc-card__title"><a class="reskin-news-heading-link cds--link" href="<?php echo hsc($article['url']); ?>"><?php echo hsc($article['title']); ?></a></h2>
        <div class="mc-card__actions">
            <a class="<?php echo $ghost; ?>" href="<?php echo hsc($article['url']); ?>" aria-label="<?php echo hsc($model['labels']['read'] . ': ' . $article['title']); ?>"><?php echo hsc($model['labels']['read']); ?></a>
        </div>
    </div>
    <?php if ($article['date'] !== '' || $author !== '') : ?>
        <div class="reskin-news-meta">
            <?php if ($article['date'] !== '') : ?><time datetime="<?php echo hsc($article['date_iso']); ?>"><?php echo hsc($article['date']); ?></time><?php endif; ?>
            <?php if ($author !== '') : ?><span class="reskin-news-author"><?php echo hsc($author); ?></span><?php endif; ?>
        </div>
    <?php endif; ?>
    <?php $body = reskin_news_body_html($article['body'] ?? ''); // native rendering stays at the body slot ?>
    <?php if ($body !== '') : ?>
        <div class="reskin-news-body"><?php echo $body; ?></div>
    <?php endif; ?>
</article>
