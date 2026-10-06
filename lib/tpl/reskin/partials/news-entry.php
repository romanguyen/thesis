<?php
if (!defined('DOKU_INC')) die();
/** @var array{article:array{anchor:string,title:string,body:string,date:string,date_iso:string,author:string,external_url:string,external_title:string,permalink:string},url:string,back_url:string,labels:array{eyebrow:string,external:string,permalink:string,footer:string}} $model */
/** @var array{archive:bool} $options */
$article = $model['article'];
$archive = $options['archive'];
?>
<article class="reskin-news-article <?php echo $archive ? 'reskin-news-article--archive' : 'reskin-news-article--detail'; ?>">
    <header class="reskin-news-article-header">
        <span class="reskin-news-eyebrow"><?php echo hsc($model['labels']['eyebrow']); ?></span>
        <?php if ($archive) : ?>
            <h2 id="<?php echo hsc($article['anchor']); ?>"><a class="reskin-news-heading-link" href="<?php echo hsc($model['url']); ?>"><?php echo hsc($article['title']); ?></a></h2>
        <?php else : ?>
            <h1><?php echo hsc($article['title']); ?></h1>
        <?php endif; ?>
        <?php if ($article['date'] !== '' || $article['author'] !== '') : ?>
            <div class="reskin-news-meta">
                <?php if ($article['date'] !== '') : ?>
                    <time datetime="<?php echo hsc($article['date_iso']); ?>"><?php echo hsc($article['date']); ?></time>
                <?php endif; ?>
                <?php if ($article['author'] !== '') : ?>
                    <span><?php echo hsc($article['author']); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </header>
    <div class="reskin-news-body">
        <?php
        // Native rendering deliberately stays here, after the entry header and before its footer.
        $renderInfo = [];
        echo p_render('xhtml', p_get_instructions($article['body']), $renderInfo);
        ?>
    </div>
    <?php if ($article['external_url'] !== '') : ?>
        <a class="reskin-news-external" href="<?php echo hsc($article['external_url']); ?>">
            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
            <span>
                <span class="reskin-news-external-label"><?php echo hsc($model['labels']['external']); ?></span>
                <span class="reskin-news-external-title"><?php echo hsc($article['external_title']); ?></span>
            </span>
        </a>
    <?php endif; ?>
    <footer class="reskin-news-footer">
        <?php if ($article['permalink'] !== '') : ?>
            <a class="reskin-news-permalink" href="<?php echo hsc($article['permalink']); ?>"><?php echo hsc($model['labels']['permalink']); ?></a>
        <?php endif; ?>
        <a class="reskin-news-back" href="<?php echo hsc($model['back_url']); ?>">
            <?php if (!$archive) : ?><i class="bi bi-arrow-left" aria-hidden="true"></i><?php endif; ?>
            <?php echo hsc($model['labels']['footer']); ?>
            <?php if ($archive) : ?><i class="bi bi-arrow-right" aria-hidden="true"></i><?php endif; ?>
        </a>
    </footer>
</article>
