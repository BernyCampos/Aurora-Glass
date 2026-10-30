# Changelog

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
