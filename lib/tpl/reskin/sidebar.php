<?php
if (!defined('DOKU_INC')) die();

global $conf, $ID;
?>
<div class="reskin-nav-tree">
    <?php
    $sidebarHtml = tpl_include_page($conf['sidebar'], false, true);
    if (!$sidebarHtml) {
        return;
    }

    $currentId = $ID;

    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="utf-8"?>' . $sidebarHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $xpath = new DOMXPath($doc);
    $links = $xpath->query('//a[@data-wiki-id]');

    $addClass = function (DOMElement $node, string $class) {
        $existing = trim($node->getAttribute('class'));
        $classes = $existing ? preg_split('/\s+/', $existing) : [];
        if (!in_array($class, $classes, true)) {
            $classes[] = $class;
        }
        $node->setAttribute('class', trim(implode(' ', $classes)));
    };

    foreach ($links as $link) {
        $id = $link->getAttribute('data-wiki-id');
        if (!$id) continue;

        if ($id === $currentId) {
            $addClass($link, 'is-active');
            $li = $link->parentNode;
            while ($li && $li->nodeName !== 'li') {
                $li = $li->parentNode;
            }
            if ($li instanceof DOMElement) {
                $addClass($li, 'is-active');
            }
        } elseif (strpos($currentId, $id . ':') === 0) {
            $li = $link->parentNode;
            while ($li && $li->nodeName !== 'li') {
                $li = $li->parentNode;
            }
            if ($li instanceof DOMElement) {
                $addClass($li, 'is-active-parent');
            }
        }
    }

    $listItems = $xpath->query('//li[ul]');
    $toggleIndex = 0;
    $instance = 'sidebar';
    if (isset($reskinSidebarInstance)) {
        $instance = preg_replace('/[^a-z0-9_-]+/i', '', (string) $reskinSidebarInstance) ?: 'sidebar';
    }
    foreach ($listItems as $li) {
        if (!$li instanceof DOMElement) continue;

        $wrapper = null;
        foreach ($li->childNodes as $child) {
            if (!$child instanceof DOMElement) continue;
            $classAttr = ' ' . $child->getAttribute('class') . ' ';
            if (strpos($classAttr, ' li ') !== false) {
                $wrapper = $child;
                break;
            }
        }
        if ($wrapper instanceof DOMElement) {
            $addClass($wrapper, 'reskin-nav-item');
        }

        $toggleIndex++;
        $toggleId = 'reskin-toggle-' . $instance . '-' . $toggleIndex;
        $input = $doc->createElement('input');
        $input->setAttribute('type', 'checkbox');
        $input->setAttribute('class', 'reskin-nav-toggle-input');
        $input->setAttribute('id', $toggleId);

        $liClasses = ' ' . $li->getAttribute('class') . ' ';
        if (strpos($liClasses, ' is-active ') !== false || strpos($liClasses, ' is-active-parent ') !== false) {
            $input->setAttribute('checked', 'checked');
        }

        $li->insertBefore($input, $li->firstChild);

        if ($wrapper instanceof DOMElement) {
            $label = $doc->createElement('label');
            $label->setAttribute('class', 'reskin-nav-toggle');
            $label->setAttribute('for', $toggleId);
            $label->setAttribute('aria-label', 'Toggle section');
            $wrapper->appendChild($label);
        }
    }

    $output = $doc->saveHTML();
    $output = preg_replace('/^<\?xml.*?\?>/i', '', $output);
    echo $output;
    ?>
</div>
