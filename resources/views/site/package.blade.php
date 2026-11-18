@extends('layouts.site', ['seo' => ['title' => $package->seo['meta_title'] ?? $package->name, 'description' => $package->seo['meta_description'] ?? $package->short_description, 'image' => $package->seo['og_image'] ?? $package->image]])

@section('content')
    @php($images = array_values(array_filter(array_merge([$package->image], $package->gallery ?? []))))
    @php($waMsg = "Halo Neira Energy, saya tertarik dengan paket {$package->name} ({$package->capacity_label}). Mohon info penawarannya.")
    <div class="border-b border-slate-100 bg-slate-50">
        <nav class="container-x flex flex-wrap items-center gap-1.5 py-3 text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-brand-600">Beranda</a><x-heroicon-m-chevron-right class="h-3.5 w-3.5"/>
            <a href="{{ route('products.index') }}" class="hover:text-brand-600">Produk</a><x-heroicon-m-chevron-right class="h-3.5 w-3.5"/>
            @if($category->parent)<a href="{{ $category->parent->url() }}" class="hover:text-brand-600">{{ $category->parent->name }}</a><x-heroicon-m-chevron-right class="h-3.5 w-3.5"/>@endif
            <a href="{{ $category->url() }}" class="hover:text-brand-600">{{ $category->name }}</a><x-heroicon-m-chevron-right class="h-3.5 w-3.5"/>
            <span class="text-slate-900">{{ $package->name }}</span>
        </nav>
    </div>

    <section class="py-10 sm:py-14">
        <div class="container-x grid gap-10 lg:grid-cols-2">
            <div x-data="{ img: 0 }">
                <div class="aspect-[4/3] overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-accent-50">
                    @forelse($images as $k => $img)
                        <img x-show="img === {{ $k }}" @if($k) x-cloak @endif src="{{ media($img) }}" alt="{{ $package->name }}" class="h-full w-full object-cover">
                    @empty
                        <div class="grid h-full place-items-center"><x-icon :name="$category->icon" fallback="heroicon-o-sun" class="h-24 w-24 text-brand-300"/></div>
                    @endforelse
                </div>
                @if(count($images) > 1)
                    <div class="mt-3 grid grid-cols-5 gap-2">
                        @foreach($images as $k => $img)
                            <button @click="img = {{ $k }}" :class="img === {{ $k }} ? 'ring-2 ring-brand-500' : 'opacity-70'" class="aspect-square overflow-hidden rounded-xl"><img src="{{ media($img) }}" class="h-full w-full object-cover" alt=""></button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="chip">{{ $category->full_name }}</span>
                    @if($package->sku)<span class="text-xs text-slate-500">{{ $package->sku }}</span>@endif
                </div>
                <h1 class="mt-3 text-3xl font-bold sm:text-4xl">{{ $package->name }}</h1>
                <p class="mt-2 text-xl font-semibold text-brand-600">{{ $package->capacity_label }}{{ $package->phase ? ' · '.$package->phase : '' }}</p>
                @if($package->short_description)<p class="mt-4 text-slate-600">{{ $package->short_description }}</p>@endif

                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach(array_filter([
                        'Kapasitas' => $package->capacity_label,
                        'Produksi' => $package->est_production_kwh_month ? '± '.number_format($package->est_production_kwh_month, 0, ',', '.').' kWh/bln' : null,
                        'Baterai' => $package->battery_kwh ? rtrim(rtrim(number_format($package->battery_kwh, 2, ',', '.'), '0'), ',').' kWh' : null,
                        'Area' => $package->area_required_m2 ? '± '.number_format($package->area_required_m2, 0, ',', '.').' m²' : null,
                    ]) as $k => $v)
                        <div class="rounded-xl border border-slate-200 p-3"><p class="text-xs text-slate-500">{{ $k }}</p><p class="mt-0.5 text-sm font-semibold text-slate-900">{{ $v }}</p></div>
                    @endforeach
                </div>

                <div class="mt-6 rounded-2xl border border-brand-100 bg-brand-50/60 p-5">
                    @if($package->base_price)
                        @if($promo)
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-accent-500 px-2.5 py-0.5 text-xs font-bold text-brand-950">{{ $promo->label }}</span>
                                <a href="{{ route('promos.show', $promo) }}" class="text-xs font-medium text-accent-700 hover:underline">{{ $promo->title }}</a>
                            </div>
                            <p class="mt-2 text-sm text-slate-500 line-through">{{ rupiah($package->base_price) }}</p>
                        @elseif($package->strike_price)
                            <p class="text-sm text-slate-500 line-through">{{ rupiah($package->strike_price) }}</p>
                        @endif
                        <p class="text-3xl font-extrabold text-slate-900">{{ rupiah($finalPrice) }}</p>
                        @if($package->price_note)<p class="mt-1 text-xs text-slate-500">{{ $package->price_note }}</p>@endif
                    @else
                        <p class="text-lg font-semibold text-slate-900">Harga sesuai survei lokasi</p>
                    @endif
                    @if($package->warranty)<p class="mt-3 flex items-center gap-2 text-sm text-slate-600"><x-heroicon-o-shield-check class="h-5 w-5 text-brand-500"/>Garansi: {{ $package->warranty }}</p>@endif
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ wa_link($waMsg) }}" target="_blank" rel="noopener" class="btn-primary">Minta Penawaran</a>
                        @if($package->datasheet)<a href="{{ media($package->datasheet) }}" target="_blank" class="btn-outline"><x-heroicon-o-document-arrow-down class="h-4 w-4"/> Datasheet</a>@endif
                    </div>
                </div>

                @if($package->scope_of_work)
                    <div class="mt-6">
                        <p class="text-sm font-semibold text-slate-900">Sudah termasuk:</p>
                        <ul class="mt-2 grid gap-1.5 text-sm sm:grid-cols-2">
                            @foreach($package->scope_of_work as $s)<li class="flex items-center gap-2"><x-heroicon-s-check-circle class="h-4 w-4 text-brand-500"/>{{ $s }}</li>@endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section x-data="{ tab: '{{ $package->specifications ? 'spec' : ($package->components ? 'bom' : 'desc') }}' }" class="border-t border-slate-100 bg-slate-50 py-12">
        <div class="container-x">
            <div class="flex gap-2 overflow-x-auto">
                @if($package->specifications)<button @click="tab='spec'" :class="tab==='spec' ? 'bg-brand-600 text-white' : 'bg-white text-slate-600'" class="btn shrink-0">Data Teknis</button>@endif
                @if($package->components)<button @click="tab='bom'" :class="tab==='bom' ? 'bg-brand-600 text-white' : 'bg-white text-slate-600'" class="btn shrink-0">Komponen</button>@endif
                @if($package->description)<button @click="tab='desc'" :class="tab==='desc' ? 'bg-brand-600 text-white' : 'bg-white text-slate-600'" class="btn shrink-0">Deskripsi</button>@endif
            </div>
            @if($package->specifications)
                <div x-show="tab==='spec'" class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <table class="w-full text-sm">
                        @foreach(collect($package->specifications)->groupBy(fn ($s) => $s['group'] ?? '') as $group => $rows)
                            @if($group)<tr><th colspan="2" class="bg-brand-50 px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-brand-700">{{ $group }}</th></tr>@endif
                            @foreach($rows as $r)
                                <tr class="border-t border-slate-100"><td class="w-1/3 px-5 py-3 text-slate-500">{{ $r['label'] }}</td><td class="px-5 py-3 font-medium text-slate-900">{{ $r['value'] }}</td></tr>
                            @endforeach
                        @endforeach
                    </table>
                </div>
            @endif
            @if($package->components)
                <div x-show="tab==='bom'" x-cloak class="mt-6 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3">Komponen</th><th class="px-5 py-3">Merek / Tipe</th><th class="px-5 py-3 text-right">Qty</th></tr></thead>
                        @foreach($package->components as $c)
                            <tr class="border-t border-slate-100"><td class="px-5 py-3 font-medium text-slate-900">{{ $c['name'] }}</td><td class="px-5 py-3">{{ $c['brand'] ?? '' }}</td><td class="px-5 py-3 text-right">{{ $c['qty'] ?? '' }} {{ $c['unit'] ?? '' }}</td></tr>
                        @endforeach
                    </table>
                </div>
            @endif
            @if($package->description)
                <div x-show="tab==='desc'" x-cloak class="prose prose-slate mt-6 max-w-none rounded-2xl bg-white p-6">{!! $package->description !!}</div>
            @endif
        </div>
    </section>

    @if($siblings->isNotEmpty())
        <section class="section">
            <div class="container-x">
                <x-section-heading :title="'Paket '.$category->name.' lainnya'"/>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($siblings as $p)<x-package-card :package="$p"/>@endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
