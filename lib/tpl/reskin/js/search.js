(function () {
  var modal = document.querySelector("[data-search-modal]");
  var openButton = document.querySelector("[data-search-open]");
  if (!modal || !openButton) return;

  var closeButton = modal.querySelector("[data-search-close]");
  var searchInput = modal.querySelector('input[name="q"]');
  var mobileLayout = window.matchMedia("(max-width: 767px)");
  var isOpen = false;

  var focusableElements = function () {
    return Array.prototype.filter.call(
      modal.querySelectorAll('a[href], button, input, select, textarea, [tabindex]'),
      function (element) {
        return !element.disabled && element.tabIndex >= 0 && element.getClientRects().length > 0 &&
          window.getComputedStyle(element).visibility !== "hidden";
      }
    );
  };

  var focusSearch = function () {
    var elements = focusableElements();
    var target = elements.indexOf(searchInput) !== -1 ? searchInput : (elements[0] || modal);
    target.focus();
  };

  var openModal = function () {
    if (!mobileLayout.matches || isOpen) return;
    isOpen = true;
    modal.classList.add("is-open");
    document.body.classList.add("reskin-search-open");
    modal.setAttribute("role", "dialog");
    modal.setAttribute("aria-modal", "true");
    modal.setAttribute("aria-label", openButton.getAttribute("aria-label") || "Search");
    openButton.setAttribute("aria-expanded", "true");
    focusSearch();
    if (document.activeElement === searchInput) searchInput.select();
  };

  var closeModal = function (restoreFocus) {
    if (!isOpen) return;
    isOpen = false;
    modal.classList.remove("is-open");
    document.body.classList.remove("reskin-search-open");
    modal.removeAttribute("role");
    modal.removeAttribute("aria-modal");
    modal.removeAttribute("aria-label");
    openButton.setAttribute("aria-expanded", "false");
    if (restoreFocus) openButton.focus();
  };

  openButton.addEventListener("click", function () {
    openModal();
  });

  if (closeButton) {
    closeButton.addEventListener("click", function () {
      closeModal(true);
    });
  }

  modal.addEventListener("click", function (event) {
    if (event.target === modal) {
      closeModal(true);
    }
  });

  document.addEventListener("keydown", function (event) {
    if (!isOpen || !mobileLayout.matches) return;
    if (event.key === "Escape") {
      event.preventDefault();
      closeModal(true);
    } else if (event.key === "Tab") {
      var elements = focusableElements();
      var first = elements[0];
      var last = elements[elements.length - 1];
      if (!first) {
        event.preventDefault();
        modal.focus();
      } else if (elements.indexOf(document.activeElement) === -1 ||
        (event.shiftKey && document.activeElement === first) ||
        (!event.shiftKey && document.activeElement === last)) {
        event.preventDefault();
        (event.shiftKey ? last : first).focus();
      }
    }
  });

  document.addEventListener("focusin", function (event) {
    if (isOpen && mobileLayout.matches && !modal.contains(event.target)) focusSearch();
  });

  var handleLayoutChange = function () {
    if (mobileLayout.matches || !isOpen) return;
    var hadFocus = modal.contains(document.activeElement);
    closeModal(false);
    if (hadFocus && searchInput) searchInput.focus();
  };
  if (mobileLayout.addEventListener) {
    mobileLayout.addEventListener("change", handleLayoutChange);
  } else {
    mobileLayout.addListener(handleLayoutChange);
  }
})();
