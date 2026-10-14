(function () {
  "use strict";

  var DESKTOP_MIN_WIDTH = 768;

  function initHoverDropdown(item) {
    var parent = item.querySelector("[data-topnav-parent]");
    var menu = item.querySelector(":scope > .reskin-topnav-dropdown");
    if (!parent || !menu) return;

    function open() {
      if (window.innerWidth < DESKTOP_MIN_WIDTH || item.classList.contains("is-overflowed")) return;
      item.classList.remove("is-dismissed");
      parent.setAttribute("aria-expanded", "true");
    }

    function dismiss() {
      item.classList.add("is-dismissed");
      parent.setAttribute("aria-expanded", "false");
    }

    item.addEventListener("mouseenter", open);
    item.addEventListener("mouseleave", function () {
      if (!item.contains(document.activeElement)) dismiss();
    });
    item.addEventListener("focusin", function (event) {
      if (event.target === parent && item.classList.contains("is-dismissed")) return;
      open();
    });
    item.addEventListener("focusout", function () {
      setTimeout(function () {
        if (!item.contains(document.activeElement)) dismiss();
      }, 0);
    });
    item.addEventListener("keydown", function (event) {
      if (window.innerWidth < DESKTOP_MIN_WIDTH) return;
      var links = Array.prototype.slice.call(menu.querySelectorAll("a[href]"));
      if (event.key === "Escape") {
        event.preventDefault();
        event.stopPropagation();
        dismiss();
        parent.focus();
      } else if (event.key === "ArrowDown" || event.key === "ArrowUp") {
        event.preventDefault();
        event.stopPropagation();
        if (!links.length) return;
        open();
        var index = links.indexOf(document.activeElement);
        var next = event.key === "ArrowDown"
          ? (index + 1) % links.length
          : (index <= 0 ? links.length - 1 : index - 1);
        links[next].focus();
      }
    });
    document.addEventListener("click", function (event) {
      if (!item.contains(event.target)) dismiss();
    });
  }

  function getDirectTopLink(item) {
    for (var i = 0; i < item.children.length; i++) {
      var child = item.children[i];
      if (child.classList && child.classList.contains("reskin-topnav-link")) {
        return child;
      }
    }
    return item.querySelector(".reskin-topnav-link");
  }

  function clearNode(node) {
    while (node.firstChild) node.removeChild(node.firstChild);
  }

  function buildMoreMenu(items, moreMenu) {
    clearNode(moreMenu);

    items.forEach(function (item) {
      var topLink = getDirectTopLink(item);
      if (!topLink) return;

      var entry = document.createElement("li");
      var entryLink = document.createElement("a");
      entryLink.className = "dropdown-item reskin-topnav-subitem reskin-topnav-more-link";
      entryLink.href = topLink.getAttribute("href") || "#";
      entryLink.textContent = topLink.textContent.trim();

      var isActive = item.classList.contains("is-active") || topLink.classList.contains("is-active");
      if (isActive) {
        entryLink.classList.add("is-active");
        entryLink.setAttribute("aria-current", "page");
      }

      entry.appendChild(entryLink);
      moreMenu.appendChild(entry);

      var childLinks = item.querySelectorAll(":scope > .reskin-topnav-dropdown .reskin-topnav-subitem");
      childLinks.forEach(function (childLink) {
        var childEntry = document.createElement("li");
        var childEntryLink = document.createElement("a");
        childEntryLink.className = "dropdown-item reskin-topnav-subitem reskin-topnav-more-child";
        childEntryLink.href = childLink.getAttribute("href") || "#";
        childEntryLink.textContent = "- " + childLink.textContent.trim();

        if (childLink.classList.contains("is-active")) {
          childEntryLink.classList.add("is-active");
          childEntryLink.setAttribute("aria-current", "page");
        }

        childEntry.appendChild(childEntryLink);
        moreMenu.appendChild(childEntry);
      });
    });
  }

  function initTopnav(nav) {
    var list = nav.querySelector("[data-topnav-list]");
    var collapse = nav.querySelector("[data-topnav-collapse]");
    var moreItem = nav.querySelector("[data-topnav-more]");
    var moreMenu = nav.querySelector("[data-topnav-more-menu]");
    if (!list || !collapse || !moreItem || !moreMenu) return;

    var items = Array.prototype.slice.call(list.querySelectorAll(":scope > [data-topnav-item]"));
    if (!items.length) return;
    items.filter(function (item) { return item.hasAttribute("data-topnav-hover"); }).forEach(initHoverDropdown);

    var frame = 0;

    function relayout() {
      if (frame) cancelAnimationFrame(frame);
      frame = requestAnimationFrame(function () {
        items.forEach(function (item) {
          item.classList.remove("is-overflowed");
          item.style.removeProperty("display");
        });

        clearNode(moreMenu);
        moreItem.classList.add("d-none");

        if (window.innerWidth < DESKTOP_MIN_WIDTH) {
          return;
        }

        var availableWidth = Math.floor(collapse.getBoundingClientRect().width);
        if (!availableWidth) return;

        if (list.scrollWidth <= availableWidth + 1) return;

        moreItem.classList.remove("d-none");

        while (list.scrollWidth > availableWidth + 1) {
          var visibleItems = items.filter(function (item) {
            return !item.classList.contains("is-overflowed");
          });
          if (!visibleItems.length) break;

          // Overflow is a stable suffix, independent of the current page.
          var candidate = visibleItems[visibleItems.length - 1];

          candidate.classList.add("is-overflowed");
          candidate.style.display = "none";
        }

        var overflowedItems = items.filter(function (item) {
          return item.classList.contains("is-overflowed");
        });

        if (!overflowedItems.length) {
          moreItem.classList.add("d-none");
          return;
        }

        buildMoreMenu(overflowedItems, moreMenu);
      });
    }

    relayout();
    window.addEventListener("resize", relayout, { passive: true });
    window.addEventListener("orientationchange", relayout, { passive: true });
    window.addEventListener("load", relayout, { once: true });

    if (window.ResizeObserver) {
      var observer = new ResizeObserver(relayout);
      observer.observe(nav);
    }
  }

  function init() {
    var navs = document.querySelectorAll("[data-topnav-overflow]");
    navs.forEach(initTopnav);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init, { once: true });
  } else {
    init();
  }
})();
