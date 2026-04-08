(function () {
  var sliders = document.querySelectorAll("[data-story-slider]");
  if (!sliders.length) return;

  var getPerView = function () {
    if (window.matchMedia("(max-width: 767px)").matches) return 1;
    if (window.matchMedia("(max-width: 991px)").matches) return 2;
    return 3;
  };

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
      var perView = getPerView();
      var computed = window.getComputedStyle(track);
      var gap = parseFloat(computed.gap || computed.columnGap || "0") || 0;
      var itemWidth = items[0].getBoundingClientRect().width;
      step = itemWidth + gap;
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

    var resizeTimer = null;
    window.addEventListener("resize", function () {
      if (resizeTimer) window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(measure, 120);
    });

    measure();
  });
})();
