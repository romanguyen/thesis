// Private feature scope: loading this script again must not redeclare globals.
(function () {
"use strict";

const HARDWARE_CATALOG_MEDIA_ID = "hardware:catalog.json";

const LABELS = {
  en: {
    cpu: "CPU",
    ram: "RAM",
    gpu: "GPU",
    storage: "disk",
    network: "net",
    comment: "comment",
    owner: "owner",
    resource: "resource",
    resourceLinkText: "Open resource page",
    detail: "Detail",
    nodes: "nodes",
    close: "Close",
    empty: "No additional details available.",
    detailAriaPrefix: "Detail for ",
  },
  cs: {
    cpu: "CPU",
    ram: "RAM",
    gpu: "GPU",
    storage: "disk",
    network: "sit",
    comment: "poznamka",
    owner: "vlastnik",
    resource: "odkaz",
    resourceLinkText: "Otevrit resource page",
    detail: "Detail",
    nodes: "uzly",
    close: "Zavrit",
    empty: "Dalsi informace nejsou k dispozici.",
    detailAriaPrefix: "Detail pro ",
  },
};

(async () => {
  const site = document.getElementById("dokuwiki__site");
  if (!site || site.dataset.hwDrawerState) return;
  site.dataset.hwDrawerState = "loading";
  try {
    if (await initializeHardware(site)) {
      site.dataset.hwDrawerState = "ready";
    } else {
      delete site.dataset.hwDrawerState;
    }
  } catch (error) {
    delete site.dataset.hwDrawerState;
    throw error;
  }
})();

/** Orchestrate one enhancement; absent content stays eligible for later initialization. */
async function initializeHardware(site) {
  const locale = detectLocale(site);
  const labels = LABELS[locale] || LABELS.en;
  const catalog = await loadHardwareCatalog(site);
  const catalogIndex = buildCatalogIndex(catalog);

  const page = site.querySelector(".reskin-page");
  if (!page) return false;

  const inventoryTable = findInventoryTable(page);
  if (!inventoryTable) return false;

  const profileMap = buildProfileMapFromTable(inventoryTable, catalogIndex, labels);
  if (profileMap.size === 0) return false;

  const controller = createDrawerController(ensureDrawer(site, labels), labels);
  appendDetailColumn(inventoryTable, profileMap, labels, (clusterKey) => {
    controller.show(profileMap.get(clusterKey));
  });
  return true;
}

/** Bootstrap/event boundary; profile presentation is kept separate from wiring. */
function createDrawerController(drawer, labels) {
  const canOpen = drawer && window.bootstrap && window.bootstrap.Offcanvas;
  const api = canOpen ? window.bootstrap.Offcanvas.getOrCreateInstance(drawer) : null;
  const elements = {
    title: api ? drawer.querySelector("[data-hw-drawer-title]") : null,
    image: api ? drawer.querySelector("[data-hw-drawer-image]") : null,
    specs: api ? drawer.querySelector("[data-hw-drawer-specs]") : null,
  };
  bindDrawerImageError(elements.image);
  return {
    show(profile) {
      if (!api || !profile) return;
      renderDrawerProfile(elements, profile, labels);
      api.show();
    },
  };
}

function bindDrawerImageError(image) {
  if (!image || image.dataset.errorBound) return;
  image.addEventListener("error", () => hideDrawerImage(image));
  image.dataset.errorBound = "1";
}

function hideDrawerImage(image) {
  image.setAttribute("hidden", "hidden");
  image.removeAttribute("src");
  image.alt = "";
}

function renderDrawerProfile(elements, profile, labels) {
  if (elements.title) elements.title.textContent = profile.title;
  if (elements.image) {
    if (profile.imageSrc) {
      elements.image.src = profile.imageSrc;
      elements.image.alt = profile.imageAlt || profile.title;
      elements.image.removeAttribute("hidden");
    } else {
      hideDrawerImage(elements.image);
    }
  }
  if (elements.specs) {
    elements.specs.innerHTML = profile.specs.length === 0
      ? `<tr><th scope="row">Info</th><td>${escapeHtml(labels.empty)}</td></tr>`
      : profile.specs.map((spec) => `<tr><th scope="row">${escapeHtml(spec.label)}</th><td>${spec.valueHtml}</td></tr>`).join("");
  }
}

// Locale/catalog discovery and loading retain the existing media/base and fetch policy.
function detectLocale(site) {
  return Array.from(site.classList).some((className) =>
    className.startsWith("reskin-pageid-cs-")
  )
    ? "cs"
    : "en";
}

async function loadHardwareCatalog(site) {
  const catalogUrl = resolveCatalogUrl(site);
  if (!catalogUrl) return {};

  try {
    const response = await fetch(catalogUrl, {
      credentials: "same-origin",
      headers: {
        Accept: "application/json, text/plain, */*",
      },
    });

    if (!response.ok) return {};

    const data = await response.json();
    if (!data || typeof data !== "object" || Array.isArray(data)) return {};

    return data;
  } catch (error) {
    return {};
  }
}

function resolveCatalogUrl(site) {
  const configured = site.dataset.hwCatalogUrl || "";
  if (configured) return configured;

  const base = typeof window.DOKU_BASE === "string" ? window.DOKU_BASE : "/";
  const normalizedBase = base.endsWith("/") ? base : `${base}/`;

  return `${normalizedBase}lib/exe/fetch.php?media=${encodeURIComponent(HARDWARE_CATALOG_MEDIA_ID)}`;
}

// Native table adaptation: only matching inventories are enhanced.
function findInventoryTable(page) {
  const tables = Array.from(page.querySelectorAll("table"));
  for (const table of tables) {
    const headers = Array.from(table.querySelectorAll("tr:first-child th"))
      .map((th) => normalizeHeader(th.textContent))
      .filter(Boolean);

    const hasCluster = headers.includes("cluster");
    const hasCpu = headers.includes("cpu");
    const hasNodes =
      headers.includes("nodes") || headers.includes("uzly") || headers.includes("node");

    if (hasCluster && hasCpu && hasNodes) {
      return table;
    }
  }

  return null;
}

function normalizeHeader(text) {
  return (text || "").trim().toLowerCase().replace(/\s+/g, " ");
}

function buildProfileMapFromTable(table, catalogIndex, labels) {
  const map = new Map();

  const rows = Array.from(table.querySelectorAll("tr")).filter(
    (row, index) => index > 0 && row.querySelector("td")
  );

  rows.forEach((row) => {
    const clusterAnchor = findClusterAnchor(row);
    if (!clusterAnchor) return;

    const clusterName = clusterAnchor.textContent.trim();
    const clusterKey = normalizeClusterKey(clusterName);
    const details =
      catalogIndex[clusterKey] || catalogIndex[stripClusterYearSuffix(clusterKey)] || null;

    map.set(clusterKey, buildHardwareProfile(clusterName, details, inventoryFallback(row, clusterAnchor), labels));
  });

  return map;
}

function inventoryFallback(row, clusterAnchor) {
  const cells = row.querySelectorAll("td");
  return {
    institution: cells[0] ? cells[0].textContent.trim() : "",
    cpu: cells[2] ? cells[2].textContent.trim() : "",
    nodes: cells[3] ? cells[3].textContent.trim() : "",
    liveUrl: clusterAnchor.href || "",
  };
}

function buildHardwareProfile(title, details, fallback, labels) {
  return {
    title,
    imageSrc: buildImageSource(details && details.imageSrc),
    imageAlt: title,
    specs: buildSpecs(details, labels, fallback),
  };
}

function buildCatalogIndex(catalog) {
  const index = {};
  Object.entries(catalog).forEach(([rawKey, details]) => {
    const normalized = normalizeClusterKey(rawKey);
    index[normalized] = details;

    const noYear = stripClusterYearSuffix(normalized);
    if (noYear) {
      index[noYear] = details;
    }
  });
  return index;
}

function findClusterAnchor(row) {
  const anchors = Array.from(row.querySelectorAll("a"));
  return (
    anchors.find((anchor) => normalizeClusterKey(anchor.textContent).includes(".")) ||
    null
  );
}

function buildImageSource(url) {
  if (typeof url !== "string" || url === "") return "";
  return url.replace(/^http:\/\//i, "https://");
}

function buildSpecs(details, labels, fallback) {
  const cpu = details && details.cpu ? details.cpu : fallback.cpu;
  const ram = details && details.ram ? details.ram : "";
  const gpu = details && details.gpu ? details.gpu : "";
  const storage = details && details.storage ? details.storage : "";
  const network = details && details.network ? details.network : "";
  const comment = details && details.comment ? details.comment : "";
  const owner = details && details.owner ? details.owner : fallback.institution;
  const liveUrl = details && details.liveUrl ? details.liveUrl : fallback.liveUrl;
  const nodeCount = fallback.nodes;

  const specs = [];

  if (cpu) {
    specs.push({ label: labels.cpu, valueHtml: escapeHtml(cpu) });
  }

  if (nodeCount && nodeCount !== "-") {
    specs.push({ label: labels.nodes, valueHtml: escapeHtml(nodeCount) });
  }

  if (ram) {
    specs.push({ label: labels.ram, valueHtml: escapeHtml(ram) });
  }

  if (gpu) {
    specs.push({ label: labels.gpu, valueHtml: escapeHtml(gpu) });
  }

  if (storage) {
    specs.push({ label: labels.storage, valueHtml: escapeHtml(storage) });
  }

  if (network) {
    specs.push({ label: labels.network, valueHtml: escapeHtml(network) });
  }

  if (comment) {
    specs.push({ label: labels.comment, valueHtml: escapeHtml(comment) });
  }

  if (owner) {
    specs.push({ label: labels.owner, valueHtml: escapeHtml(owner) });
  }

  if (liveUrl) {
    specs.push({
      label: labels.resource,
      valueHtml: `<a href="${escapeAttr(liveUrl)}" class="urlextern" rel="ugc nofollow">${escapeHtml(labels.resourceLinkText)}</a>`,
    });
  }

  return specs;
}

function stripClusterYearSuffix(name) {
  return (name || "").replace(/\s*\(\d{4}\)$/, "").trim();
}

function normalizeClusterKey(name) {
  return (name || "")
    .trim()
    .toLowerCase()
    .replace(/\s+/g, " ");
}

function appendDetailColumn(table, profileMap, labels, onDetailClick) {
  const headRow = table.querySelector("tr:first-child");
  if (!headRow) return;

  if (!headRow.querySelector(".reskin-hw-detail-head")) {
    const headCell = document.createElement("th");
    headCell.className = "reskin-hw-detail-head";
    headCell.textContent = labels.detail;
    headRow.appendChild(headCell);
  }

  const rows = Array.from(table.querySelectorAll("tr")).filter(
    (row, index) => index > 0 && row.querySelector("td")
  );

  rows.forEach((row) => {
    if (row.querySelector(".reskin-hw-detail-cell")) return;

    const clusterAnchor = findClusterAnchor(row);
    const clusterName = clusterAnchor ? clusterAnchor.textContent.trim() : "";
    const clusterKey = normalizeClusterKey(clusterName);
    if (!clusterKey || !profileMap.has(clusterKey)) return;

    const cell = document.createElement("td");
    cell.className = "reskin-hw-detail-cell";

    const button = document.createElement("button");
    button.type = "button";
    button.className = "btn btn-sm btn-outline-secondary reskin-hw-detail-btn";
    button.textContent = labels.detail;
    button.setAttribute("aria-label", `${labels.detailAriaPrefix}${clusterName}`);
    button.addEventListener("click", () => onDetailClick(clusterKey));

    cell.appendChild(button);
    row.appendChild(cell);
  });
}

function ensureDrawer(site, labels) {
  let drawer = document.getElementById("reskinHardwareDrawer");
  if (drawer) return drawer;

  const drawerMarkup = `
    <div class="offcanvas offcanvas-end reskin-offcanvas reskin-hw-drawer" tabindex="-1" id="reskinHardwareDrawer" aria-labelledby="reskinHardwareDrawerLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="reskinHardwareDrawerLabel" data-hw-drawer-title></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="${escapeAttr(labels.close)}"></button>
      </div>
      <div class="offcanvas-body">
        <img class="reskin-hw-drawer-image" data-hw-drawer-image alt="" hidden>
        <table class="table table-sm align-middle reskin-hw-drawer-table">
          <tbody data-hw-drawer-specs></tbody>
        </table>
      </div>
    </div>
  `;

  site.insertAdjacentHTML("beforeend", drawerMarkup);
  drawer = document.getElementById("reskinHardwareDrawer");
  return drawer;
}

function escapeHtml(value) {
  const text = String(value == null ? "" : value);
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function escapeAttr(value) {
  return escapeHtml(value);
}

})();
