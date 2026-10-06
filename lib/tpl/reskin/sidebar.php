<?php
if (!defined('DOKU_INC')) die();
require_once __DIR__ . '/inc/bootstrap.php';
?>
<div class="reskin-nav-tree">
    <?php echo reskin_current_sidebar_navigation(isset($reskinSidebarInstance) ? (string) $reskinSidebarInstance : 'sidebar'); ?>
</div>
