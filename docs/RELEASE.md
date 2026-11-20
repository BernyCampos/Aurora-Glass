# Aurora Glass v0.5.2 — Pelican rc.4 compatibility

Aurora Glass v0.5.2 is the compatibility update for **Pelican Panel v1.0.0-rc.4**, released on 19 November 2026.

## What changed

- Update `plugin.json` to require Pelican **v1.0.0-rc.4**. The previous strict rc.3 constraint would mark the plugin incompatible on rc.4.
- Confirm that rc.4 preserves the official `App\Contracts\Plugins\HasTheme` methods, conditional Vite CSS, color registration and render hooks.
- Keep the root-variable `:scope` fix from v0.5.1, native Filament CSS and the transparent Aurora Glass layout.
- Preserve all 25 existing `.env` appearance settings and four presets; no migrations or Pelican core changes.
- Re-run CSS scope regression tests in Chromium, PHP 8.4/8.5 syntax checks and package/checksum validation.

## Compatibility

- **Aurora Glass v0.5.2:** Pelican Panel **v1.0.0-rc.4** (plugin API 1)
- **Aurora Glass v0.5.1:** Pelican Panel **v1.0.0-rc.3**
- **Aurora Glass v0.4.2:** Older rc.2 installations

The panel version in the manifest is intentionally exact; we do not claim future untested releases.

## Download files

- `Aurora-Glass-0.5.2.zip` — installable Pelican plugin.
- `Aurora-Glass-0.5.2-source.zip` — source, tests and docs.
- `SHA256SUMS.txt` — archive SHA-256 checksums.
- `update.json` — rc.4-specific update feed entry.

## Validation and remaining checks

Automated checks validate the theme interface structure, glass styling, light/dark behavior, isolation when another theme is active, archive integrity and PHP syntax. A full installation and live end-to-end walkthrough on an rc.4 panel (admin, app, server console/files, plugin settings, theme switching, mobile and upgrades) is still required to verify runtime presentation.

GitHub Releases do not automatically update the Pelican Hub Marketplace listing or Hub's hosted update feed.
