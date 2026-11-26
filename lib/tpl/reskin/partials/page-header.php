<?php
if (!defined('DOKU_INC')) die();
/** @var array{heading_html:string,breadcrumbs:list<array{label:string,url:string}>,content_html:string} $model PageHeader; components.scss gives the native H1 its title style. */
?>
<header class="mc-page-header">
    <?php if ($model['breadcrumbs'] !== []) : ?>
        <nav aria-label="<?php echo hsc(reskin_i18n_current_lang() === 'en' ? 'Breadcrumb' : 'Drobečková navigace'); ?>">
            <ol class="mc-page-header__breadcrumbs">
                <?php foreach ($model['breadcrumbs'] as $crumb) : ?>
                    <li class="mc-page-header__breadcrumb"><?php
                        // No literal whitespace between the slash pseudo-element and label.
                        echo $crumb['url'] !== ''
                            ? '<a class="mc-page-header__breadcrumb-link" href="' . hsc($crumb['url']) . '">' . hsc($crumb['label']) . '</a>'
                            : '<span aria-current="page">' . hsc($crumb['label']) . '</span>';
                    ?></li>
                <?php endforeach; ?>
            </ol>
        </nav>
    <?php endif; ?>
    <div class="mc-page-header__row"><?php echo $model['heading_html']; ?></div>
    <?php if ($model['content_html'] !== '') : ?>
        <div class="mc-page-header__content"><?php echo $model['content_html']; ?></div>
    <?php endif; ?>
</header>
