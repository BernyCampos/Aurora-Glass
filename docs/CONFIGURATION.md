# Configuration reference

## Palettes

Colors accept six-digit hexadecimal values: `#RRGGBB`. Dark settings use the key shown below; light settings use the `light_` prefix.

| Key | Dark default | Light default | Target |
| --- | --- | --- | --- |
| `bg` | `#101216` | `#eef1f7` | Page background |
| `surface` | `#20232c` | `#ffffff` | Cards, forms, menus and dialogs |
| `accent` | `#a69afa` | `#6251bd` | Primary controls and active states |
| `secondary` | `#708cab` | `#4a8397` | Secondary background glow |
| `text` | `#eeeef5` | `#202637` | Themed text |
| `muted` | `#aeb1c2` | `#596477` | Secondary text |
| `danger` | `#fda4af` | `#b42342` | Themed danger buttons |
| `success` | `#6ee7b7` | `#15734e` | Themed success buttons |
| `warning` | `#fcd34d` | `#925b06` | Themed warning buttons |

Status indicators and resource thresholds supplied by Pelican retain their native semantics. Some native or third-party components can retain their own colors.

## Shared appearance

| Key | Default | Range | Unit |
| --- | --- | --- | --- |
| `opacity` | `0.72` | `0.35–1` | Surface alpha |
| `blur` | `24` | `0–48` | px |
| `radius` | `22` | `4–32` | px |
| `sidebar` | `250` | `200–320` | px |
| `font` | `14` | `12–18` | Body font size, px |
| `glow` | `0.22` | `0–0.5` | Ambient glow intensity |
| `border` | `0.14` | `0.05–0.35` | Glass highlight alpha |

Components with fixed native typography can retain their own font sizes. Dropdown panels are opaque for readability; their background follows `surface` rather than `opacity`.

## Storage and validation

Settings are persisted using Pelican's official `EnvironmentWriterTrait` in the panel's `.env` file, **not** in a database table. Values are available to the plugin through `config/aurora-glass.php` (loaded by Pelican with the plugin ID `aurora-glass`).

Each key uses the `AURORA_GLASS_` prefix. For example:

```dotenv
AURORA_GLASS_BG="#101216"
AURORA_GLASS_ACCENT="#a69afa"
AURORA_GLASS_LIGHT_ACCENT="#6251bd"
AURORA_GLASS_BLUR=24
AURORA_GLASS_OPACITY=0.72
```

All 25 keys are optional until the settings form is saved. Keys missing from `.env` fall back to defaults. Invalid configured values cause a fallback to the default palette and layout; the form validates ranges and hexadecimal colors before writing. Configuration is global, not per user. `preset` only changes form values and is not persisted.

`writeToEnvironment()` clears the framework config cache after saving; reload the page to see the updated theme. If you have previously built a config cache manually, run `php artisan config:clear` before opening settings.

### Upgrading from the database-backed 0.4.0 release

Older versions stored JSON in the `aurora_glass_settings` table. **Back up that table and export your old settings before upgrading** if you changed the defaults. 0.4.1 no longer reads or writes this table, and does not drop it. See [UPGRADING.md](UPGRADING.md) for migration instructions.

## Mode selection

- `html.dark`: dark palette.
- `html` without `dark`: light palette.
- System mode: Pelican resolves the active appearance; Aurora Glass follows that result.

There is no independent theme-mode toggle or client-side state in the plugin.
