(function () {
  "use strict";
  var page = document.documentElement;
  if (["localhost", "127.0.0.1", "[::1]"].includes(location.hostname) || page.dataset.reskinAnalytics) return;
  page.dataset.reskinAnalytics = "1";

  var queue = window._paq = window._paq || [];
  var locale = page.lang === "cs" ? "cs" : "en";
  // Unknown/private page IDs and native query inputs must never become analytics labels.
  var id = window.JSINFO && window.JSINFO.id;
  var publicPages = ["start", "about:start", "about:contacts", "sluzby:start", "faq:start", "news:start", "outages:start", "projekty:start", "resources:hardware:start", "variants:it4i-elements"];
  var name = typeof id === "string" && id.startsWith(locale + ":") ? id.slice(3) : "other";
  if (!publicPages.includes(name)) name = "other";
  var url = new URL("../../../../doku.php", document.currentScript.src);
  url.searchParams.set("id", locale + ":" + name);

  // Cookieless tracking without a consent banner: no cookies, no requireConsent (it would withhold every request).
  queue.push(["disableCookies"]);
  queue.push(["setDoNotTrack", true]);
  queue.push(["setCustomUrl", url.href]);
  queue.push(["setDocumentTitle", "MetaCentrum"]);
  queue.push(["setReferrerUrl", ""]);
  queue.push(["disableCampaignParameters"]);
  queue.push(["setCustomRequestProcessing", function (request) {
    var params = new URLSearchParams(request);
    params.set("url", url.href);
    params.delete("urlref");
    params.delete("_ref");
    ["_rcn", "_rck", "rcn", "rck", "search", "search_cat"].forEach(function (key) { params.delete(key); });
    ["link", "download"].forEach(function (key) {
      if (params.has(key)) params.set(key, new URL(params.get(key), url).origin + "/");
    });
    return params.toString();
  }]);
  queue.push(["setTrackerUrl", "https://metacentrum-matomo.duckdns.org/matomo.php"]);
  queue.push(["setSiteId", "1"]);
  queue.push(["trackPageView"]);
  queue.push(["enableLinkTracking"]);

  function track(action, label) {
    window._paq.push([function () {
      if (!this.isUserOptedOut()) this.trackEvent("Interface", action, label);
    }]);
  }
  document.addEventListener("click", function (event) {
    if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    var link = event.target.closest && event.target.closest("a.reskin-lang-btn[lang]");
    var target = link && link.lang;
    if ((target === "cs" || target === "en") && target !== locale) track("language_select", locale + "_to_" + target);
  });
  document.addEventListener("mc-language", function (event) {
    if (!event.defaultPrevented && event.target.matches("[data-design-header] mc-header")) {
      track("language_select", locale === "cs" ? "cs_to_en" : "en_to_cs");
    }
  });
  document.addEventListener("shown.bs.offcanvas", function (event) {
    if (event.target.id === "reskinHardwareDrawer") track("hardware_open", "details");
  });

  var script = document.createElement("script");
  script.async = true;
  script.referrerPolicy = "no-referrer";
  script.src = "https://metacentrum-matomo.duckdns.org/matomo.js";
  document.head.appendChild(script);
})();
