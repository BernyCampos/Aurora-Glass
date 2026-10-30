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

Table: `aurora_glass_settings`.

| Column | Type | Role |
| --- | --- | --- |
| `id` | Unsigned tiny integer, primary key | Single settings row, value `1` |
| `settings` | Text | JSON object containing the validated configuration |

Unknown keys are discarded on read. Missing keys use defaults. Invalid stored settings cause the complete default configuration to be used. If the table is absent, the theme renders with defaults; saving displays an initialization error.

`preset` is a form control and is not persisted. Settings are stored globally, not per user.

## Mode selection

- `html.dark`: dark palette.
- `html` without `dark`: light palette.
- System mode: Pelican resolves the active appearance; Aurora Glass follows that result.

There is no independent theme-mode toggle or client-side state in the plugin.
