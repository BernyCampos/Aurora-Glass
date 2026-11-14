import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const read = name => readFileSync(new URL('../' + name, import.meta.url), 'utf8');
const plugin = read('src/AuroraGlassPlugin.php');
const css = read('resources/css/theme.css');
const manifest = JSON.parse(read('plugin.json'));

test('Aurora 0.5.0 manifest targets Pelican native theme switcher', () => {
    assert.equal(manifest.id, 'aurora-glass');
    assert.equal(manifest.name, 'Aurora Glass');
    assert.equal(manifest.category, 'theme');
    assert.equal(manifest.api_version, 1);
    assert.equal(manifest.version, '0.5.0');
    assert.equal(manifest.panel_version, '1.0.0-rc.3');
    assert.deepEqual(manifest.panels, ['app', 'admin', 'server']);
    assert.equal(Object.hasOwn(manifest, 'meta'), false);
});

test('theme styling is registered only through HasTheme methods', () => {
    assert.match(plugin, /implements Plugin, HasPluginSettings, HasTheme/);
    for (const method of ['getThemeViteEntry', 'getThemeColors', 'getThemeFont', 'getThemeRenderHooks']) {
        assert.match(plugin, new RegExp('function ' + method + '\\('));
    }
    const register = plugin.split('public function register(Panel $panel): void')[1]
        .split('public function getThemeViteEntry()')[0];
    assert.doesNotMatch(register, /->(?:viteTheme|theme|colors|renderHook|sidebarWidth|topNavigation|defaultThemeMode)\(/);
    assert.match(plugin, /plugins\/aurora-glass\/resources\/css\/theme\.css/);
    assert.match(plugin, /Color::hex\(\$settings\['accent'\]\)/);
    assert.match(plugin, /PanelsRenderHook::HEAD_END/);
});

test('native Filament styles are not re-imported and Aurora CSS is active-only', () => {
    assert.doesNotMatch(css, /@import\b[^;]*filament/i);
    assert.match(css, /@scope\s*\(html:has\(meta\[name="aurora-glass-active"\]\)\)\s*\{/);
    assert.ok(css.trimEnd().endsWith('}'));
    assert.match(plugin, /<meta name="aurora-glass-active" content="0\.5\.0">/);
    assert.ok(plugin.includes('html:has(meta[name="aurora-glass-active"])'));
    assert.match(plugin, /id="aurora-glass-variables"/);
    assert.doesNotMatch(css, /\burl\(https?:\/\//);
});

test('legacy appearance settings and palettes persist unchanged', () => {
    for (const token of [
        "'light_bg'", "'light_accent'", "'bg'", "'accent'",
        "'opacity'", "'blur'", "'radius'", "'sidebar'",
        "'font'", "'glow'", "'border'",
    ]) assert.ok(plugin.includes(token), "Missing " + token);
    assert.match(plugin, /writeToEnvironment\(\$environment\)/);
    assert.match(read('config/aurora-glass.php'), /AURORA_GLASS_LIGHT_ACCENT/);
    assert.match(css, /:root:not\(\.dark\)/);
    assert.match(css, /:root\.dark/);
    assert.doesNotMatch(plugin, /DB::|Schema::|Artisan::call|chmod\(|chown\(/);
});
