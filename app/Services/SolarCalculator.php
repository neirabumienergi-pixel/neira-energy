<?php

namespace App\Services;

use App\Models\ProductCategory;
use App\Models\ProductPackage;
use App\Models\Setting;
use Illuminate\Support\Collection;

/**
 * Kalkulator estimasi PLTS on grid.
 * Rumus yang sama dipakai di front-end (resources/js/hero.js) agar hasil hero & server identik.
 */
class SolarCalculator
{
    public const DEFAULTS = [
        'tariff_residential' => 1444.70,   // Rp/kWh R-1 > 1.300 VA
        'tariff_business' => 1444.70,      // Rp/kWh B-2 / I-3 (atur sesuai golongan)
        'psh' => 4.0,                      // peak sun hours (jam/hari)
        'performance_ratio' => 0.8,
        'offset_residential' => 0.6,       // porsi kWh yang ditargetkan dari PLTS (rumah: banyak dipakai malam)
        'offset_business' => 0.8,
        'price_per_kwp' => 12_000_000,     // fallback bila tidak ada paket yang cocok
        'emission_factor' => 0.87,         // kg CO2 / kWh (faktor emisi grid)
        'min_bill' => 500_000,
        'max_bill' => 100_000_000,
        'category_id' => null,             // kategori paket untuk rekomendasi (default: PLTS On Grid)
    ];

    public function __construct(public array $config = [])
    {
        $this->config = array_merge(self::DEFAULTS, array_filter(Setting::get('calculator', []) ?: [], fn ($v) => $v !== null && $v !== ''), $config);
        foreach (self::DEFAULTS as $k => $v) {
            if (is_float($v) || is_int($v)) {
                $this->config[$k] = (float) $this->config[$k];
            }
        }
    }

    public function tariff(string $profile): float
    {
        return $profile === 'residential' ? $this->config['tariff_residential'] : $this->config['tariff_business'];
    }

    /** @return array{kwh_month: float, kwp: float, production_month: float, saving_month: float, cost: float, payback_years: ?float, co2_ton_year: float, package: ?ProductPackage} */
    public function estimate(float $monthlyBill, string $profile = 'business'): array
    {
        $c = $this->config;
        $tariff = $this->tariff($profile);
        $kwhMonth = $monthlyBill / $tariff;
        $offset = $profile === 'residential' ? $c['offset_residential'] : $c['offset_business'];
        $yieldPerKwp = $c['psh'] * $c['performance_ratio'] * 30;
        $kwp = round(max(1, $kwhMonth * $offset / $yieldPerKwp), 1);

        $package = $this->recommend($monthlyBill, $kwp);
        if ($package) {
            $kwp = (float) $package->capacity;
        }
        $production = $kwp * $yieldPerKwp;
        // Tanpa kompensasi ekspor (Permen ESDM 2/2024): hemat hanya dari energi yang dipakai sendiri.
        $saving = min($production, $kwhMonth * $offset) * $tariff;
        $cost = $package?->finalPrice() ?? $kwp * $c['price_per_kwp'];

        return [
            'kwh_month' => round($kwhMonth),
            'kwp' => $kwp,
            'production_month' => round($production),
            'saving_month' => round($saving),
            'cost' => round($cost),
            'payback_years' => $saving > 0 ? round($cost / ($saving * 12), 1) : null,
            'co2_ton_year' => round($production * 12 * $c['emission_factor'] / 1000, 1),
            'package' => $package,
        ];
    }

    /**
     * 1) paket yang rentang tagihannya (diisi admin) cocok;
     * 2) paket dengan kapasitas terdekat, selama kebutuhan tidak melebihi 1,5x paket terbesar
     *    (di atas itu dianggap proyek custom → null).
     */
    public function recommend(float $monthlyBill, float $kwp): ?ProductPackage
    {
        $packages = $this->packages();
        $byBill = $packages->first(fn ($p) => $p->min_monthly_bill !== null && $p->max_monthly_bill !== null
            && $monthlyBill >= $p->min_monthly_bill && $monthlyBill <= $p->max_monthly_bill);
        if ($byBill) {
            return $byBill;
        }
        if ($packages->isEmpty() || $kwp > $packages->max('capacity') * 1.5) {
            return null;
        }

        return $packages->sortBy(fn ($p) => [abs($p->capacity - $kwp), -$p->capacity])->first();
    }

    public function category(): ?ProductCategory
    {
        return ($this->config['category_id'] ? ProductCategory::find($this->config['category_id']) : null)
            ?? ProductCategory::where('name', 'like', '%On Grid%')->where('has_packages', true)->first();
    }

    public function packages(): Collection
    {
        $cat = $this->category();
        if (! $cat) {
            return collect();
        }

        return ProductPackage::active()->with('category')->whereIn('product_category_id', $cat->descendantIds())
            ->whereNotNull('capacity')->orderBy('capacity')->get();
    }

    /** Data untuk Alpine (hero) */
    public function frontendPayload(): array
    {
        return [
            'config' => collect($this->config)->except(['category_id'])->all(),
            'packages' => $this->packages()->map(fn (ProductPackage $p) => [
                'name' => $p->name, 'capacity' => (float) $p->capacity, 'label' => $p->capacity_label,
                'min' => $p->min_monthly_bill, 'max' => $p->max_monthly_bill,
                'price' => $p->finalPrice(), 'url' => $p->url(), 'image' => media($p->image),
            ])->values()->all(),
        ];
    }
}
