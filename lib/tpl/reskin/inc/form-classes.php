<?php
if (!defined('DOKU_INC')) die();

use dokuwiki\Form\Form;

/** Core forms that get Bootstrap classes; the header's QUICKSEARCH belongs to the header markup. */
const RESKIN_FORMS = ['LOGIN', 'REGISTER', 'RESENDPWD', 'UPDATEPROFILE', 'PROFILEDELETE', 'EDIT', 'CONFLICT', 'DRAFT', 'SEARCH', 'SEARCHMEDIA', 'RECENT', 'REVISIONS', 'SUBSCRIBE', 'UPLOAD'];

/** CESNET only: MetaCentrum maps native forms in its own stylesheet. Call before any form is rendered. */
function reskin_register_form_classes(): void
{
    global $EVENT_HANDLER;
    foreach (RESKIN_FORMS as $form) {
        $EVENT_HANDLER->register_hook('FORM_' . $form . '_OUTPUT', 'BEFORE', null, 'reskin_form_classes');
    }
}

/** FORM_*_OUTPUT: adds classes only; names, ids, values, order and hidden fields stay native. */
function reskin_form_classes(object $event): void
{
    $form = $event->data;
    if (!$form instanceof Form) return;
    $submit = $event->name === 'FORM_PROFILEDELETE_OUTPUT' ? 'btn btn-danger' : 'btn btn-primary';
    for ($i = 0; $i < $form->elementCount(); $i++) {
        $element = $form->getElementAt($i);
        $class = match ($element->getType()) {
            'text', 'password', 'email', 'textarea', 'file' => $element->attr('id') === 'wiki__text' ? 'form-control font-monospace' : 'form-control',
            'dropdown' => 'form-select',
            'checkbox', 'radio' => 'form-check-input',
            'button' => $element->attr('type') === 'submit' ? $submit : 'btn btn-outline-secondary',
            default => '',
        };
        if ($class === '') continue;
        if ($class === $submit) $submit = 'btn btn-outline-secondary'; // one main action per form
        // Not addClass(): on an input it also puts the class on the wrapping label.
        $element->attr('class', trim($element->attr('class') . ' ' . $class));
    }
}
