<?php
if (!defined('DOKU_INC')) die();

global $ID, $INPUT, $lang;
?>
<header class="reskin-header">
    <a class="reskin-skip" href="#reskin-main">Skip to content</a>

    <nav class="navbar navbar-expand-lg reskin-nav" aria-label="Primary">
        <div class="container-xl">
            <div
                class="offcanvas offcanvas-end offcanvas-lg reskin-offcanvas reskin-nav-offcanvas"
                tabindex="-1"
                id="reskinNavbar"
                aria-labelledby="reskinNavbarLabel"
            >
                <div class="offcanvas-header d-lg-none">
                    <h5 class="offcanvas-title" id="reskinNavbarLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="reskin-menu me-lg-auto">
                        <?php
                        $menuHtml = tpl_include_page('menu-main', false, true);
                        $menuItems = [];

                        if ($menuHtml) {
                            $doc = new DOMDocument();
                            libxml_use_internal_errors(true);
                            $doc->loadHTML('<?xml encoding="utf-8"?>' . $menuHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                            libxml_clear_errors();

                            $xpath = new DOMXPath($doc);
                            $headings = $xpath->query('//*[self::h1 or self::h2 or self::h3 or self::h4 or self::h5 or self::h6]');

                            foreach ($headings as $heading) {
                                $label = trim($heading->textContent);
                                $href = null;
                                $linkNodes = $xpath->query('.//a', $heading);
                                if ($linkNodes->length > 0) {
                                    $href = $linkNodes->item(0)->getAttribute('href');
                                    $label = trim($linkNodes->item(0)->textContent);
                                }

                                $children = [];
                                $next = $heading->nextSibling;
                                while ($next && !($next instanceof DOMElement)) {
                                    $next = $next->nextSibling;
                                }
                                if ($next instanceof DOMElement) {
                                    $links = $next->getElementsByTagName('a');
                                    foreach ($links as $link) {
                                        $childLabel = trim($link->textContent);
                                        $childHref = $link->getAttribute('href');
                                        if ($childLabel && $childHref) {
                                            $children[] = ['label' => $childLabel, 'href' => $childHref];
                                        }
                                    }
                                }

                                if ($label) {
                                    $menuItems[] = ['label' => $label, 'href' => $href, 'children' => $children];
                                }
                            }
                        }
                        ?>
                        <?php if (!empty($menuItems)) : ?>
                            <ul class="reskin-menu-bar">
                                <?php foreach ($menuItems as $item) : ?>
                                    <li class="reskin-menu-item">
                                        <?php if (!empty($item['href'])) : ?>
                                            <a class="reskin-menu-trigger" href="<?php echo hsc($item['href']); ?>" target="_blank" rel="noopener">
                                                <?php echo hsc($item['label']); ?>
                                            </a>
                                        <?php else : ?>
                                            <button class="reskin-menu-trigger" type="button">
                                                <?php echo hsc($item['label']); ?>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (!empty($item['children'])) : ?>
                                            <div class="reskin-menu-panel" role="menu">
                                                <ul>
                                                    <?php foreach ($item['children'] as $child) : ?>
                                                        <li>
                                                            <a class="reskin-menu-link" href="<?php echo hsc($child['href']); ?>" target="_blank" rel="noopener">
                                                                <?php echo hsc($child['label']); ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <?php tpl_include_page('menu-main', true, true); ?>
                        <?php endif; ?>
                    </div>
                    <div class="reskin-nav-meta d-flex align-items-center gap-3">
                        <a class="reskin-nav-link" href="https://www.metacentrum.cz/en/" target="_blank" rel="noopener">EN</a>
                        <a class="reskin-nav-link" href="https://www.cesnet.cz" target="_blank" rel="noopener">CESNET</a>
                        <?php if ($INPUT->server->str('REMOTE_USER')) : ?>
                            <a class="reskin-nav-link" href="<?php echo wl($ID, ['do' => 'logout', 'sectok' => getSecurityToken()], true, '&'); ?>">
                                <?php echo hsc($lang['btn_logout'] ?? 'Logout'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="reskin-header-bar">
        <div class="container-xl">
            <div class="d-flex align-items-center justify-content-between gap-3 py-3">
                <a class="reskin-brand d-flex align-items-center text-decoration-none" href="<?php echo wl() ?>">
                    <img class="reskin-brand-logo reskin-brand-logo--header" src="<?php echo tpl_basedir(); ?>img/metacentrum_RGB.svg" alt="MetaCentrum logo">
                </a>

                <div class="d-flex align-items-center gap-2">
                    <div class="reskin-search" data-search-modal>
                        <?php tpl_searchform(); ?>
                        <button class="btn reskin-search-close" type="button" data-search-close aria-label="Close search">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                        </button>
                    </div>
                    <button class="btn reskin-search-toggle" type="button" data-search-open aria-label="Open search">
                        <i class="bi bi-search" aria-hidden="true"></i>
                    </button>
                    <button
                        class="btn reskin-theme-toggle"
                        type="button"
                        aria-pressed="false"
                        data-theme-toggle
                        title="Toggle theme"
                    >
                        <span class="reskin-theme-icon reskin-theme-icon-sun" aria-hidden="true">
                            <i class="bi bi-sun"></i>
                        </span>
                        <span class="reskin-theme-icon reskin-theme-icon-moon" aria-hidden="true">
                            <i class="bi bi-moon"></i>
                        </span>
                        <span class="visually-hidden">Toggle theme</span>
                    </button>

                    <button
                        class="navbar-toggler reskin-toggler d-lg-none"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#reskinNavbar"
                        aria-controls="reskinNavbar"
                        aria-expanded="false"
                        aria-label="Toggle navigation"
                    >
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>
