<?php
if (!defined('DOKU_INC')) die();

require_once __DIR__ . '/inc/bootstrap.php';
$topModel = reskin_current_top_navigation();
if ($topModel['items'] === []) return;
?>
<nav class="reskin-topnav navbar navbar-expand-md" data-topnav-overflow aria-label="<?php echo hsc($topModel['labels']['navigation']); ?>">
    <div class="container-xl">
        <button
            class="navbar-toggler reskin-topnav-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#reskinTopnavMenu"
            aria-controls="reskinTopnavMenu"
            aria-expanded="false"
            aria-label="<?php echo hsc($topModel['labels']['toggle']); ?>"
        >
            <i class="bi bi-list" aria-hidden="true"></i>
            <span><?php echo hsc($topModel['labels']['navigation']); ?></span>
        </button>

        <div class="collapse navbar-collapse" id="reskinTopnavMenu" data-topnav-collapse>
            <ul class="navbar-nav reskin-topnav-list" data-topnav-list>
                <?php foreach ($topModel['items'] as $idx => $item) : ?>
                    <?php
                    $hasChildren = !empty($item['children']);
                    $isActive = $item['is_active'];
                    $itemClasses = 'nav-link reskin-topnav-link';
                    if ($isActive) $itemClasses .= ' is-active';
                    ?>
                    <?php if ($hasChildren) : ?>
                        <li class="nav-item dropdown reskin-topnav-item<?php echo $isActive ? ' is-active' : ''; ?>" data-topnav-item>
                            <a
                                class="<?php echo hsc($itemClasses); ?> dropdown-toggle"
                                href="<?php echo hsc($item['href']); ?>"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                id="reskinTopnavDropdown<?php echo $idx; ?>"
                            >
                                <?php echo hsc($item['title']); ?>
                            </a>
                            <ul class="dropdown-menu reskin-topnav-dropdown" aria-labelledby="reskinTopnavDropdown<?php echo $idx; ?>">
                                <?php foreach ($item['children'] as $child) : ?>
                                    <?php $childClasses = 'dropdown-item reskin-topnav-subitem'; ?>
                                    <?php if ($child['is_current']) $childClasses .= ' is-active'; ?>
                                    <li>
                                        <a
                                            class="<?php echo hsc($childClasses); ?>"
                                            href="<?php echo hsc($child['href']); ?>"
                                            <?php if ($child['is_current']) echo 'aria-current="page"'; ?>
                                        >
                                            <?php echo hsc($child['title']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else : ?>
                        <li class="nav-item reskin-topnav-item<?php echo $isActive ? ' is-active' : ''; ?>" data-topnav-item>
                            <a
                                class="<?php echo hsc($itemClasses); ?>"
                                href="<?php echo hsc($item['href']); ?>"
                                <?php if ($item['is_current']) echo 'aria-current="page"'; ?>
                            >
                                <?php echo hsc($item['title']); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>

                <li class="nav-item dropdown reskin-topnav-more d-none" data-topnav-more>
                    <a
                        class="nav-link reskin-topnav-link dropdown-toggle"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        aria-expanded="false"
                        id="reskinTopnavMore"
                    >
                        <?php echo hsc($topModel['labels']['more']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end reskin-topnav-dropdown reskin-topnav-more-menu" aria-labelledby="reskinTopnavMore" data-topnav-more-menu></ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
