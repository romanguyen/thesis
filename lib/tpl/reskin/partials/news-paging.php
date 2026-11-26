<?php
if (!defined('DOKU_INC')) die();
/** @var array{current:int,total:int,pages:list<array{number:int,url:string,current:bool}>,prev:string,next:string,design:bool,labels:array{paging:string,newer:string,older:string}} $model News archive pages. */
$labels = $model['labels'];
?>
<?php if ($model['design']) : // MetaCentrum maps native `.pagenav a` onto its ghost buttons ?>
    <nav class="pagenav reskin-news-paging" aria-label="<?php echo hsc($labels['paging']); ?>">
        <?php if ($model['prev'] !== '') : ?><a href="<?php echo hsc($model['prev']); ?>" rel="prev"><?php echo hsc($labels['newer']); ?></a><?php endif; ?>
        <?php foreach ($model['pages'] as $page) : ?>
            <?php if ($page['current']) : ?>
                <span class="reskin-news-paging-current" aria-current="page"><?php echo (int) $page['number']; ?></span>
            <?php else : ?>
                <a href="<?php echo hsc($page['url']); ?>"><?php echo (int) $page['number']; ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($model['next'] !== '') : ?><a href="<?php echo hsc($model['next']); ?>" rel="next"><?php echo hsc($labels['older']); ?></a><?php endif; ?>
    </nav>
<?php else : ?>
    <nav class="reskin-news-paging" aria-label="<?php echo hsc($labels['paging']); ?>">
        <ul class="pagination flex-wrap">
            <li class="page-item<?php if ($model['prev'] === '') echo ' disabled'; ?>">
                <?php if ($model['prev'] !== '') : ?><a class="page-link" href="<?php echo hsc($model['prev']); ?>" rel="prev"><?php echo hsc($labels['newer']); ?></a><?php else : ?><span class="page-link"><?php echo hsc($labels['newer']); ?></span><?php endif; ?>
            </li>
            <?php foreach ($model['pages'] as $page) : ?>
                <li class="page-item<?php if ($page['current']) echo ' active'; ?>">
                    <a class="page-link" href="<?php echo hsc($page['url']); ?>"<?php if ($page['current']) echo ' aria-current="page"'; ?>><?php echo (int) $page['number']; ?></a>
                </li>
            <?php endforeach; ?>
            <li class="page-item<?php if ($model['next'] === '') echo ' disabled'; ?>">
                <?php if ($model['next'] !== '') : ?><a class="page-link" href="<?php echo hsc($model['next']); ?>" rel="next"><?php echo hsc($labels['older']); ?></a><?php else : ?><span class="page-link"><?php echo hsc($labels['older']); ?></span><?php endif; ?>
            </li>
        </ul>
    </nav>
<?php endif; ?>
