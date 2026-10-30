<?php

declare(strict_types=1);

namespace AuroraGlass;

use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Validator;
use Filament\Panel;
use Illuminate\Support\HtmlString;
use Throwable;

final class AuroraGlassPlugin implements Plugin, HasPluginSettings
{
    use EnvironmentWriterTrait;

    public function getId(): string { return 'aurora-glass'; }

    public function register(Panel $panel): void
    {
        $panel->viteTheme('plugins/aurora-glass/resources/css/theme.css');
        $panel->renderHook('panels::head.end', static fn (): HtmlString => new HtmlString(
            '<style id="aurora-glass-variables">' . self::cssVariables(self::settings()) . '</style>'
        ));
    }

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
        $configured = config('aurora-glass', []);
        $configured = is_array($configured) ? array_intersect_key($configured, self::DEFAULTS) : [];
        $values = array_replace(self::DEFAULTS, $configured);

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
        $validated = Validator::make($data, self::rules())->validate();
        $environment = [];

        foreach ($validated as $key => $value) {
            $environment['AURORA_GLASS_' . strtoupper($key)] = $value;
        }

        try {
            $this->writeToEnvironment($environment);
        } catch (Throwable $exception) {
            report($exception);
            Notification::make()
                ->title('Could not save Aurora Glass settings')
                ->body('Check that the panel can write to its .env file.')
                ->danger()
                ->send();
            return;
        }

        config()->set('aurora-glass', $validated);
        Notification::make()->title('Appearance saved. Reload the page to apply your changes.')->success()->send();
    }

    public function boot(Panel $panel): void {}
}
