@extends('layouts.site', ['seo' => ['title' => 'Produk & Layanan', 'description' => 'PLTS On Grid, Off Grid, Hybrid, PJU Tenaga Surya, solusi surya terapan, fabrikasi custom, dan retail Energy Mart.']])

@section('content')
    <x-page-hero title="Produk & Layanan" subtitle="Solusi energi surya lengkap untuk rumah, bisnis, industri, pemerintah, dan komunitas." eyebrow="Neira Energy" :crumbs="['Produk' => null]"/>
    <section class="section">
        <div class="container-x space-y-14">
            @foreach($categories as $cat)
                <div class="grid items-center gap-8 lg:grid-cols-5">
                    <a href="{{ $cat->url() }}" class="group block overflow-hidden rounded-3xl lg:col-span-2 {{ $loop->odd ? '' : 'lg:order-2' }}">
                        <img src="{{ media($cat->image, 'images/logo.png') }}" alt="{{ $cat->name }}" loading="lazy" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105">
                    </a>
                    <div class="lg:col-span-3">
                        <div class="flex items-center gap-3">
                            <div class="grid h-11 w-11 place-items-center rounded-xl bg-brand-50 text-brand-600"><x-icon :name="$cat->icon" class="h-6 w-6"/></div>
                            @if($cat->tagline)<p class="eyebrow">{{ $cat->tagline }}</p>@endif
                        </div>
                        <h2 class="mt-3 text-2xl font-bold sm:text-3xl">{{ $cat->name }}</h2>
                        <p class="mt-3 text-slate-600">{{ $cat->short_description }}</p>
                        @if($cat->children->isNotEmpty())
                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                @foreach($cat->children as $child)
                                    <a href="{{ $child->url() }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium hover:border-brand-500 hover:text-brand-600">
                                        {{ $child->name }} <x-heroicon-m-arrow-right class="h-4 w-4"/>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ $cat->url() }}" @if($cat->external_url) target="_blank" rel="noopener" @endif class="btn-primary">{{ $cat->external_url ? 'Kunjungi toko' : 'Lihat detail' }}</a>
                            @unless($cat->external_url)
                                <a href="{{ route('products.brochure', $cat) }}" target="_blank" class="btn-outline"><x-heroicon-o-document-arrow-down class="h-4 w-4"/> Brosur</a>
                            @endunless
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
