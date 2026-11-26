/* Keep the selected layout and style on wiki links and search forms rendered without them. */
(function () {
  var here = new URL(location.href);
  var kept = ["style", "layout"].filter(function (key) { return here.searchParams.has(key); });
  if (!kept.length) return;

  var isWiki = function (url) {
    return url.origin === location.origin && /(^|\/)doku\.php$/.test(url.pathname);
  };

  var keepLinks = function (root) {
    // The chooser is skipped: its links to Top and CESNET omit the parameter on purpose.
    root.querySelectorAll("a[href]:not(.reskin-layout-btn)").forEach(function (link) {
      if (link.getAttribute("href").charAt(0) === "#" || !isWiki(link)) return;
      var url = new URL(link.href);
      kept.forEach(function (key) {
        if (!url.searchParams.has(key)) url.searchParams.set(key, here.searchParams.get(key));
      });
      if (url.href !== link.href) link.href = url.href;
    });
  };
  keepLinks(document);

  // Native quicksearch replaces its results after each request.
  var results = document.getElementById("qsearch__out");
  if (results) new MutationObserver(function () { keepLinks(results); }).observe(results, { childList: true });

  // A GET submission drops the query of its action, so the values must be fields.
  document.querySelectorAll('form[method="get"][action*="doku.php"]').forEach(function (form) {
    if (!isWiki(new URL(form.getAttribute("action"), location.href))) return;
    kept.forEach(function (key) {
      if (form.querySelector('[name="' + key + '"]')) return;
      var field = document.createElement("input");
      field.type = "hidden";
      field.name = key;
      field.value = here.searchParams.get(key);
      form.append(field);
    });
  });
})();
