/* Bootstrap 5 style: counterpart language links, current navigation and colour mode. */
(function () {
  var root = document.documentElement;
  var here = new URL(location.href);
  var id = here.searchParams.get("id") || "";
  var lang = root.lang;

  document.querySelectorAll("a.nav-link[href]:not([href^='#'])").forEach(function (link) {
    if (link.origin !== location.origin || !/(^|\/)doku\.php$/.test(link.pathname)) return;
    if (new URL(link.href).searchParams.get("id") === id) {
      link.classList.add("active");
      link.setAttribute("aria-current", "page");
    }
  });

  // Language buttons open the counterpart of the current page; a missing one falls back through the wiki.
  document.querySelectorAll("[data-b5-lang]").forEach(function (link) {
    var target = link.getAttribute("data-b5-lang");
    if (target === lang) {
      link.href = here.href;
      link.classList.add("active");
      link.setAttribute("aria-current", "page");
    } else if (id.indexOf(lang + ":") === 0) {
      var url = new URL(here.href);
      url.searchParams.set("id", target + id.slice(lang.length));
      url.searchParams.delete("fallback_from");
      link.href = url.href;
    }
  });

  var toggle = document.querySelector("[data-b5-theme-toggle]");
  if (!toggle) return;
  var sync = function () {
    toggle.setAttribute("aria-pressed", root.getAttribute("data-bs-theme") === "dark" ? "true" : "false");
  };
  sync();
  toggle.addEventListener("click", function () {
    var next = root.getAttribute("data-bs-theme") === "dark" ? "light" : "dark";
    root.setAttribute("data-bs-theme", next);
    try {
      localStorage.setItem("reskin-theme", next);
    } catch (err) {
      // storage unavailable: the choice lasts for this page only
    }
    sync();
  });
})();
