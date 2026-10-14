(function () {
  var sliders = document.querySelectorAll("[data-story-slider]");
  if (!sliders.length) return;

  sliders.forEach(function (slider) {
    var viewport = slider.querySelector(".reskin-story-viewport");
    var track = slider.querySelector(".reskin-story-track");
    var items = slider.querySelectorAll(".reskin-story-item");
    var prev = slider.querySelector("[data-story-prev]");
    var next = slider.querySelector("[data-story-next]");
    var controls = slider.querySelector(".reskin-story-controls");

    if (!viewport || !track || !items.length) return;

    var index = 0;
    var maxIndex = 0;
    var step = 0;

    var update = function () {
      var offset = step * index;
      track.style.transform = "translate3d(" + -offset + "px, 0, 0)";

      if (prev) {
        var atStart = index <= 0;
        prev.disabled = atStart;
        prev.setAttribute("aria-disabled", atStart ? "true" : "false");
      }

      if (next) {
        var atEnd = index >= maxIndex;
        next.disabled = atEnd;
        next.setAttribute("aria-disabled", atEnd ? "true" : "false");
      }

      if (controls) {
        controls.style.display = maxIndex > 0 ? "" : "none";
      }
    };

    var measure = function () {
      var computed = window.getComputedStyle(track);
      var gap = parseFloat(computed.gap || computed.columnGap || "0") || 0;
      var itemWidth = items[0].getBoundingClientRect().width;
      var viewportWidth = viewport.getBoundingClientRect().width;
      if (itemWidth <= 0 || viewportWidth <= 0) return;
      step = itemWidth + gap;
      // Layout CSS owns card widths; tolerate subpixel rounding at an exact fit.
      var perView = Math.max(1, Math.floor((viewportWidth + gap + 1) / step));
      maxIndex = Math.max(0, items.length - perView);
      if (index > maxIndex) index = maxIndex;
      update();
    };

    if (prev) {
      prev.addEventListener("click", function () {
        if (index <= 0) return;
        index -= 1;
        update();
      });
    }

    if (next) {
      next.addEventListener("click", function () {
        if (index >= maxIndex) return;
        index += 1;
        update();
      });
    }

    if (window.ResizeObserver) {
      var observer = new ResizeObserver(measure);
      observer.observe(viewport);
    } else {
      var resizeTimer = null;
      window.addEventListener("resize", function () {
        if (resizeTimer) window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(measure, 120);
      });
    }

    measure();
  });
})();
