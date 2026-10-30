# Upgrading from Aurora Glass 0.4.0 to 0.4.1

Aurora Glass 0.4.1 no longer uses the `aurora_glass_settings` database table. Settings now belong to Pelican's `.env` file and are read from `config/aurora-glass.php`. The old database table is **not** deleted by the update.

## Before updating

1. Back up your Pelican `.env` and database. Do not share the full `.env` file publicly; it contains secrets.
2. If you changed your 0.4.0 theme settings, export the existing JSON object:

```sql
SELECT settings FROM aurora_glass_settings WHERE id = 1;
```

3. For each key in the JSON, put its corresponding key/value into the Pelican `.env` file, using uppercase `AURORA_GLASS_` followed by the field name. Example: `light_bg` becomes `AURORA_GLASS_LIGHT_BG`. Color values should be in double quotes, e.g. `AURORA_GLASS_LIGHT_BG="#eef1f7"`. Numeric settings can be written unquoted.
4. Do not delete any preexisting `.env` keys. Avoid changing other panel settings.
5. Install/update Aurora Glass 0.4.1. Check that the appearance settings page shows the same values. Save the form, then reload the panel.
6. If a Laravel config cache was created manually, run `php artisan config:clear` in the Pelican panel directory. If the new stylesheet does not appear, check that Node.js 22+ and Yarn/Corepack are available and rebuild with `yarn install && yarn build`.

## Existing table

The old `aurora_glass_settings` table may remain as unused data. Keep it as a fallback backup until the appearance is fully verified. This version intentionally does not include SQL code or an uninstall migration to drop it.

## New installations

No migration or manual `.env` editing is required. Install the theme, open plugin settings, select a preset or fine-tune values and save.
