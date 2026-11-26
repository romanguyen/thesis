<?php
if (!defined('DOKU_INC')) die();
/** @var array{labels:array<string,string>,items:list<array>} $model Plain fallback links; js/design-header.js maps them into mc-header. */
?>
<nav class="reskin-topnav" aria-label="<?php echo hsc($model['labels']['navigation']); ?>">
    <ul data-topnav-list>
        <?php foreach ($model['items'] as $item) : ?>
            <li class="reskin-topnav-item<?php echo $item['is_active'] ? ' is-active' : ''; ?>" data-topnav-item>
                <a class="reskin-topnav-link cds--link<?php echo $item['is_active'] ? ' is-active' : ''; ?>" href="<?php echo hsc($item['href']); ?>" <?php if ($item['is_current']) echo 'aria-current="page"'; ?>><?php echo hsc($item['title']); ?></a>
                <?php if (!empty($item['children'])) : ?>
                    <ul class="reskin-topnav-dropdown">
                        <?php foreach ($item['children'] as $child) : ?>
                            <li><a class="cds--link<?php echo $child['is_current'] ? ' is-active' : ''; ?>" href="<?php echo hsc($child['href']); ?>" <?php if ($child['is_current']) echo 'aria-current="page"'; ?>><?php echo hsc($child['title']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
