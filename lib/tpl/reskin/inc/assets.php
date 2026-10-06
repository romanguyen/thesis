<?php
if (!defined('DOKU_INC')) die();

/** Pinned dependencies retain their existing order, versions and integrity policy. */
function reskin_external_stylesheets(): array
{
    return [
        ['url' => 'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;600&family=Source+Sans+3:wght@400;500;600;700&family=Open+Sans:wght@400;500;600;700&display=swap'],
        ['url' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', 'integrity' => 'sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH', 'crossorigin' => 'anonymous'],
        ['url' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', 'integrity' => 'sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+', 'crossorigin' => 'anonymous'],
    ];
}

/** Shared order matches the former import umbrella; exactly one layout follows it. */
function reskin_css_paths(string $layout): array
{
    $layout = reskin_layout_variant($layout)['layout'];
    return ['css/base.css', 'css/home.css', 'css/components.css', 'css/layout.css', 'css/responsive.css', 'css/layouts/' . $layout . '.css'];
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
    return array_merge(reskin_external_stylesheets(), array_map('reskin_local_asset', reskin_css_paths($variant['layout'])));
}

/** Preserve the existing feature applicability, including hardware on non-show actions. */
function reskin_script_paths(array $context, bool $hasToc): array
{
    $paths = ['js/theme.js', 'js/search.js'];
    if ($hasToc) $paths[] = 'js/page-toc.js';
    if ($context['show']['top_navigation']) $paths[] = 'js/topnav-overflow.js';
    if ($context['show']['hero'] || $context['show']['stories_inline']) $paths[] = 'js/story-slider.js';
    if ($context['hardware']) $paths[] = 'js/hardware-drawer.js';
    return $paths;
}

/** Called at the late native script stage, after TOC availability is known. */
function reskin_script_assets(array $context, bool $hasToc): array
{
    return array_merge([
        ['url' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', 'integrity' => 'sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz', 'crossorigin' => 'anonymous'],
    ], array_map('reskin_local_asset', reskin_script_paths($context, $hasToc)));
}
