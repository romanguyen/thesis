<?php
if (!defined('DOKU_INC')) die();

require_once __DIR__ . '/i18n.php';

global $conf, $ID;

$sidebarHtml = reskin_i18n_include_page($conf['sidebar'], false, true);
if (!$sidebarHtml) return;

$normalizeId = function (string $pageId): string {
    $pageId = trim(cleanID($pageId), ':');
    return $pageId;
};

$currentBaseId = $normalizeId((string) $ID);

$doc = new DOMDocument();
libxml_use_internal_errors(true);
$doc->loadHTML('<?xml encoding="utf-8"?>' . $sidebarHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
libxml_clear_errors();

$xpath = new DOMXPath($doc);

$headingNodes = $xpath->query('//h1|//h2|//h3|//h4|//h5|//h6');
foreach ($headingNodes as $heading) {
    if ($heading instanceof DOMElement && $heading->parentNode) {
        $heading->parentNode->removeChild($heading);
    }
}

$comments = $xpath->query('//comment()');
foreach ($comments as $comment) {
    if ($comment->parentNode) {
        $comment->parentNode->removeChild($comment);
    }
}

$emptyDivs = $xpath->query('//div[not(*) and normalize-space(.)=""]');
foreach ($emptyDivs as $div) {
    if ($div instanceof DOMElement && $div->parentNode) {
        $div->parentNode->removeChild($div);
    }
}

$menuRoot = null;
$candidateLists = $xpath->query('//ul[li]');
foreach ($candidateLists as $candidate) {
    if (!($candidate instanceof DOMElement)) continue;
    if ($xpath->query('.//a[@href]', $candidate)->length === 0) continue;
    $menuRoot = $candidate;
    break;
}

if (!($menuRoot instanceof DOMElement)) return;

$buildMenuItems = function (DOMElement $list, int $depth = 0) use (&$buildMenuItems, $xpath, $normalizeId, $currentBaseId): array {
    $items = [];

    $liNodes = $xpath->query('./li', $list);
    foreach ($liNodes as $li) {
        if (!($li instanceof DOMElement)) continue;

        $link = null;
        $linkSelectors = [
            './div[contains(concat(" ", normalize-space(@class), " "), " li ")]/a[1]',
            './a[1]',
            './/a[1]',
        ];

        foreach ($linkSelectors as $selector) {
            $candidate = $xpath->query($selector, $li)->item(0);
            if ($candidate instanceof DOMElement) {
                $link = $candidate;
                break;
            }
        }

        if (!($link instanceof DOMElement)) continue;

        $title = trim((string) preg_replace('/\s+/u', ' ', (string) $link->textContent));
        if ($title === '') continue;

        $wikiId = trim((string) $link->getAttribute('data-wiki-id'));
        $href = trim((string) $link->getAttribute('href'));
        if ($wikiId !== '') {
            $href = wl($wikiId, reskin_variant_url_params(), false, '&');
        }
        if ($href === '') continue;

        $linkBaseId = $wikiId !== '' ? $normalizeId($wikiId) : '';
        $isCurrent = $linkBaseId !== '' && $linkBaseId === $currentBaseId;
        $isParent = $linkBaseId !== '' && strpos($currentBaseId, $linkBaseId . ':') === 0;

        $children = [];
        if ($depth === 0) {
            $subList = $xpath->query('./ul[1]', $li)->item(0);
            if ($subList instanceof DOMElement) {
                $children = $buildMenuItems($subList, $depth + 1);
            }
        }

        $items[] = [
            'title' => $title,
            'href' => $href,
            'is_current' => $isCurrent,
            'is_parent' => $isParent,
            'children' => $children,
        ];
    }

    return $items;
};

$topItems = $buildMenuItems($menuRoot, 0);
if (empty($topItems)) return;

$hasTopLevelCurrent = false;
foreach ($topItems as $topItem) {
    if (!empty($topItem['is_current'])) {
        $hasTopLevelCurrent = true;
        break;
    }
}
?>
<nav class="reskin-topnav navbar navbar-expand-md" data-topnav-overflow aria-label="<?php echo hsc(reskin_i18n_t('navigation')); ?>">
    <div class="container-xl">
        <button
            class="navbar-toggler reskin-topnav-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#reskinTopnavMenu"
            aria-controls="reskinTopnavMenu"
            aria-expanded="false"
            aria-label="<?php echo hsc(reskin_i18n_t('toggle_navigation')); ?>"
        >
            <i class="bi bi-list" aria-hidden="true"></i>
            <span><?php echo hsc(reskin_i18n_t('navigation')); ?></span>
        </button>

        <div class="collapse navbar-collapse" id="reskinTopnavMenu" data-topnav-collapse>
            <ul class="navbar-nav reskin-topnav-list" data-topnav-list>
                <?php foreach ($topItems as $idx => $item) : ?>
                    <?php
                    $hasChildren = !empty($item['children']);
                    $isActive = $item['is_current'] || (!$hasTopLevelCurrent && $item['is_parent']);
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
                        <?php echo hsc(reskin_i18n_t('more_navigation')); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end reskin-topnav-dropdown reskin-topnav-more-menu" aria-labelledby="reskinTopnavMore" data-topnav-more-menu></ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
