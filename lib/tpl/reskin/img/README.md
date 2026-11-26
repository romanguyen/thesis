# Branding assets

`metacentrum_RGB.svg` is the pre-existing local MetaCentrum/CESNET artwork used by
this template. Its Adobe Illustrator export identifies the original gray/orange
fills and a padded `0 0 582.4 200.5` viewBox. M2 of the visual alignment plan does
not replace, crop, recolor or import an SVG file.

The supervisor's `metacentrum-design/src/assets/metacentrum-logo.svg` contains the
same brand treatment with a tighter `40.8 40.8 500.8 118.9` viewBox. This explains
why equal CSS image heights do not produce equal visible logo sizes.

M3 introduces `metacentrum-compact.svg` for Top's header and fallback footer. It is
a local derivative of the existing `metacentrum_RGB.svg`, not an imported supervisor
asset: the 14 path/polygon elements and their fills are preserved, whitespace is
normalized, and the transparent protection-zone rectangles, Adobe metadata and DTD
are omitted. Its compact viewBox matches the supplied reference; layout gutters
provide surrounding clear space. Do not edit the original artwork to affect other layouts.

Top's header uses the compact image in both themes, with M2's white monochrome CSS
filter in dark mode. Its charcoal footer also retains the white treatment. Left/Clean
still use the unchanged original image. No sibling source path is loaded at runtime.

The supplied design is a visual reference, not a new asset license. This repository
does not establish additional redistribution or trademark permission. Retain the
existing project's authorization for its current artwork; confirm permission and
provenance with the supervisor/CESNET before importing partner logos or replacements.

## Current layout ownership — 2026-11-14

Top restores the pre-parity `0bd08b9` presentation and original `metacentrum_RGB.svg`; Left also retains the original. MetaCentrum (`style=metacentrum`) now owns the canonical reference header/footer artwork described below. The compact-header descriptions above are historical.

## Canonical shell artwork — 2026-10-26

Top parity M2 copies `reference-metacentrum-logo.svg`, `reference-cesnet-logo.svg`
and `reference-einfra-logo.svg` byte-for-byte from the supplied design project's
`src/assets/` equivalents. Their source hashes are recorded in
[the pinned build manifest](../vendor/metacentrum/manifest.json). No vector paths,
viewBoxes or brand colors are edited. The header uses the canonical public logo
slot with the local source image; its monochrome dark filter is a wiki-owned
legibility adaptation. Footer default logos are embedded in the canonical bundle;
the three local images also provide no-JavaScript/module-failure footer output.
Existing original/compact assets remain for historical evidence and alternative
layouts. This provenance does not expand the supplied artwork's usage rights.

## Homepage hero photo — 2026-11-26

`hero-infrastructure.jpg` (580×326 JPEG, SHA-256
`9a94b5e68ba0bcccd9dba88522aa45da4d2ad964464f5ccf69f88c7c0b53dbc2`) is a byte-for-byte copy of
<https://www.cesnet.cz/gimg/default/2/8/4/284-580-326.jpg>, which the homepage
hero previously hotlinked from cesnet.cz. It is CESNET's own image; the copy only
removes the runtime dependency and grants no new usage rights.
