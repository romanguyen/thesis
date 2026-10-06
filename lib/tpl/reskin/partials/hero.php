<?php
if (!defined('DOKU_INC')) die();
/** @var array{content_html:string,image_url:string,image_alt:string} $model Trusted native HTML; plain image attributes. */
?>
<section class="reskin-hero" aria-label="Hero">
    <div class="container-xl">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-7">
                <div class="reskin-hero-content">
                    <?php echo $model['content_html']; ?>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="reskin-hero-card">
                    <img
                        class="reskin-hero-image"
                        src="<?php echo hsc($model['image_url']); ?>"
                        alt="<?php echo hsc($model['image_alt']); ?>"
                    >
                    <div class="reskin-hero-rings"></div>
                </div>
            </div>
        </div>
    </div>
</section>
