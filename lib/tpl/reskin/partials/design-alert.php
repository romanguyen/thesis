<?php
if (!defined('DOKU_INC')) die();
/** @var array{title:string} $model Warning Alert: plain text title, Carbon WarningFilled icon. */
?>
<div role="status" class="mc-alert cds--inline-notification cds--inline-notification--low-contrast cds--inline-notification--warning cds--inline-notification--hide-close-button reskin-i18n-fallback">
    <div class="cds--inline-notification__details">
        <svg fill="currentColor" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" class="cds--inline-notification__icon"><path d="M10,1c-5,0-9,4-9,9s4,9,9,9s9-4,9-9S15,1,10,1z M9.2,5h1.5v7H9.2V5z M10,16c-0.6,0-1-0.4-1-1s0.4-1,1-1 s1,0.4,1,1S10.6,16,10,16z"></path><path d="M9.2,5h1.5v7H9.2V5z M10,16c-0.6,0-1-0.4-1-1s0.4-1,1-1s1,0.4,1,1S10.6,16,10,16z" opacity="0"></path></svg>
        <div class="cds--inline-notification__text-wrapper"><div class="cds--inline-notification__title"><?php echo hsc($model['title']); ?></div></div>
    </div>
</div>
