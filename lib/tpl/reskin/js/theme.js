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
