<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanySetting extends Model
{
    public const DEFAULT_COLOR = '#db2777';

    protected $fillable = [
        'trade_name',
        'legal_name',
        'nit',
        'slogan',
        'address',
        'city',
        'phone',
        'email',
        'website',
        'logo_path',
        'brand_color',
    ];

    protected static ?self $current = null;

    protected static function booted(): void
    {
        static::saved(fn () => static::$current = null);
    }

    public static function defaults(): array
    {
        return [
            'trade_name' => 'Sistema de Gestión Empresarial',
            'brand_color' => self::DEFAULT_COLOR,
        ];
    }

    public static function current(): self
    {
        return static::$current ??= static::loadOrCreate();
    }

    private static function loadOrCreate(): self
    {
        try {
            return static::query()->first() ?? static::create(static::defaults());
        } catch (\Throwable) {
            return new static(static::defaults());
        }
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? asset('storage/'.$this->logo_path) : null;
    }

    public function faviconUrl(): string
    {
        $logo = $this->logoUrl();

        if (! $logo) {
            return asset('favicon.svg');
        }

        return $logo.'?v='.($this->updated_at?->timestamp ?? 0);
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/u', trim((string) $this->trade_name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $significant = array_values(array_filter($words, fn ($word) => mb_strlen($word) > 2));
        $words = $significant ?: $words;

        if (count($words) >= 2) {
            return mb_strtoupper(implode('', array_map(
                fn ($word) => mb_substr($word, 0, 1),
                array_slice($words, 0, 3)
            )));
        }

        return mb_strtoupper(mb_substr($words[0] ?? 'SGE', 0, 2));
    }

    public function palette(): array
    {
        $white = [255, 255, 255];
        $black = [0, 0, 0];
        $base = $this->guardedBase();

        $steps = [
            50 => [$white, 0.95],
            100 => [$white, 0.90],
            200 => [$white, 0.78],
            300 => [$white, 0.58],
            400 => [$white, 0.35],
            500 => [$white, 0.15],
            600 => null,
            700 => [$black, 0.14],
            800 => [$black, 0.28],
            900 => [$black, 0.42],
        ];

        $palette = [];
        foreach ($steps as $shade => $step) {
            $rgb = $step ? $this->mix($base, $step[0], $step[1]) : $base;
            $palette[$shade] = implode(' ', $rgb);
        }

        return $palette;
    }

    public function accentHex(): string
    {
        return sprintf('#%02x%02x%02x', ...$this->guardedBase());
    }

    public function logoDataUri(): ?string
    {
        $disk = Storage::disk('public');

        if (! $this->logo_path || ! $disk->exists($this->logo_path)) {
            return null;
        }

        $mime = $disk->mimeType($this->logo_path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode($disk->get($this->logo_path));
    }

    private function guardedBase(): array
    {
        $base = $this->rgb();

        for ($i = 0; $i < 20 && $this->contrastWithWhite($base) < 4.5; $i++) {
            $base = $this->mix($base, [0, 0, 0], 0.06);
        }

        return $base;
    }

    private function rgb(): array
    {
        $hex = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $this->brand_color)
            ? $this->brand_color
            : self::DEFAULT_COLOR;

        return [
            (int) hexdec(substr($hex, 1, 2)),
            (int) hexdec(substr($hex, 3, 2)),
            (int) hexdec(substr($hex, 5, 2)),
        ];
    }

    private function mix(array $color, array $with, float $amount): array
    {
        return array_map(
            fn ($from, $to) => (int) round($from + ($to - $from) * $amount),
            $color,
            $with
        );
    }

    private function contrastWithWhite(array $color): float
    {
        $linear = array_map(function ($value) {
            $value /= 255;

            return $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }, $color);

        $luminance = 0.2126 * $linear[0] + 0.7152 * $linear[1] + 0.0722 * $linear[2];

        return 1.05 / ($luminance + 0.05);
    }
}
