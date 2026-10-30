# Development

## Entry point

`plugin.json` maps namespace `AuroraGlass` and class `AuroraGlassPlugin` to `src/AuroraGlassPlugin.php`. Pelican provides autoloading and framework dependencies.

The class implements:

- `Filament\Contracts\Plugin`.
- `App\Contracts\Plugins\HasPluginSettings`.

`register()` registers a standalone Vite stylesheet at `plugins/aurora-glass/resources/css/theme.css` and uses a small `panels::head.end` hook only for validated, dynamically generated CSS variables. The static component styles are not embedded in PHP.

`getSettingsFormData()`, `getSettingsForm()` and `saveSettings()` provide the native administrative settings integration. Settings are written with Pelican's `EnvironmentWriterTrait` to `.env` and loaded through the plugin's `config/aurora-glass.php`. There are no plugin tables or migrations.

## Editing styles

Edit `resources/css/theme.css`. Palette defaults and configuration limits are defined in `DEFAULTS`, `LABELS` and `RANGES` in `src/AuroraGlassPlugin.php`. Keep `config/aurora-glass.php` defaults in sync with `DEFAULTS`.

When adding a setting:

1. Add its default and English label.
2. Add numeric bounds in `RANGES`, or use a six-digit hex color.
3. Add a static CSS fallback when required.
4. Update the settings form grouping and configuration reference.

Runtime values are emitted as `--ag-<key>` variables. Light keys retain their underscore, for example `--ag-light_bg`. Light-mode selectors map those values to the active variables. Primary Filament shades are derived from the active accent.

Do not place unvalidated values in the dynamic CSS variable output.

## Component scope

The stylesheet targets Filament hook classes for sections, tabs, inputs, buttons, tables, dialogs, dropdowns and server stats.

The server grid fix also targets Pelican's `server-entry` structure: a `wire:poll.15s` element with `relative cursor-pointer` classes and its `dark:bg-gray-800` surface. Recheck these selectors after upstream component changes.

Tenant dropdowns remain opaque and scroll internally. The plugin does not replace Livewire components or their event handlers.

## Data storage and upgrade

No database migrations are shipped. New settings are saved into `.env` with plugin-prefixed keys. Existing 0.4.0 installations should export any customized settings before upgrading; the old table is left untouched. See [UPGRADING.md](UPGRADING.md).

## Build

`$panel->viteTheme(...)` uses Pelican's Vite pipeline. The panel automatically includes `plugins/*/resources/css/**/*.css` as Vite inputs. Installing this **theme** rebuilds frontend assets; Node.js 22+ and Yarn/Corepack must be installed on the panel host. Use `yarn install && yarn build` in the Pelican panel root if assets need to be rebuilt manually.

## Validation

Syntax checks, from the repository root:

```sh
php -l src/AuroraGlassPlugin.php
php -l config/aurora-glass.php
```

On a Pelican test installation, check:

1. Import, install, enable and open plugin settings.
2. Save both palettes; reload and verify persistence.
3. Switch Light, Dark and System appearance.
4. Inspect forms, selected options, menus, tables and server grid cards.
5. Verify keyboard focus, disabled controls and narrow layouts.
6. Disable the plugin and verify the native panel appearance returns.

Version 0.4.2 has PHP syntax, configuration consistency and CSS structure checks. End-to-end installation and visual verification on a running Pelican panel are still required.

## Runtime release

Create a ZIP containing a single root directory named `aurora-glass`, containing the manifest, PHP entry point, configuration and static stylesheet:

```text
aurora-glass/
  plugin.json
  src/AuroraGlassPlugin.php
  config/aurora-glass.php
  resources/css/theme.css
```

The runtime ZIP does not need the documentation or screenshots. Vite compiles CSS as part of installing a `theme` plugin; unlike the old inline CSS release, this version requires Node.js 22+ and Yarn/Corepack on the panel host.

## Integration constraints

- The panel content security policy must permit the small inline **variable-only** style block. Static theme rules use a Vite-generated CSS asset.
- Native and third-party component markup can change across Pelican or Filament releases.
- Modern CSS support is required; the blur fallback does not replace support for `color-mix()` or `:has()`.
- Reduced-motion and reduced-transparency preferences are handled where supported by the browser.
