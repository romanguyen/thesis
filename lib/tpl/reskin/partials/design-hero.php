<?php
if (!defined('DOKU_INC')) die();
/** @var array{content_html:string,image_url:string,image_alt:string} $model Trusted native HTML; plain image attributes. */
// PageHeader: the fragment's own H1 is the page title, the rest its content.
preg_match('~^\s*(<h1\b[^>]*>.*?</h1>)?(.*)$~su', $model['content_html'], $parts);
?>
<section class="reskin-hero reskin-section" aria-label="Hero">
    <div class="row align-items-center g-4">
        <div class="col-12 col-lg-7">
            <header class="mc-page-header">
                <div class="mc-page-header__row"><?php echo $parts[1] ?? ''; ?></div>
                <div class="mc-page-header__content"><?php echo $parts[2] ?? ''; ?></div>
            </header>
        </div>
        <div class="col-12 col-lg-5">
            <img class="reskin-hero-image" src="<?php echo hsc($model['image_url']); ?>" alt="<?php echo hsc($model['image_alt']); ?>">
        </div>
    </div>
</section>
