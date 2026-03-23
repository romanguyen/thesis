(function () {
  var root = document.documentElement;
  var storageKey = "reskin-theme";
  var stored = null;

  try {
    stored = localStorage.getItem(storageKey);
  } catch (err) {
    stored = null;
  }

  if (stored === "dark" || stored === "light") {
    root.setAttribute("data-theme", stored);
  }

  var toggle = document.querySelector("[data-theme-toggle]");
  if (!toggle) return;

  var updatePressed = function () {
    var current = root.getAttribute("data-theme");
    toggle.setAttribute("aria-pressed", current === "dark" ? "true" : "false");
  };

  updatePressed();

  toggle.addEventListener("click", function () {
    var current = root.getAttribute("data-theme");
    var next = current === "dark" ? "light" : "dark";
    root.setAttribute("data-theme", next);
    try {
      localStorage.setItem(storageKey, next);
    } catch (err) {
      // ignore storage errors
    }
    updatePressed();
  });
})();

(function () {
  var modal = document.querySelector("[data-search-modal]");
  var openButton = document.querySelector("[data-search-open]");
  if (!modal || !openButton) return;

  var closeButton = modal.querySelector("[data-search-close]");
  var searchInput = modal.querySelector('input[name="q"]');

  var openModal = function () {
    modal.classList.add("is-open");
    document.body.classList.add("reskin-search-open");
    if (searchInput) {
      searchInput.focus();
      searchInput.select();
    }
    openButton.setAttribute("aria-expanded", "true");
  };

  var closeModal = function () {
    modal.classList.remove("is-open");
    document.body.classList.remove("reskin-search-open");
    openButton.setAttribute("aria-expanded", "false");
  };

  openButton.addEventListener("click", function () {
    openModal();
  });

  if (closeButton) {
    closeButton.addEventListener("click", function () {
      closeModal();
    });
  }

  modal.addEventListener("click", function (event) {
    if (event.target === modal) {
      closeModal();
    }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && modal.classList.contains("is-open")) {
      closeModal();
    }
  });
})();

(function () {
  function getCurrentId() {
    if (window.JSINFO && JSINFO.id) {
      return JSINFO.id;
    }

    var idInput = document.querySelector('input[name="id"]');
    if (idInput && idInput.value) return idInput.value;

    try {
      var params = new URLSearchParams(window.location.search);
      var id = params.get("id");
      if (id) return id;
    } catch (err) {
      // ignore
    }

    var path = window.location.pathname.replace(/^\/+/, "");
    if (!path || path === "doku.php" || path === "index.php") return null;
    return decodeURIComponent(path.replace(/\//g, ":"));
  }

  function getIdFromHref(href) {
    if (!href) return null;
    if (href.charAt(0) === "#") return null;
    if (href.indexOf("javascript:") === 0) return null;
    try {
      var url = new URL(href, window.location.origin);
      var id = url.searchParams.get("id");
      if (id) return id;
      var path = url.pathname.replace(/^\/+/, "");
      if (!path || path === "doku.php" || path === "index.php") return null;
      return decodeURIComponent(path.replace(/\//g, ":"));
    } catch (err) {
      return null;
    }
  }

  function getLinkId(link) {
    if (!link) return null;
    var dataId = link.getAttribute("data-wiki-id");
    if (dataId) return dataId;
    return getIdFromHref(link.getAttribute("href"));
  }

  function markActive(container) {
    var currentId = getCurrentId();
    if (!currentId) return;

    var links = container.querySelectorAll("a[href]");
    links.forEach(function (link) {
      var id = getLinkId(link);
      if (!id) return;

      if (id === currentId) {
        link.classList.add("is-active");
        var liExact = link.closest("li");
        if (liExact) liExact.classList.add("is-active");
      } else if (currentId.indexOf(id + ":") === 0) {
        link.classList.add("is-active");
        var liParent = link.closest("li");
        if (liParent) liParent.classList.add("is-active-parent");
      }
    });
  }

  function setupTopNav() {
    var menu = document.querySelector(".reskin-menu");
    if (!menu) return;
    markActive(menu);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      setupTopNav();
    });
  } else {
    setupTopNav();
  }
})();
