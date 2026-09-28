(function () {
  var toc = document.querySelector('.reskin-page-toc #dw__toc');
  if (!toc) return;

  var sections = Array.prototype.map.call(toc.querySelectorAll('a[href^="#"]'), function (link) {
    var id;
    try {
      id = decodeURIComponent(link.getAttribute('href').slice(1));
    } catch (err) {
      return null;
    }
    var heading = document.getElementById(id);
    return heading ? { link: link, heading: heading } : null;
  }).filter(Boolean);
  if (!sections.length) return;

  var active = null;
  var scheduled = false;

  function update() {
    scheduled = false;
    var current = sections[0];
    for (var i = 0; i < sections.length; i++) {
      if (sections[i].heading.getBoundingClientRect().top > 140) break;
      current = sections[i];
    }
    if (current === active) return;
    if (active) {
      active.link.classList.remove('is-active');
      active.link.removeAttribute('aria-current');
    }
    current.link.classList.add('is-active');
    current.link.setAttribute('aria-current', 'location');
    active = current;
  }

  function scheduleUpdate() {
    if (scheduled) return;
    scheduled = true;
    window.requestAnimationFrame(update);
  }

  window.addEventListener('scroll', scheduleUpdate, { passive: true });
  window.addEventListener('resize', scheduleUpdate);
  window.addEventListener('load', scheduleUpdate);
  scheduleUpdate();
})();
