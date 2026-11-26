# Pinned MetaCentrum shell

| File | Origin |
| --- | --- |
| `mc-elements.js`, `.map` | Unmodified `dist/` output: canonical `mc-header`/`mc-footer`, Lit runtime, shadow-scoped Carbon header styles, embedded artwork |
| `tokens.css` | Unmodified `dist/tokens/variables.css` (`--mc-*`) |
| `foundation.css` | Unmodified `dist/foundation.css` (Carbon reset and theme) |
| `components.css` | Compiled from `components.scss`: the design's `dist/index.css`, the Carbon list/link/type it does not compile, and native DokuWiki markup mapped onto those classes with `@extend` |

Only the MetaCentrum style loads them. Global Carbon styles were added on 2026-11-24, reversing the earlier rule, so this style stops being a blend of two designs; React is still not installed.

Update: export the chosen revision to a scratch directory and run `node build.mjs <scratch copy>`. It uses the design's lockfile, copies its output unmodified, compiles `components.scss` and records every hash in `manifest.json`. It refuses to build inside a checkout. The 2026-11-24 rebuild reproduced the same `mc-elements.js` hash. Never patch generated files. Wiki URLs, authentication, search/preferences, home-link logo, skip target and page offset belong to native PHP models, the design partials, `js/design-header.js` and `css/layouts/clean.css`.

Current build: `30de28a` (2026-11-21), updated 2026-11-24 from `9220452`. `mc-header`
now renders a sidebar (top bar below 66rem), has no `home-href` and needs no overflow
handling. Its `src/assets/metacentrum-logo-black.svg` is new but unused by the bundle,
so no reference copy is kept. The git-ignored upstream playground predates this
revision (it still passes `homeHref`).

Historical implementation evidence: `thesis/design-parity/2026-10-26-m2/` from the DokuWiki
repository root, captured before the design moved from Top to MetaCentrum on 2026-11-14. Older Top screenshots and compact-header/footer exceptions are
historical evidence, not the acceptance target for this shell.
