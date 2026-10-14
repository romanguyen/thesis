(function () {
  "use strict";

  var shell = document.querySelector("[data-header-shell]");
  if (!shell) return;

  var preferences = shell.querySelector("[data-header-preferences]");
  var home = shell.querySelector("[data-header-preferences-home]");
  var mobileSlot = document.querySelector("[data-header-preferences-mobile]");
  var menuButton = shell.querySelector("[data-header-menu]");
  var sidebar = document.getElementById("reskinSidebar");
  var mobile = window.matchMedia("(max-width: 767px)");
  var summary = preferences ? preferences.querySelector("summary") : null;
  var actions = shell.querySelector(".reskin-header-actions");
  var language = shell.querySelector(".reskin-header-meta");
  var theme = shell.querySelector("[data-theme-toggle]");
  var compact = false;
  var frame = 0;

  function measureHeader() {
    document.documentElement.style.setProperty("--reskin-sticky-header-height", Math.ceil(shell.getBoundingClientRect().height) + "px");
  }

  function updateScroll() {
    frame = 0;
    // Hysteresis prevents the smaller in-flow header from oscillating at its threshold.
    var next = compact ? window.scrollY > 32 : window.scrollY > 96;
    if (next !== compact) {
      compact = next;
      shell.classList.toggle("is-compact", compact);
    }
    measureHeader();
  }

  function queueScroll() {
    if (!frame) frame = requestAnimationFrame(updateScroll);
  }

  function placePreferences() {
    if (!preferences || !home) return;
    var destination = mobile.matches && mobileSlot ? mobileSlot : home;
    var focused = document.activeElement;
    var hadFocus = preferences.contains(document.activeElement);
    var utilityFocus = (language && language.contains(focused)) || theme === focused;
    var sidebarFocus = sidebar && sidebar.contains(document.activeElement);
    var instance = !mobile.matches && sidebar && window.bootstrap
      ? window.bootstrap.Offcanvas.getInstance(sidebar) : null;
    var closingSidebar = instance && (sidebar.classList.contains("show") || sidebar.classList.contains("showing"));

    if (closingSidebar) {
      sidebar.addEventListener("hidden.bs.offcanvas", function () {
        if (!mobile.matches && sidebarFocus && summary) (utilityFocus ? focused : summary).focus();
      }, { once: true });
      // Finish opening before hiding so Bootstrap cannot reactivate its focus trap afterward.
      if (sidebar.classList.contains("showing")) {
        sidebar.addEventListener("shown.bs.offcanvas", function () { instance.hide(); }, { once: true });
      } else instance.hide();
    }

    var moving = preferences.parentElement !== destination;
    if (moving) {
      preferences.open = false;
      if (destination === mobileSlot) {
        if (language) mobileSlot.appendChild(language);
        if (theme) mobileSlot.appendChild(theme);
      } else if (actions) {
        if (language) actions.insertBefore(language, actions.firstElementChild);
        if (theme) actions.insertBefore(theme, home);
      }
      destination.appendChild(preferences);
      if ((hadFocus || utilityFocus) && !closingSidebar) {
        (mobile.matches && menuButton ? menuButton : utilityFocus ? focused : summary).focus();
      }
    }
    shell.classList.toggle("has-mobile-controls", destination === mobileSlot);
    measureHeader();
  }

  if (preferences) {
    preferences.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && preferences.open) {
        event.preventDefault();
        event.stopPropagation();
        preferences.open = false;
        summary.focus();
      }
    });
    preferences.addEventListener("focusout", function () {
      setTimeout(function () {
        if (!preferences.contains(document.activeElement)) preferences.open = false;
      }, 0);
    });
    document.addEventListener("click", function (event) {
      if (!preferences.contains(event.target)) preferences.open = false;
    });
  }

  if (sidebar && menuButton) {
    sidebar.addEventListener("show.bs.offcanvas", function () { menuButton.setAttribute("aria-expanded", "true"); });
    sidebar.addEventListener("hidden.bs.offcanvas", function () {
      menuButton.setAttribute("aria-expanded", "false");
      if (preferences) preferences.open = false;
    });
  }

  placePreferences();
  updateScroll();
  window.addEventListener("scroll", queueScroll, { passive: true });
  window.addEventListener("resize", measureHeader, { passive: true });
  if (mobile.addEventListener) mobile.addEventListener("change", placePreferences);
  else mobile.addListener(placePreferences);
  if (window.ResizeObserver) new ResizeObserver(measureHeader).observe(shell);
  else shell.addEventListener("transitionend", measureHeader);
})();
