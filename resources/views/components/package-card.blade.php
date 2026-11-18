@props(['package', 'showPrice' => true])
@php($promo = $showPrice ? $package->bestPromo() : null)
@php($final = $promo ? $package->finalPrice($promo) : $package->base_price)
<a href="{{ $package->url() }}" class="card group flex flex-col">
    <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-brand-50 to-accent-50">
        @if($package->image)
            <img src="{{ media($package->image) }}" alt="{{ $package->name }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="grid h-full place-items-center"><x-icon :name="$package->category?->icon" fallback="heroicon-o-sun" class="h-16 w-16 text-brand-300"/></div>
        @endif
        <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-sm font-bold text-brand-700 shadow">{{ $package->capacity_label }}</span>
        @if($promo)
            <span class="absolute right-3 top-3 rounded-full bg-accent-500 px-3 py-1 text-xs font-bold text-white shadow">PROMO {{ $promo->label }}</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-5">
        <p class="text-xs font-medium text-brand-600">{{ $package->category?->name }}</p>
        <h3 class="mt-1 text-lg font-semibold text-slate-900 group-hover:text-brand-600">{{ $package->name }}</h3>
        @if($package->short_description)<p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $package->short_description }}</p>@endif
        <dl class="mt-4 grid grid-cols-2 gap-2 text-xs">
            @if($package->est_production_kwh_month)
                <div class="rounded-lg bg-slate-50 p-2"><dt class="text-slate-400">Produksi</dt><dd class="font-semibold text-slate-800">± {{ number_format($package->est_production_kwh_month, 0, ',', '.') }} kWh/bln</dd></div>
            @endif
            @if($package->battery_kwh)
                <div class="rounded-lg bg-slate-50 p-2"><dt class="text-slate-400">Baterai</dt><dd class="font-semibold text-slate-800">{{ rtrim(rtrim(number_format($package->battery_kwh, 2, ',', '.'), '0'), ',') }} kWh</dd></div>
            @endif
            @if($package->area_required_m2)
                <div class="rounded-lg bg-slate-50 p-2"><dt class="text-slate-400">Area</dt><dd class="font-semibold text-slate-800">± {{ number_format($package->area_required_m2, 0, ',', '.') }} m²</dd></div>
            @endif
            @if($package->phase)
                <div class="rounded-lg bg-slate-50 p-2"><dt class="text-slate-400">Listrik</dt><dd class="font-semibold text-slate-800">{{ $package->phase }}</dd></div>
            @endif
        </dl>
        <div class="mt-auto pt-5">
            @if($showPrice && $package->base_price)
                @if($promo || $package->strike_price)
                    <p class="text-xs text-slate-400 line-through">{{ rupiah($promo ? $package->base_price : $package->strike_price) }}</p>
                @endif
                <p class="text-xl font-bold text-slate-900">{{ rupiah($final) }}</p>
                @if($package->price_note)<p class="text-[11px] text-slate-400">{{ $package->price_note }}</p>@endif
            @else
                <p class="text-sm font-semibold text-brand-600">Hubungi kami untuk penawaran</p>
            @endif
        </div>
    </div>
</a>
