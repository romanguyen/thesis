(function () {
  var root = document.documentElement;
  var storageKey = "reskin-theme";

  var toggle = document.querySelector("[data-theme-toggle]");
  if (!toggle) return;

  var updatePressed = function () {
    var current = root.getAttribute("data-bs-theme");
    toggle.setAttribute("aria-pressed", current === "dark" ? "true" : "false");
  };

  updatePressed();

  toggle.addEventListener("click", function () {
    var current = root.getAttribute("data-bs-theme");
    var next = current === "dark" ? "light" : "dark";
    root.setAttribute("data-bs-theme", next);
    try {
      localStorage.setItem(storageKey, next);
    } catch (err) {
      // ignore storage errors
    }
    updatePressed();
  });
})();
