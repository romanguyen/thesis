<?php
if (!defined('DOKU_INC')) die();

/** Pinned dependencies retain their existing order, versions and integrity policy; the web font is local (css/tokens.css). */
function reskin_external_stylesheets(): array
{
    return [
        ['url' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css', 'integrity' => 'sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB', 'crossorigin' => 'anonymous'],
        ['url' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', 'integrity' => 'sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+', 'crossorigin' => 'anonymous'],
    ];
}

/**
 * CESNET: brand tokens and Bootstrap overrides, wiki content, components, home sections; Left adds its sidebar frame.
 * MetaCentrum (frame `clean`) loads none of them: the design's tokens, Carbon reset and components,
 * then its own layout/native adapter.
 * @param string $frame top|left|clean (reskin_variant_frame(); clean = MetaCentrum style)
 */
function reskin_css_paths(string $frame): array
{
    if ($frame === 'clean') return ['vendor/metacentrum/tokens.css', 'vendor/metacentrum/foundation.css', 'vendor/metacentrum/components.css', 'css/layouts/clean.css'];
    $paths = ['css/tokens.css', 'css/bootstrap-overrides.css', 'css/dokuwiki.css', 'css/components.css', 'css/home.css'];
    return $frame === 'left' ? array_merge($paths, ['css/frames/left.css']) : $paths;
}

/** Named file/URL boundary; root and URL base may be supplied for portable checks. */
function reskin_local_asset(string $path, ?string $base = null, ?string $root = null): array
{
    $root = $root ?? dirname(__DIR__) . '/';
    $base = $base ?? tpl_basedir();
    $mtime = @filemtime(rtrim($root, '/') . '/' . $path);
    return ['path' => $path, 'url' => rtrim($base, '/') . '/' . $path . '?v=' . ($mtime ?: '1')];
}

function reskin_stylesheet_assets(array $variant): array
{
    $frame = reskin_variant_frame($variant);
    $external = reskin_external_stylesheets();
    // MetaCentrum keeps Bootstrap for grid and behaviour; its font and icons are the design's.
    if ($frame === 'clean') $external = [$external[0]];
    return array_merge($external, array_map('reskin_local_asset', reskin_css_paths($frame)));
}

/** Preserve the existing feature applicability, including hardware on non-show actions. */
function reskin_script_paths(array $context, bool $hasToc): array
{
    // MetaCentrum is light only (no theme script), lists every navigation item and shows stories as plain cards.
    $design = $context['variant']['style'] === 'metacentrum';
    $paths = $design
        ? ['js/variant-links.js', 'js/search.js', 'js/header.js', 'js/design-header.js', 'js/forms.js']
        : ['js/theme.js', 'js/variant-links.js', 'js/search.js', 'js/header.js'];
    if ($hasToc) $paths[] = 'js/page-toc.js';
    if (!$design && $context['show']['top_navigation']) $paths[] = 'js/topnav-overflow.js';
    if (!$design && ($context['show']['hero'] || $context['show']['stories_inline'])) $paths[] = 'js/story-slider.js';
    if ($context['hardware']) $paths[] = 'js/hardware-drawer.js';
    return $paths;
}

/** Called at the late native script stage, after TOC availability is known. */
function reskin_script_assets(array $context, bool $hasToc): array
{
    return array_merge([
        ['url' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js', 'integrity' => 'sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI', 'crossorigin' => 'anonymous'],
    ], array_map('reskin_local_asset', reskin_script_paths($context, $hasToc)));
}
