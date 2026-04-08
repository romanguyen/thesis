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
