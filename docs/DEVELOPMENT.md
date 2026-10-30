# Development

## Entry point

`plugin.json` maps namespace `AuroraGlass` and class `AuroraGlassPlugin` to `src/AuroraGlassPlugin.php`. Pelican provides autoloading and framework dependencies.

The class implements:

- `Filament\Contracts\Plugin`.
- `App\Contracts\Plugins\HasPluginSettings`.

`register()` adds a `panels::head.end` callback. The callback concatenates the static `AURORA_CSS` nowdoc and validated settings variables into `<style id="aurora-glass">`.

`getSettingsFormData()`, `getSettingsForm()` and `saveSettings()` provide the native administrative settings integration. Writes use Laravel's query builder. The plugin uses no direct filesystem or process functions.

## Editing styles

Edit the `AURORA_CSS` block in `src/AuroraGlassPlugin.php`. Palette defaults and configuration limits are defined separately in `DEFAULTS`, `LABELS` and `RANGES`.

When adding a setting:

1. Add its default and English label.
2. Add numeric bounds in `RANGES`, or use a six-digit hex color.
3. Add a static CSS fallback when required.
4. Update the settings form grouping and configuration reference.

Runtime values are emitted as `--ag-<key>` variables. Light keys retain their underscore, for example `--ag-light_bg`. Light-mode selectors map those values to the active variables. Primary Filament shades are derived from the active accent.

Do not insert unvalidated user content into the CSS block.

## Component scope

The stylesheet targets Filament hook classes for sections, tabs, inputs, buttons, tables, dialogs, dropdowns and server stats.

The server grid fix also targets Pelican's `server-entry` structure: a `wire:poll.15s` element with `relative cursor-pointer` classes and its `dark:bg-gray-800` surface. Recheck these selectors after upstream component changes.

Tenant dropdowns remain opaque and scroll internally. The plugin does not replace Livewire components or their event handlers.

## Migration

The migration creates only `aurora_glass_settings`. Installation is idempotent when the table already exists. The down migration drops that table.

No new migration is required for additional JSON settings keys; missing keys are merged from defaults.

## Validation

Syntax checks, from the repository root:

```sh
php -l src/AuroraGlassPlugin.php
php -l database/migrations/2026_10_29_000001_create_aurora_glass_settings_table.php
```

On a Pelican test installation, check:

1. Import, install, enable and open plugin settings.
2. Save both palettes; reload and verify persistence.
3. Switch Light, Dark and System appearance.
4. Inspect forms, selected options, menus, tables and server grid cards.
5. Verify keyboard focus, disabled controls and narrow layouts.
6. Disable the plugin and verify the native panel appearance returns.

Version 0.4.0 received static PHP/CSS parsing and browser checks against local component fixtures. User-supplied screenshots show the plugin enabled in an installed panel. Automated full-panel integration tests are not included.

## Runtime release

Create a ZIP containing only the manifest, PHP entry point and migration:

```sh
zip -r ../Aurora-Glass-0.4.0.zip plugin.json src database
```

Pelican accepts the manifest at the archive root or one directory below it. If using a containing directory, name it `aurora-glass`.

Exclude documentation, screenshots, development dependencies, local fixtures and environment files from the runtime ZIP. Do not import the GitHub source archive as the release package.

The PHP source is deployable as supplied; npm, Composer installation and CSS compilation are not required for this plugin.

## Integration constraints

- The panel content security policy must allow the injected style block. A stricter CSP requires an adapted asset-loading strategy.
- Native and third-party component markup can change across Pelican or Filament releases.
- Modern CSS support is required; the blur fallback does not replace support for `color-mix()` or `:has()`.
- Reduced-motion and reduced-transparency preferences are handled where supported by the browser.
