# Aurora Glass v0.5.1 — Glass surfaces restored

Aurora Glass now supports Pelican Panel's built-in theme selector through the official `App\Contracts\Plugins\HasTheme` interface.

## Bug fixed in v0.5.1

The v0.5.0 CSS isolation used root selectors that failed to match the scoping root inside `@scope`. This prevented glass design variables from reaching cards and other components. v0.5.1 uses `:scope` for root-level variables and light/dark overrides, restoring translucent cards, borders, corners and palette colors while keeping the native Pelican theme selector.

## Features retained from v0.5.0

- Aurora appears in the theme picker in the user menu and profile. Administrators can choose it as the default or forced theme.
- Aurora CSS, Filament colors and dynamically configured variables are loaded only while Aurora is selected.
- Replaced the legacy `viteTheme()` integration with the new `getThemeViteEntry()`, `getThemeColors()`, `getThemeFont()` and `getThemeRenderHooks()` methods.
- Removed the duplicate Filament stylesheet import and scoped Aurora's style rules to its active theme.
- Preserved all 25 `.env`-backed settings, independent light/dark palettes, and Violet/Ocean/Graphite/Rose presets.
- Added automated tests and deterministic release archives; no migrations or Pelican core modifications.

## Compatibility

**Requires Pelican Panel 1.0.0-rc.3** (plugin API 1). Do not install this version on rc.2; Aurora Glass v0.4.2 remains available for older panels.

## Download files

- `Aurora-Glass-0.5.1.zip` — installable Pelican plugin (use this for the panel or Marketplace).
- `Aurora-Glass-0.5.1-source.zip` — developer source with documentation, tests, and workflows.
- `SHA256SUMS.txt` — SHA-256 checksums for both ZIP files.
- `update.json` — rc.3-specific update feed entry with package checksum.

## Verification

GitHub Actions validates PHP syntax on 8.4 and 8.5, the HasTheme contract, style isolation, manifest compatibility, archive integrity and checksums. Switching between multiple themes, default/forced selection, and the three panel layouts still require a live rc.3 staging check before production rollout.
