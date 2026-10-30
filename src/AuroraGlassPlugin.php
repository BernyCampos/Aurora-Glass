<?php

declare(strict_types=1);

namespace AuroraGlass;

use App\Contracts\Plugins\HasPluginSettings;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Filament\Panel;
use Illuminate\Support\HtmlString;

final class AuroraGlassPlugin implements Plugin, HasPluginSettings
{
    public function getId(): string { return 'aurora-glass'; }

    public function register(Panel $panel): void
    {
        $panel->renderHook('panels::head.end', static function (): HtmlString {
            $css = <<<'AURORA_CSS'
/* Aurora Glass — configurable tokens. No external assets. */
:root {
 --ag-light_bg: #eef1f7;
 --ag-light_surface: #ffffff;
 --ag-light_accent: #6251bd;
 --ag-light_secondary: #4a8397;
 --ag-light_text: #202637;
 --ag-light_muted: #596477;
 --ag-light_danger: #b42342;
 --ag-light_success: #15734e;
 --ag-light_warning: #925b06;

 --ag-danger: #fda4af;
 --ag-success: #6ee7b7;
 --ag-warning: #fcd34d;
 --ag-bg: #101216;
 --ag-surface: #20232c;
 --ag-accent: #a69afa;
 --ag-secondary: #708cab;
 --ag-text: #eeeef5;
 --ag-muted: #aeb1c2;
 --ag-opacity: 0.72;
 --ag-blur: 24px;
 --ag-radius: 22px;
 --ag-sidebar: 250px;
 --ag-font: 14px;
 --ag-glow: 0.22;
 --ag-border: 0.14;
}
/* Aurora Glass 0.3 — component styles, no behavioral overrides. */
:root{--sidebar-width:var(--ag-sidebar);--ag-line:color-mix(in srgb,var(--ag-text) 9%,transparent);--ag-fill:color-mix(in srgb,var(--ag-surface) calc(var(--ag-opacity)*100%),transparent);--ag-control:color-mix(in srgb,var(--ag-surface) 44%,transparent);--ag-hover:color-mix(in srgb,var(--ag-accent) 12%,transparent);--ag-round:clamp(8px,calc(var(--ag-radius)*.5),16px)}
body:has(.fi-layout),body:has(.fi-simple-layout),body.ag-preview {
 background:radial-gradient(ellipse at 85% 0%,color-mix(in srgb,var(--ag-accent) calc(var(--ag-glow)*100%),transparent),transparent 58%),radial-gradient(ellipse at 5% 95%,color-mix(in srgb,var(--ag-secondary) 14%,transparent),transparent 60%),var(--ag-bg) !important;
 background-attachment:fixed !important;color:var(--ag-text);font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;font-size:var(--ag-font);min-height:100dvh;
}
/* Glass belongs only on contained surfaces. Tabs are a separate Filament component. */
:is(.fi-sc-tabs.fi-contained,.fi-section:not(.fi-section-not-contained):not(.fi-aside),.fi-section.fi-aside > .fi-section-content-ctn,.fi-ta-ctn,.fi-simple-main,.fi-sidebar,.fi-topbar,.glass){
 background:linear-gradient(135deg,color-mix(in srgb,var(--ag-text) 3%,transparent),transparent 50%),var(--ag-fill) !important;
 border:0 !important;border-radius:var(--ag-radius) !important;
 box-shadow:inset 0 1px 0 color-mix(in srgb,var(--ag-text) calc(var(--ag-border)*100%),transparent),0 12px 40px #0000000d !important;
 backdrop-filter:blur(var(--ag-blur)) saturate(135%);-webkit-backdrop-filter:blur(var(--ag-blur)) saturate(135%);
}
.fi-sc-tabs.fi-contained > .fi-tabs {background:transparent !important;border:0 !important;box-shadow:none !important;border-bottom:1px solid var(--ag-line) !important;border-radius:0 !important;padding:12px 16px !important}
.fi-sc-tabs-tab,.fi-section-content,.fi-section-content-ctn {background:transparent}
.fi-section-content-ctn,.fi-section-header,.fi-section-footer {border-color:var(--ag-line) !important}
.fi-section.fi-section-not-contained {background:transparent !important;border:0 !important;box-shadow:none !important;backdrop-filter:none !important}
.fi-topbar {margin:8px;width:auto}.fi-topbar nav{background:transparent !important;border:0 !important;box-shadow:none !important}
.fi-sidebar{margin:8px;max-height:calc(100dvh - 16px);scrollbar-width:thin;scrollbar-color:var(--ag-line) transparent}
:is(.fi-header-heading,.fi-section-header-heading,.fi-sidebar-item-label,.fi-ta-text,.fi-fo-field-wrp-label,.fi-fo-field-label,.fi-modal-heading){color:var(--ag-text) !important}
:is(.fi-section-header-description,.fi-header-subheading,.fi-sidebar-group-label,.fi-fo-field-wrp-helper-text,.fi-fo-field-helper-text,.fi-modal-description){color:var(--ag-muted) !important}
.fi-sidebar-item-btn{border-radius:var(--ag-round) !important;transition:background-color .16s ease}
.fi-sidebar-item-btn:hover{background:var(--ag-hover) !important}
.fi-sidebar-item-active .fi-sidebar-item-btn {background:var(--ag-hover) !important}
.fi-sidebar-item-active .fi-sidebar-item-icon{color:var(--ag-accent) !important}
/* Quiet tabs; the active state remains distinct without a boxed navigation strip. */
.fi-tabs-item{border:0 !important;box-shadow:none !important;background:transparent !important;border-radius:var(--ag-round) !important;color:var(--ag-muted) !important;transition:background-color .16s ease,color .16s ease}
.fi-tabs-item:hover{background:color-mix(in srgb,var(--ag-text) 4%,transparent) !important;color:var(--ag-text) !important}
.fi-tabs-item:is(.fi-active,.fi-tabs-item-active,[aria-selected="true"]){background:var(--ag-hover) !important;color:var(--ag-accent) !important}
.fi-tabs-item .fi-icon{color:inherit !important}
/* Inputs use an inset wash. Validation rings are intentionally preserved. */
.fi-input-wrp:not(.fi-invalid){background:var(--ag-control) !important;border:1px solid var(--ag-line) !important;border-radius:var(--ag-round) !important;box-shadow:inset 0 1px 4px #00000008 !important;transition:border-color .16s ease,background-color .16s ease}
.fi-input-wrp:not(.fi-invalid):focus-within{border-color:color-mix(in srgb,var(--ag-accent) 60%,transparent) !important;background:color-mix(in srgb,var(--ag-accent) 5%,var(--ag-control)) !important}
:is(.fi-input,.fi-select-input,.fi-textarea){color:var(--ag-text) !important;background:transparent !important}
:is(.fi-input,.fi-textarea)::placeholder{color:var(--ag-muted);opacity:.65}
.fi-select-input option{background:var(--ag-surface);color:var(--ag-text)}
/* Action buttons float separately. No shared border or joined dividers. */
.fi-btn-group{display:flex !important;flex-wrap:wrap;gap:8px !important;background:transparent !important;border:0 !important;box-shadow:none !important;padding:0 !important;max-width:100%}
.fi-btn,.fi-btn-group > .fi-btn{border:0 !important;box-shadow:none !important;margin:0 !important;border-radius:var(--ag-round) !important;background:var(--ag-control) !important;color:var(--ag-text) !important;font-weight:500;transition:background-color .16s ease,color .16s ease}
.fi-btn.fi-color-primary{background:color-mix(in srgb,var(--ag-accent) 13%,transparent) !important;color:var(--ag-accent) !important}
.fi-btn.fi-color-danger{background:color-mix(in srgb,var(--ag-danger) 12%,transparent) !important;color:var(--ag-danger) !important}
.fi-btn.fi-color-success{background:color-mix(in srgb,var(--ag-success) 12%,transparent) !important;color:var(--ag-success) !important}
.fi-btn.fi-color-warning{background:color-mix(in srgb,var(--ag-warning) 12%,transparent) !important;color:var(--ag-warning) !important}
.fi-btn:not(:disabled):not([aria-disabled="true"]):not(.fi-disabled):hover{background:color-mix(in srgb,var(--ag-text) 8%,transparent) !important}
.fi-btn.fi-color-primary:not(:disabled):not([aria-disabled="true"]):hover{background:color-mix(in srgb,var(--ag-accent) 21%,transparent) !important}
.fi-btn.fi-color-danger:not(:disabled):not([aria-disabled="true"]):hover{background:color-mix(in srgb,var(--ag-danger) 21%,transparent) !important}
.fi-btn:is(:disabled,[aria-disabled="true"],.fi-disabled){opacity:.4 !important}
.fi-header-actions .fi-btn{min-height:36px;padding:8px 14px !important;font-size:13px !important}
.fi-btn > .fi-icon{color:inherit !important}
/* Toggle options need neutral idle values and a single clear selection. */
.fi-fo-toggle-buttons .fi-btn{background:color-mix(in srgb,var(--ag-text) 3%,transparent) !important;color:var(--ag-muted) !important;box-shadow:none !important;border-radius:var(--ag-round) !important}
.fi-fo-toggle-buttons .fi-btn:hover{background:color-mix(in srgb,var(--ag-text) 7%,transparent) !important;color:var(--ag-text) !important}
.fi-fo-toggle-buttons input:checked + .fi-btn,.fi-fo-toggle-buttons .fi-btn[aria-pressed="true"]{background:color-mix(in srgb,var(--ag-accent) 17%,transparent) !important;color:var(--ag-accent) !important;box-shadow:inset 0 1px 0 color-mix(in srgb,var(--ag-accent) 20%,transparent) !important}
.fi-fo-toggle-buttons input:focus-visible + .fi-btn{outline:2px solid var(--ag-accent) !important;outline-offset:3px}
/* Native switches preserve their enabled/disabled symbols and semantics. */
.fi-fo-toggle[aria-checked="true"]{background:var(--ag-accent) !important}
/* Menus and dialogs need a denser surface for text legibility above the page. */
:is(.fi-dropdown-panel,.fi-modal-window,.fi-fo-select-options,.fi-global-search-results-ctn){background:color-mix(in srgb,var(--ag-surface) 94%,transparent) !important;color:var(--ag-text);border:1px solid var(--ag-line) !important;border-radius:var(--ag-radius) !important;box-shadow:0 24px 80px #00000040 !important;backdrop-filter:blur(var(--ag-blur))}
.fi-dropdown-list-item:hover{background:var(--ag-hover) !important}
:is(.fi-ta-header,.fi-ta-content,.fi-ta-row,.fi-ta-header-ctn,.fi-ta-selection-indicator){background:transparent !important;border-color:var(--ag-line) !important}
.fi-ta-row:hover{background:color-mix(in srgb,var(--ag-accent) 4%,transparent) !important}
.fi-ta-row:has(input:checked){background:var(--ag-hover) !important}
.fi-ta-cell,.fi-ta-header-cell {border-color:var(--ag-line) !important}
/* Keep each server info card, never the outer stats frame. */
.fi-wi-stats-overview .fi-section:has(.fi-small-stat-block),.fi-wi-stats-overview .fi-section:has(.fi-small-stat-block) > .fi-section-content-ctn{background:transparent !important;border:0 !important;box-shadow:none !important;backdrop-filter:none !important}
.fi-wi-stats-overview .fi-section:has(.fi-small-stat-block) > .fi-section-content-ctn > .fi-section-content{padding:0 !important}
.fi-small-stat-block{background:var(--ag-fill) !important;color:var(--ag-text) !important;border:0 !important;box-shadow:inset 0 1px 0 var(--ag-line) !important;border-radius:var(--ag-round) !important}
.fi-small-stat-block span span:first-child{color:var(--ag-muted) !important}
:is(.fi-btn,.fi-tabs-item,.fi-icon-btn,.fi-sidebar-item-btn):focus-visible{outline:2px solid var(--ag-accent) !important;outline-offset:3px}
@supports not (backdrop-filter:blur(1px)){:is(.fi-sc-tabs.fi-contained,.fi-section:not(.fi-section-not-contained),.fi-sidebar,.fi-topbar,.fi-modal-window,.fi-dropdown-panel,.fi-ta-ctn,.glass){background:var(--ag-surface) !important}}
@media(prefers-reduced-transparency:reduce){:root{--ag-fill:var(--ag-surface)}:is(.fi-sc-tabs.fi-contained,.fi-section:not(.fi-section-not-contained),.fi-sidebar,.fi-topbar,.fi-modal-window,.fi-dropdown-panel,.glass){background:var(--ag-surface) !important;backdrop-filter:none !important}}
@media(prefers-reduced-motion:reduce){:is(.fi-btn,.fi-tabs-item,.fi-sidebar-item-btn,.fi-input-wrp){transition:none !important}}
@media(max-width:700px){.fi-topbar{margin:4px}.fi-sidebar{margin:0}.fi-sc-tabs.fi-contained > .fi-tabs{padding:8px !important}.fi-btn-group{gap:6px !important}}

/* Overlay menus must never reveal the navigation underneath. */
.fi-dropdown-panel {
 background:var(--ag-surface) !important;isolation:isolate;z-index:60;
 backdrop-filter:none !important;-webkit-backdrop-filter:none !important;
}
.fi-tenant-menu .fi-dropdown-panel {
 max-height:min(65dvh,36rem) !important;overflow-y:auto !important;
 overscroll-behavior:contain;scrollbar-width:thin;scrollbar-color:var(--ag-line) transparent;
}
.fi-dropdown-panel .fi-dropdown-list-item-label {color:var(--ag-text) !important}
.fi-tenant-menu .fi-dropdown-panel .fi-input[type="search"] {
 box-sizing:border-box;background:var(--ag-control) !important;
 border:1px solid var(--ag-line) !important;border-radius:var(--ag-round) !important;
 padding:10px 12px;margin-block:4px 10px;box-shadow:none !important;
}
.fi-tenant-menu .fi-dropdown-panel .fi-input[type="search"]:focus-visible {
 outline:2px solid var(--ag-accent) !important;outline-offset:-2px;
}

/* 0.3.1: Pelican server-entry has its own opaque utility background. */
.fi-ta [wire\:poll\.15s].relative.cursor-pointer > .dark\:bg-gray-800,
.fi-ta [wire\:poll\.15s].relative.cursor-pointer .end-0 > .dark\:bg-gray-800 {
 background:transparent !important;box-shadow:none !important;
}
.fi-ta-record:has([wire\:poll\.15s].relative.cursor-pointer) {
 background:var(--ag-fill) !important;border:0 !important;
 border-radius:var(--ag-radius) !important;
 box-shadow:inset 0 1px 0 var(--ag-line) !important;
 transition:background-color .16s ease;
}
.fi-ta-record:has([wire\:poll\.15s].relative.cursor-pointer):hover {
 background:color-mix(in srgb,var(--ag-accent) 5%,var(--ag-fill)) !important;
}
/* Resource-list tabs sit outside schema tabs and need their own surface. */
.fi-tabs:not(.fi-sc-tabs > .fi-tabs) {
 background:var(--ag-control) !important;border:0 !important;
 box-shadow:inset 0 1px 0 var(--ag-line) !important;border-radius:var(--ag-round) !important;
}
@media(prefers-reduced-motion:reduce){.fi-ta-record{transition:none !important}}

/* Follow Pelican native mode; separate light values preserve the dark palette. */
:root:not(.dark) {
 color-scheme:light;
 --ag-bg:var(--ag-light_bg);
 --ag-surface:var(--ag-light_surface);
 --ag-accent:var(--ag-light_accent);
 --ag-secondary:var(--ag-light_secondary);
 --ag-text:var(--ag-light_text);
 --ag-muted:var(--ag-light_muted);
 --ag-danger:var(--ag-light_danger);
 --ag-success:var(--ag-light_success);
 --ag-warning:var(--ag-light_warning);
}
:root.dark {color-scheme:dark}

AURORA_CSS;
            return new HtmlString('<style id="aurora-glass">' . $css . self::cssVariables(self::settings()) . '</style>');
        });
    }

    private const TABLE = 'aurora_glass_settings';
    private const DEFAULTS = [
        'light_bg' => '#eef1f7',
        'light_surface' => '#ffffff',
        'light_accent' => '#6251bd',
        'light_secondary' => '#4a8397',
        'light_text' => '#202637',
        'light_muted' => '#596477',
        'light_danger' => '#b42342',
        'light_success' => '#15734e',
        'light_warning' => '#925b06',
        'bg' => '#101216', 'surface' => '#20232c', 'accent' => '#a69afa',
        'danger' => '#fda4af', 'success' => '#6ee7b7', 'warning' => '#fcd34d',
        'secondary' => '#708cab', 'text' => '#eeeef5', 'muted' => '#aeb1c2',
        'opacity' => 0.72, 'blur' => 24, 'radius' => 22, 'sidebar' => 250,
        'font' => 14, 'glow' => 0.22, 'border' => 0.14,
    ];
    private const LABELS = [
        'light_bg' => 'Background',
        'light_surface' => 'Glass surfaces',
        'light_accent' => 'Accent',
        'light_secondary' => 'Secondary glow',
        'light_text' => 'Text',
        'light_muted' => 'Secondary text',
        'light_danger' => 'Danger',
        'light_success' => 'Success',
        'light_warning' => 'Warning',
        'danger' => 'Danger', 'success' => 'Success', 'warning' => 'Warning',
        'bg' => 'Background', 'surface' => 'Glass surfaces', 'accent' => 'Accent',
        'secondary' => 'Secondary glow', 'text' => 'Text', 'muted' => 'Secondary text',
        'opacity' => 'Glass opacity', 'blur' => 'Blur (px)', 'radius' => 'Corner radius (px)',
        'sidebar' => 'Sidebar width (px)', 'font' => 'Base text size (px)',
        'glow' => 'Ambient glow', 'border' => 'Glass highlight',
    ];
    private const RANGES = [
        'opacity' => [0.35, 1], 'blur' => [0, 48], 'radius' => [4, 32],
        'sidebar' => [200, 320], 'font' => [12, 18], 'glow' => [0, 0.5], 'border' => [0.05, 0.35],
    ];

    private static function rules(): array
    {
        $rules = [];
        foreach (self::DEFAULTS as $key => $value) {
            $rules[$key] = isset(self::RANGES[$key])
                ? ['required', 'numeric', 'min:' . self::RANGES[$key][0], 'max:' . self::RANGES[$key][1]]
                : ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'];
        }
        return $rules;
    }

    private static function settings(): array
    {
        if (!Schema::hasTable(self::TABLE)) {
            return self::DEFAULTS;
        }
        $raw = DB::table(self::TABLE)->where('id', 1)->value('settings');
        $values = is_string($raw) ? json_decode($raw, true) : [];
        $values = is_array($values) ? array_replace(self::DEFAULTS, array_intersect_key($values, self::DEFAULTS)) : self::DEFAULTS;
        return Validator::make($values, self::rules())->fails() ? self::DEFAULTS : $values;
    }

    private static function cssVariables(array $values): string
    {
        $css = ':root {';
        foreach (self::DEFAULTS as $key => $default) {
            $unit = in_array($key, ['blur', 'radius', 'sidebar', 'font'], true) ? 'px' : '';
            $value = isset(self::RANGES[$key]) ? (float) $values[$key] : $values[$key];
            $css .= '--ag-' . $key . ':' . $value . $unit . ';';
        }
        // Native Filament primary colors follow the chosen accent too.
        foreach ([50 => 94, 100 => 88, 200 => 72, 300 => 52, 400 => 26, 500 => 0] as $shade => $white) {
            $css .= '--primary-' . $shade . ':color-mix(in srgb,var(--ag-accent),white ' . $white . '%);';
        }
        foreach ([600 => 14, 700 => 28, 800 => 42, 900 => 58, 950 => 72] as $shade => $black) {
            $css .= '--primary-' . $shade . ':color-mix(in srgb,var(--ag-accent),black ' . $black . '%);';
        }
        return $css . '}';
    }

    public function getSettingsFormData(): array
    {
        return self::settings();
    }

    public function getSettingsForm(): array
    {
        $palette = [];
        $lightPalette = [];
        $appearance = [];
        foreach (self::DEFAULTS as $key => $default) {
            if (isset(self::RANGES[$key])) {
                $appearance[] = TextInput::make($key)->label(self::LABELS[$key])->numeric()
                    ->minValue(self::RANGES[$key][0])->maxValue(self::RANGES[$key][1])
                    ->step(in_array($key, ['opacity', 'glow', 'border'], true) ? 0.01 : 1)
                    ->required()->default($default);
            } else {
                $field = ColorPicker::make($key)->label(self::LABELS[$key])
                    ->rules(['regex:/^#[0-9a-fA-F]{6}$/'])->required()->default($default);
                if (str_starts_with($key, 'light_')) {
                    $lightPalette[] = $field;
                } else {
                    $palette[] = $field;
                }
            }
        }
        return [
            Select::make('preset')->label('Starting palette')->options([
                'custom' => 'Custom', 'violet' => 'Aurora Violet', 'ocean' => 'Ocean',
                'graphite' => 'Graphite', 'rose' => 'Rose',
            ])->default('custom')->live()->afterStateUpdated(function ($state, $set): void {
                $presets = [
                    'violet' => [],
                    'ocean' => ['accent' => '#79d5dc', 'secondary' => '#4a84bd', 'light_accent' => '#116a7c', 'light_secondary' => '#397ca8'],
                    'graphite' => ['accent' => '#b6becd', 'secondary' => '#708090', 'glow' => 0.1, 'light_accent' => '#4e5c71'],
                    'rose' => ['accent' => '#e3a4c1', 'secondary' => '#9b78bc', 'light_accent' => '#a13970', 'light_secondary' => '#8464ac'],
                ];
                if (!array_key_exists($state ?? '', $presets)) { return; }
                foreach (array_replace(self::DEFAULTS, $presets[$state]) as $key => $value) {
                    $set($key, $value);
                }
            })->helperText('Choose a starting point, then fine-tune your palette. Save and reload to apply globally.'),
            Section::make('Dark palette')->description('Used when Pelican dark mode is active.')
                ->schema($palette)->columns(3),
            Section::make('Light palette')->description('Used automatically when Pelican light mode is active.')
                ->schema($lightPalette)->columns(3),
            Section::make('Glass & layout')->description('Tune translucency, highlights and proportions.')
                ->schema($appearance)->columns(3),
        ];
    }

    public function saveSettings(array $data): void
    {
        // Called by Pelican's admin-only plugin settings page, using its authorization.
        $validated = Validator::make($data, self::rules())->validate();
        if (!Schema::hasTable(self::TABLE)) {
            Notification::make()->title('Install the plugin update to initialize appearance settings')->danger()->send();
            return;
        }
        DB::table(self::TABLE)->updateOrInsert(['id' => 1], [
            'settings' => json_encode($validated, JSON_THROW_ON_ERROR),
        ]);
        Notification::make()->title('Appearance saved. Reload the page to apply your changes.')->success()->send();
    }

    public function boot(Panel $panel): void {}
}
