@php($packages = $context['packages'] ?? collect())
@php($showPrice = ($data['show_price'] ?? '1') === '1')
@if($packages->isNotEmpty())
<section id="paket" class="section {{ $index % 2 ? 'bg-slate-50' : 'bg-white' }}">
    <div class="container-x">
        <x-section-heading :title="$data['heading'] ?? 'Pilihan Paket'" :text="$data['intro'] ?? null" center/>
        @if($context['print'] ?? false)
            <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-brand-600 text-white"><tr>
                        <th class="px-4 py-3 text-left">Paket</th><th class="px-4 py-3 text-left">Kapasitas</th><th class="px-4 py-3 text-left">Produksi / bln</th>
                        <th class="px-4 py-3 text-left">Baterai</th>@if($showPrice)<th class="px-4 py-3 text-right">Harga</th>@endif
                    </tr></thead>
                    @foreach($packages->groupBy(fn ($p) => $p->category->name) as $group => $rows)
                        @if($packages->pluck('product_category_id')->unique()->count() > 1)
                            <tr><td colspan="5" class="bg-brand-50 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-brand-700">{{ $group }}</td></tr>
                        @endif
                    @foreach($rows as $p)
                        @php($promo = $showPrice ? $p->bestPromo() : null)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-2.5 font-medium text-slate-900">{{ $p->name }}</td>
                            <td class="px-4 py-2.5">{{ $p->capacity_label }}</td>
                            <td class="px-4 py-2.5">{{ $p->est_production_kwh_month ? '± '.number_format($p->est_production_kwh_month, 0, ',', '.').' kWh' : '-' }}</td>
                            <td class="px-4 py-2.5">{{ $p->battery_kwh ? rtrim(rtrim(number_format($p->battery_kwh, 2, ',', '.'), '0'), ',').' kWh' : '-' }}</td>
                            @if($showPrice)<td class="px-4 py-2.5 text-right font-semibold">{{ $p->base_price ? rupiah($p->finalPrice($promo)) : 'Hubungi kami' }}</td>@endif
                        </tr>
                    @endforeach
                    @endforeach
                </table>
            </div>
        @else
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($packages as $p)<x-package-card :package="$p" :show-price="$showPrice"/>@endforeach
            </div>
        @endif
    </div>
</section>
@endif
