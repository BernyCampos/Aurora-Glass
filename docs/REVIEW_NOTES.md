# Marketplace resubmission notes — Aurora Glass 0.4.2

Thank you for the review. This release addresses the two requests made for version 0.4.0:

1. **Remove the migration and use `.env` settings:** `database/migrations/` was removed. The plugin now imports `App\Traits\EnvironmentWriterTrait`, calls `writeToEnvironment()` in `saveSettings()` with `AURORA_GLASS_*` keys, and reads them via `config/aurora-glass.php`. No theme-settings database queries remain.
2. **Separate CSS from PHP using `viteTheme()`:** static visual rules now live in `resources/css/theme.css`; `register()` calls `$panel->viteTheme('plugins/aurora-glass/resources/css/theme.css')`. Only the 25 validated CSS custom properties and derived primary palette values are emitted at runtime, so the theme can remain configurable from the Pelican plugin settings page.

This release retains all original theme options and includes a guide for preserving existing v0.4.0 settings during upgrade. CSS building relies on Pelican's normal theme installation pipeline (Node.js/Yarn). Static checks were performed; live Pelican installation testing should still be done before submission.

3. **Fix Filament stylesheet regression found during live testing:** Aurora Glass 0.4.1 accidentally replaced the Filament theme without importing its base stylesheet. Version 0.4.2 starts `resources/css/theme.css` with `@import '../../../../vendor/filament/filament/resources/css/theme.css';` to preserve base layout, typography, and component classes before Aurora Glass overrides.
