import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, writeFileSync, mkdtempSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { pathToFileURL } from 'node:url';
import { spawnSync } from 'node:child_process';

const css = readFileSync(new URL('../resources/css/theme.css', import.meta.url), 'utf8');

function findChrome() {
    for (const name of ['google-chrome', 'google-chrome-stable', 'chromium', 'chromium-browser']) {
        const found = spawnSync('which', [name], { encoding: 'utf8' });
        if (found.status === 0) return found.stdout.trim();
    }
    throw new Error('Headless Chrome/Chromium is required for the scoped CSS regression test.');
}

function renderWithChrome(chrome, dir, active, dark) {
    const file = join(dir, (active ? 'aurora' : 'native') + '-' + (dark ? 'dark' : 'light') + '.html');
    const html = '<!DOCTYPE html><html class="' + (dark ? 'dark' : '') + '"><head>'
        + (active ? '<meta name="aurora-glass-active" content="0.5.1">' : '')
        + '<style>html{'
        + '--ag-bg:#101216;--ag-surface:#20232c;--ag-accent:#a69afa;'
        + '--ag-secondary:#708cab;--ag-text:#eeeef5;--ag-muted:#aeb1c2;'
        + '--ag-danger:#fda4af;--ag-success:#6ee7b7;--ag-warning:#fcd34d;'
        + '--ag-light_bg:#eef1f7;--ag-light_surface:#ffffff;'
        + '--ag-light_accent:#6251bd;--ag-light_secondary:#4a8397;'
        + '--ag-light_text:#202637;--ag-light_muted:#596477;'
        + '--ag-light_danger:#b42342;--ag-light_success:#15734e;'
        + '--ag-light_warning:#925b06;--ag-opacity:0.72;--ag-blur:24px;'
        + '--ag-radius:22px;--ag-sidebar:250px;--ag-font:14px;'
        + '--ag-glow:0.22;--ag-border:0.14;}</style>'
        + '<style>' + css + '</style></head>'
        + '<body><div class="fi-layout"><section class="fi-section">Test card</section></div>'
        + '<script>const root=getComputedStyle(document.documentElement);'
        + 'const card=getComputedStyle(document.querySelector(".fi-section"));'
        + 'document.body.setAttribute("data-check",btoa(JSON.stringify({'
        + 'fill:root.getPropertyValue("--ag-fill").trim(),'
        + 'round:root.getPropertyValue("--ag-round").trim(),'
        + 'activeBackground:root.getPropertyValue("--ag-bg").trim(),'
        + 'radius:card.borderTopLeftRadius,cardBackground:card.backgroundImage'
        + '})));</script></body></html>';
    writeFileSync(file, html, 'utf8');
    const proc = spawnSync(chrome, [
        '--headless=new', '--no-sandbox', '--disable-gpu',
        '--disable-dev-shm-usage', '--disable-software-rasterizer',
        '--dump-dom', pathToFileURL(file).href,
    ], { encoding: 'utf8', timeout: 25000, maxBuffer: 2_000_000 });
    assert.equal(proc.status, 0, 'Chrome fixture failed: ' + proc.stderr?.slice(-900));
    const match = proc.stdout.match(/data-check="([^"]+)"/);
    assert.ok(match, 'Chrome omitted computed-style report: ' + proc.stdout.slice(-700));
    return JSON.parse(Buffer.from(match[1], 'base64').toString('utf8'));
}

test('Chrome computes glass variables and restores native styles without Aurora', () => {
    const chrome = findChrome();
    const dir = mkdtempSync(join(tmpdir(), 'aurora-css-'));
    try {
        const dark = renderWithChrome(chrome, dir, true, true);
        assert.ok(dark.fill.includes('color-mix('), JSON.stringify(dark));
        assert.ok(dark.round.includes('clamp('), JSON.stringify(dark));
        assert.equal(dark.radius, '22px', JSON.stringify(dark));
        assert.equal(dark.activeBackground, '#101216', JSON.stringify(dark));

        const light = renderWithChrome(chrome, dir, true, false);
        assert.equal(light.activeBackground, '#eef1f7', JSON.stringify(light));
        assert.equal(light.radius, '22px', JSON.stringify(light));

        const native = renderWithChrome(chrome, dir, false, true);
        assert.equal(native.fill, '', JSON.stringify(native));
        assert.equal(native.radius, '0px', JSON.stringify(native));
    } finally {
        rmSync(dir, { recursive: true, force: true });
    }
});
