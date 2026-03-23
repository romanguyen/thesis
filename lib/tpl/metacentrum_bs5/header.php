<?php
if (!defined('DOKU_INC')) die();
?>
<!-- Header -->
<!-- <header id="dokuwiki__header" class="bg-light border-bottom"> -->
<!-- </header> -->

<header>
    <div class="top-bar mt-4">
        <!-- Top Bar -->
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <a class="navbar-brand" href="<?php echo wl() ?>">
                    <img src="<?php echo tpl_basedir() ?>/img/logo.svg"
                        alt="MetaCentrum" height="40" />
                </a>

                <!-- Quick Menu -->
                <div class="d-flex align-items-center gap-3">
                    <span class="quick-menu-text">Rychlá nabídka</span>
                    <button class="quick-menu-btn">
                        <span class="grid-icon">
                            <span class="grid-dot" style="background-color: #e74c3c;"></span>
                            <span class="grid-dot" style="background-color: #ff6b35;"></span>
                            <span class="grid-dot" style="background-color: #2ecc71;"></span>
                            <span class="grid-dot" style="background-color: #f39c12;"></span>
                            <span class="grid-dot" style="background-color: #9b59b6;"></span>
                            <span class="grid-dot" style="background-color: #1abc9c;"></span>
                            <span class="grid-dot" style="background-color: #e67e22;"></span>
                            <span class="grid-dot" style="background-color: #34495e;"></span>
                            <span class="grid-dot" style="background-color: #16a085;"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-4">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">O nás</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Služby</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Akce</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Výzkum a projekty</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Novinky</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Kariéra</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Kontakty</a>
                    </li>
                </ul>
                <!-- <div class="d-flex flex-wrap"> │ -->
                <!--     <?php tpl_include_page('menu-main', 1, 1); ?> │ -->
                <!-- </div> -->

                <!-- Right Side Icons -->
                <div class="d-flex align-items-center gap-3">
                    <button class="icon-btn search-btn">
                        <?php tpl_searchform(); ?>
                    </button>
                    <?php require __DIR__ . '/parts/social_links.php'; ?>
                    <a href="https://www.metacentrum.cz/en/" class="lang-link">EN</a>
                </div>
            </div>
        </div>
    </nav>
</header>