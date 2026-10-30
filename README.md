# Aurora Glass

Pelican Panel appearance plugin. Version `0.4.2`.

## Requirements

- Pelican plugin API `1`, with `HasPluginSettings` and `EnvironmentWriterTrait`.
- Filament 5 component structure and CSS hooks targeted by the implementation.
- Pelican's PHP and Laravel dependencies. Node.js 22+ and Yarn/Corepack are required on the panel host to build theme assets at installation.
- A current browser supporting CSS `color-mix()` and `:has()`. Blur uses `backdrop-filter`, with a solid-surface fallback.

The exact Pelican release is not pinned in `plugin.json`. Verify compatibility when updating Pelican or Filament.

## Installation

1. Import `Aurora-Glass-0.4.2.zip` from the panel's plugin administration.
2. Complete installation and enable **Aurora Glass**.
3. Open the plugin's settings, save the appearance configuration and reload the page.

For manual deployment, place the runtime files in `plugins/aurora-glass/` and complete installation through Pelican. The directory name must match the manifest ID.

The manifest category is `theme`. Aurora Glass overlays Pelican components using a Vite theme that imports Filament's full base stylesheet before applying custom styles.

## Configuration

Settings are global to the panel. Users select Light, Dark or System using Pelican's native appearance control. The plugin follows the resolved `dark` class on the document root.

The settings form provides:

- Independent **Dark palette** and **Light palette** sections, each containing nine colors.
- Shared **Glass & layout** controls.
- Aurora Violet, Ocean, Graphite and Rose presets.

Selecting a preset replaces the form values with that preset's defaults, including layout values. **Custom** leaves the current form values unchanged. Changes require saving and reloading.

See [configuration reference](docs/CONFIGURATION.md).

## Runtime

- Loaded in the `app`, `admin` and `server` panels.
- Registers a theme using `$panel->viteTheme()` that imports Filament's base CSS first; only validated CSS custom properties are injected at runtime.
- Stores validated settings with Pelican's `EnvironmentWriterTrait` in `.env`, via `config/aurora-glass.php`.
- Adds no routes, commands, JavaScript or external requests. Vite compiles the included CSS during theme installation.
- Does not change server power actions, permissions or console transport.

## Updating and removal

If upgrading from v0.4.0, export any customized JSON settings first, before installing the new ZIP. See [upgrade guide](docs/UPGRADING.md). Install 0.4.2 using Pelican's plugin management screen, ensuring the panel can build its CSS assets.

Disabling the plugin stops the theme from loading and retains `AURORA_GLASS_*` settings in `.env`. Uninstalling 0.4.2 does not remove those keys. An old 0.4.0 settings table, if present, is not accessed or removed by 0.4.2.

## Source layout

```text
plugin.json
src/AuroraGlassPlugin.php
config/aurora-glass.php
resources/css/theme.css
docs/CONFIGURATION.md
docs/DEVELOPMENT.md
docs/screenshots/
```

CSS lives in `resources/css/theme.css` and is compiled with Pelican's Vite pipeline; runtime CSS variables come from `.env` configuration. See [development and packaging](docs/DEVELOPMENT.md).

## Screenshots

![Server console](docs/screenshots/console.png)
![Server list](docs/screenshots/servers.png)

[File manager](docs/screenshots/files.png) · [Plugin administration](docs/screenshots/plugins.png)

Screenshots show the installed dark appearance. Names, icons and third-party plugin entries are installation-specific.
