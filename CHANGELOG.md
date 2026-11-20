# Changelog

## 0.5.2 — Pelican Panel v1.0.0-rc.4 compatibility

- Declare exact compatibility with Pelican Panel v1.0.0-rc.4, released 19 November 2026; an rc.3-only manifest would otherwise be marked incompatible after upgrading the panel.
- Verify the upstream rc.4 `HasTheme` contract, `ThemeService`, Vite entry discovery and plugin loading remain compatible with Aurora's implementation.
- Retain the scoped glass styles, root CSS variables, dark/light palettes, all 25 saved settings, and the four presets unchanged.
- Update version metadata, automated regression checks, release notes and deterministic archives. No migrations, direct core changes or new runtime dependencies.
- Keep Aurora Glass 0.5.1 available for rc.3 and 0.4.2 for rc.2.

## 0.5.1 — Restore scoped glass variables

- Fix a v0.5.0 CSS regression: `:root` did not match the root of Aurora's `@scope`, so glass surfaces lost their CSS variables.
- Restore glass cards, borders, rounded corners and correct light/dark palette mapping with `:scope` selectors.
- Keep the native `HasTheme` selector, Filament base styles, existing 25 preferences and presets without changing Pelican core.
- Add a regression test and publish corrected rc.3 installation ZIP.

## 0.5.0 — Native Pelican theme switcher

- Implements Pelican's official `App\Contracts\Plugins\HasTheme` interface, introduced in Panel v1.0.0-rc.3.
- Loads Aurora's Vite stylesheet, color palette and dynamic CSS variables only when Aurora Glass is selected.
- Removes the old `viteTheme()` call and avoids re-importing Filament's foundational stylesheet.
- Adds active-theme CSS scoping to prevent styles from affecting other selected themes.
- Preserves all 25 environment-backed visual settings, light/dark palettes and four presets without migrations or core changes.
- Adds standalone theme-selector checks, deterministic install/source ZIPs, SHA-256 checksums and automated GitHub Releases.
- Requires Pelican Panel v1.0.0-rc.3; Aurora Glass v0.4.2 remains available for older installations.

## 0.4.2 — Filament layout regression fix

- Restored the Filament base stylesheet import in `resources/css/theme.css`; `viteTheme()` replaces the active theme, so without the import the interface loses its layout, sizes, spacing and component styles.
- Preserved all 25 `.env`-backed settings, light/dark palettes, and theme presets.
- Updated documentation for the corrected installable package.
- No database migrations or schema changes.


## 0.4.1 — Marketplace review fixes

- Removed the database migration and all database read/write operations used for theme settings.
- Added Pelican-native `.env` persistence via `EnvironmentWriterTrait` with `AURORA_GLASS_*` keys.
- Added `config/aurora-glass.php` and kept all 25 existing configuration defaults and validation limits.
- Extracted every static component style into `resources/css/theme.css`, registered through Filament's `viteTheme()`.
- Kept the small dynamic CSS-variable style hook so runtime customization continues to work without per-setting builds.
- Preserved the light/dark palettes, Ocean/Graphite/Rose/Violet presets, and all appearance controls.
- Added an upgrade guide to preserve settings from the old v0.4.0 database table. The update does not delete that table.
- Updated documentation and runtime packaging for Pelican's theme asset build.

## 0.4.0

- Initial marketplace submission with customizable glass surfaces and persistent settings.
