(function () {
  "use strict";
  var page = document.querySelector('.reskin-layout-clean .reskin-page');
  if (!page || page.dataset.formsReady) return;
  page.dataset.formsReady = 'true';

  page.querySelectorAll('ul.tabs > li > strong').forEach(function (current) { current.setAttribute('aria-current', 'page'); });
  page.querySelectorAll('.pagenav button, .pagenav a').forEach(function (control) {
    if (!control.hasAttribute('aria-label')) control.setAttribute('aria-label', control.textContent.trim());
  });

  // A flat single-select is the reference Dropdown equivalent. Other select APIs keep native UI.
  page.querySelectorAll('select').forEach(function (select, sequence) {
    if (select.multiple || select.size > 1 || select.querySelector('optgroup') || select.hidden || select.dataset.reskinSelect) return;
    var wrapper = document.createElement('span');
    wrapper.className = 'reskin-select cds--dropdown cds--list-box';
    var trigger = document.createElement('button');
    trigger.type = 'button';
    // Native quickselect hides form buttons except its documented .show opt-out.
    trigger.className = 'reskin-select-trigger show cds--list-box__field';
    trigger.setAttribute('role', 'combobox');
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');
    var value = document.createElement('span');
    value.className = 'reskin-select-value cds--list-box__label';
    var arrow = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    arrow.setAttribute('viewBox', '0 0 16 16');
    arrow.setAttribute('aria-hidden', 'true');
    var path = document.createElementNS(arrow.namespaceURI, 'path');
    path.setAttribute('d', 'M8 11 3 6l.7-.7L8 9.6l4.3-4.3.7.7z');
    arrow.append(path);
    var icon = document.createElement('span');
    icon.className = 'cds--list-box__menu-icon';
    icon.append(arrow);
    trigger.append(value, icon);
    var menu = document.createElement('ul');
    menu.className = 'reskin-select-menu cds--list-box__menu';
    menu.id = 'reskin-select-' + sequence;
    value.id = menu.id + '-value';
    menu.hidden = true;
    menu.setAttribute('role', 'listbox');
    if (typeof menu.showPopover === 'function') menu.setAttribute('popover', 'manual');
    trigger.setAttribute('aria-controls', menu.id);
    var label = Array.from(select.labels || []).map(function (node) {
      var copy = node.cloneNode(true);
      copy.querySelectorAll('select, input, button').forEach(function (control) { control.remove(); });
      return copy.textContent.trim();
    }).join(' ');
    var labelIds = select.getAttribute('aria-labelledby') || Array.from(select.labels || []).map(function (node, index) {
      var text = node.querySelector(':scope > span:not(.reskin-select)');
      if (!text) return '';
      if (!text.id) text.id = menu.id + '-label-' + index;
      return text.id;
    }).filter(Boolean).join(' ');
    if (labelIds) trigger.setAttribute('aria-labelledby', labelIds + ' ' + value.id);
    else trigger.setAttribute('aria-label', select.getAttribute('aria-label') || label || select.name);
    if (select.hasAttribute('aria-describedby')) trigger.setAttribute('aria-describedby', select.getAttribute('aria-describedby'));
    if (select.required) trigger.setAttribute('aria-required', 'true');
    menu.setAttribute('aria-label', select.getAttribute('aria-label') || label || select.name);
    var error = document.createElement('span');
    error.className = 'reskin-field-error';
    error.id = menu.id + '-error';
    error.setAttribute('role', 'alert');
    error.hidden = true;
    var active = -1;
    var prefix = '';
    var typedAt = 0;

    function close() {
      if (menu.hasAttribute('popover') && menu.matches(':popover-open')) menu.hidePopover();
      menu.hidden = true;
      trigger.setAttribute('aria-expanded', 'false');
      trigger.removeAttribute('aria-activedescendant');
    }
    function highlight(index) {
      active = index;
      Array.from(menu.children).forEach(function (item, i) { item.classList.toggle('is-highlighted', i === index); });
      var item = menu.children[index];
      if (item) {
        trigger.setAttribute('aria-activedescendant', item.id);
        item.scrollIntoView({ block: 'nearest' });
      }
    }
    function sync() {
      value.textContent = select.selectedOptions[0]?.label || '';
      trigger.disabled = select.matches(':disabled');
      if (select.required) trigger.setAttribute('aria-required', 'true');
      else trigger.removeAttribute('aria-required');
      trigger.setAttribute('aria-invalid', String(select.getAttribute('aria-invalid') === 'true' || select.matches(':user-invalid')));
      if (select.validity.valid) error.hidden = true;
      if (error.hidden) {
        if (select.hasAttribute('aria-describedby')) trigger.setAttribute('aria-describedby', select.getAttribute('aria-describedby'));
        else trigger.removeAttribute('aria-describedby');
      }
      Array.from(menu.children).forEach(function (item, i) {
        item.setAttribute('aria-selected', String(i === select.selectedIndex));
      });
      if (trigger.disabled) close();
    }
    function renderOptions() {
      menu.replaceChildren();
      Array.from(select.options).forEach(function (option, i) {
        var item = document.createElement('li');
        item.id = menu.id + '-' + i;
        item.setAttribute('role', 'option');
        item.setAttribute('aria-disabled', String(option.disabled));
        item.hidden = option.hidden;
        item.className = 'cds--list-box__menu-item';
        var text = document.createElement('div');
        text.className = 'cds--list-box__menu-item__option';
        text.textContent = option.label;
        item.append(text);
        item.addEventListener('pointerdown', function (event) { event.preventDefault(); });
        item.addEventListener('click', function () { choose(i); });
        menu.append(item);
      });
      sync();
    }
    function choose(index) {
      if (select.matches(':disabled') || !select.options[index] || select.options[index].disabled || select.options[index].hidden) return;
      var changed = select.selectedIndex !== index;
      select.selectedIndex = index;
      close();
      sync();
      trigger.focus();
      if (changed) {
        select.dispatchEvent(new Event('input', { bubbles: true }));
        select.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
    function open() {
      if (select.matches(':disabled') || !select.options.length) return;
      renderOptions();
      var rect = trigger.getBoundingClientRect();
      var below = innerHeight - rect.bottom - 8;
      var above = rect.top - 8;
      var height = Math.min(264, Math.max(below, above));
      menu.style.left = Math.max(8, Math.min(rect.left, innerWidth - rect.width - 8)) + 'px';
      menu.style.width = Math.min(rect.width, innerWidth - 16) + 'px';
      menu.style.maxHeight = Math.max(40, height) + 'px';
      menu.style.top = below >= Math.min(264, menu.children.length * 40 + 2) || below >= above ? rect.bottom + 4 + 'px' : 'auto';
      menu.style.bottom = menu.style.top === 'auto' ? innerHeight - rect.top + 4 + 'px' : 'auto';
      menu.hidden = false;
      if (menu.hasAttribute('popover')) menu.showPopover();
      trigger.setAttribute('aria-expanded', 'true');
      highlight(select.selectedIndex);
    }
    function move(step, endpoint) {
      var indices = Array.from(select.options).flatMap(function (option, i) { return option.disabled || option.hidden ? [] : [i]; });
      var position = indices.indexOf(active);
      var next = endpoint === 'first' ? 0 : endpoint === 'last' ? indices.length - 1 : Math.max(0, Math.min(indices.length - 1, position + step));
      if (indices[next] !== undefined) highlight(indices[next]);
    }
    trigger.addEventListener('click', function () { menu.hidden ? open() : close(); });
    trigger.addEventListener('keydown', function (event) {
      if (event.key === 'Tab') { close(); return; }
      if (event.key === 'Escape') { close(); event.preventDefault(); return; }
      if (['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) {
        event.preventDefault();
        if (menu.hidden) open();
        move(event.key === 'ArrowUp' ? -1 : 1, event.key === 'Home' ? 'first' : event.key === 'End' ? 'last' : '');
      } else if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        menu.hidden ? open() : choose(active);
      } else if (event.key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
        event.preventDefault();
        if (menu.hidden) open();
        prefix = Date.now() - typedAt > 700 ? event.key : prefix + event.key;
        typedAt = Date.now();
        var index = Array.from(select.options).findIndex(function (option) { return !option.disabled && !option.hidden && option.label.toLocaleLowerCase().startsWith(prefix.toLocaleLowerCase()); });
        if (index >= 0) highlight(index);
      }
    });
    trigger.addEventListener('blur', close);
    document.addEventListener('pointerdown', function (event) { if (!wrapper.contains(event.target)) close(); });
    window.addEventListener('resize', close);
    document.addEventListener('scroll', function (event) { if (!menu.contains(event.target)) close(); }, true);
    select.addEventListener('change', sync);
    select.addEventListener('focus', function () { trigger.focus(); });
    select.addEventListener('invalid', function (event) {
      event.preventDefault();
      trigger.setAttribute('aria-invalid', 'true');
      error.textContent = select.validationMessage;
      error.hidden = false;
      trigger.setAttribute('aria-describedby', [select.getAttribute('aria-describedby'), error.id].filter(Boolean).join(' '));
      trigger.focus();
    });
    if (select.form) select.form.addEventListener('reset', function () {
      setTimeout(function () { error.hidden = true; sync(); }, 0);
    });
    new MutationObserver(renderOptions).observe(select, { attributes: true, childList: true, subtree: true, characterData: true });
    if (select.closest('fieldset')) new MutationObserver(sync).observe(select.closest('fieldset'), { attributes: true, attributeFilter: ['disabled'] });
    // Enhancement becomes visible only after the original field and options are wired.
    select.before(wrapper);
    wrapper.append(select, trigger, menu);
    wrapper.after(error);
    renderOptions();
    select.dataset.reskinSelect = 'true';
    select.classList.add('reskin-select-native');
    select.tabIndex = -1;
    select.setAttribute('aria-hidden', 'true');
  });
})();
