(function () {
  "use strict";
  var shell = document.querySelector("[data-header-shell]");
  if (!shell) return;

  var owner = document.querySelector("[data-design-header]");
  if (!owner) return;
  // Created before mc-header's own query, so it reports first: focus is read before the sidebar/bar switch re-renders.
  var mobile = window.matchMedia("(max-width: 65.98rem)");
  var rollback = function () {};
  import(owner.dataset.elementsUrl).then(async function () {
    var header = owner.querySelector("mc-header");
    await customElements.whenDefined("mc-header");
    var config = JSON.parse(owner.dataset.designConfig);
    var fallbackHeader = shell.querySelector(".reskin-header");
    var nav = document.querySelector(".reskin-topnav");
    var nativeSearch = fallbackHeader.querySelector("[data-search-modal]");
    var nativeTrigger = fallbackHeader.querySelector("[data-search-open]");
    var preferences = fallbackHeader.querySelector("[data-header-preferences]");
    var original = [nativeSearch, nativeTrigger, preferences, nativeSearch.querySelector("form.search"), nativeSearch.querySelector("[data-search-close]")].map(function (node) { return {node: node, parent: node.parentNode, next: node.nextSibling}; });
    var panel = preferences.querySelector(".reskin-preferences-panel");
    var summary = preferences.querySelector("summary");
    var wasOpen = preferences.open;
    var utilities = document.createElement("div");
    rollback = function () {
      original.forEach(function (entry) {entry.parent.insertBefore(entry.node, entry.next && entry.next.parentNode === entry.parent ? entry.next : null);});
      summary.hidden = false;
      preferences.open = wasOpen;
      nativeTrigger.hidden = false;
      if (searchRow) searchRow.remove();
      fallbackHeader.hidden = false;
      if (nav) nav.hidden = false;
      document.querySelector(".reskin-layout-clean").removeAttribute("data-design-ready");
      utilities.remove();
    };
    utilities.className = "reskin-design-utilities";
    shell.after(utilities);
    utilities.append(nativeSearch, nativeTrigger);
    nativeTrigger.hidden = true;
    var searchRow = document.createElement("div");
    searchRow.className = "reskin-design-search-row";
    searchRow.append(nativeSearch.querySelector("form.search"), nativeSearch.querySelector("[data-search-close]"));
    nativeSearch.append(searchRow);
    var dialog = document.createElement("dialog");
    dialog.className = "reskin-design-preferences";
    dialog.setAttribute("aria-label", config.labels.preferences);
    dialog.append(preferences);
    utilities.append(dialog);
    summary.hidden = true;
    preferences.open = true;

    function itemFromLink(link) {
      return {label: link.textContent.trim(), href: link.href, active: link.classList.contains("is-active") || link.hasAttribute("aria-current"), external: new URL(link.href).origin !== location.origin};
    }
    header.navItems = nav ? Array.from(nav.querySelectorAll("[data-topnav-list] > [data-topnav-item]")).map(function (item) {
      var link = item.querySelector(".reskin-topnav-link");
      var entry = itemFromLink(link);
      entry.active = item.classList.contains("is-active");
      var children = Array.from(item.querySelectorAll(":scope > .reskin-topnav-dropdown a[href]")).map(itemFromLink);
      // Canonical groups are toggles, so preserve their former destination as an overview link.
      if (children.length) {
        // The menu links namespaces (cs:vo) whose start page is the current one (cs:vo:start).
        var landing = entry.active && new URL(link.href).searchParams.get("id") + ":start" === window.JSINFO.id;
        if (!children.some(function (child) { return child.href === entry.href; })) children.unshift({...entry, active: landing || link.hasAttribute("aria-current")});
        entry.children = children;
      }
      return entry;
    }) : [];
    header.user = config.user || undefined;
    header.logout = !!config.logout;
    if (config.logout) header.logoutLabel = config.logout.label;
    var language = config.languages.find(function (target) { return !target.current; });
    header.language = language && {label: language.label, ariaLabel: language.title};
    function actions() {
      header.actions = [{label: config.labels.open_search}, {label: config.labels.preferences, active: dialog.open}];
    }
    actions();
    /** The sidebar/open panel action, or the menu button of the closed mobile panel. */
    function opener(index) {
      return header.shadowRoot.querySelector('[data-key="panel-action-' + index + '"]') || header.shadowRoot.querySelector(".mc-header__menu-toggle");
    }
    header.addEventListener("mc-language", function () { if (language) location.href = language.url; });
    header.addEventListener("mc-logout", function () { if (config.logout) location.href = config.logout.url; });
    header.addEventListener("mc-action", async function (event) {
      // The mobile panel closes after dispatching; wait for that render before choosing an opener.
      await null;
      await header.updateComplete;
      if (event.detail.index === 0) document.dispatchEvent(new CustomEvent("reskin:search-open", {detail: {opener: opener(0)}}));
      else {
        if (dialog.open) { dialog.close(); opener(1).focus(); }
        else {
          dialog.show();
          panel.querySelector("a").focus({preventScroll: true});
        }
        actions();
      }
    });
    dialog.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        event.preventDefault();
        event.stopPropagation();
        dialog.close();
        opener(1).focus();
      }
    });
    dialog.addEventListener("close", actions);
    dialog.addEventListener("focusout", function () {
      setTimeout(function () { if (!dialog.contains(document.activeElement)) dialog.close(); }, 0);
    });
    function onOpener(event) {
      return event.composedPath().some(function (node) { return node.matches && node.matches('[data-key="panel-action-1"]'); });
    }
    // Focus must not leave the open panel before the opener's click, or the click would reopen it.
    header.addEventListener("mousedown", function (event) { if (dialog.open && onOpener(event)) event.preventDefault(); });
    document.addEventListener("click", function (event) {
      if (!dialog.contains(event.target) && !onOpener(event)) dialog.close();
    });
    // mc-header has no attribute for its skip link, and recreates it when the layout switches.
    function adaptSkipLink() {
      var skip = header.shadowRoot.querySelector(".cds--skip-to-content");
      skip.href = "#reskin-main";
      skip.textContent = config.labels.skip_to_content;
    }
    await header.updateComplete;
    adaptSkipLink();
    // Hide the fully functional native fallback only after successful configuration.
    document.querySelector(".reskin-layout-clean").setAttribute("data-design-ready", "true");
    fallbackHeader.hidden = true;
    if (nav) nav.hidden = true;
    mobile.addEventListener("change", function () {
      var focused = header.shadowRoot.activeElement;
      requestAnimationFrame(async function () {
        await header.updateComplete;
        adaptSkipLink();
        if (focused && !focused.isConnected) opener(0).focus();
      });
    });
  }).catch(function (error) {
    // A missing/unsupported bundle must leave native navigation and search available.
    rollback();
    console.warn("MetaCentrum shell unavailable; using the native wiki shell.", error);
  });
})();
