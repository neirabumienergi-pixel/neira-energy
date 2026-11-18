@extends('layouts.site', ['seo' => ['title' => 'Portfolio Proyek', 'description' => 'Proyek PLTS dan energi terbarukan yang telah dibangun Neira Energy di berbagai kota di Indonesia.']])

@section('content')
    <x-page-hero title="Portfolio Proyek" subtitle="Membangun ekosistem energi bersih di lebih dari 30 kota di Indonesia." eyebrow="Portfolio" :crumbs="['Portfolio' => null]"/>
    <section class="section">
        <div class="container-x">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('portfolio.index') }}" class="btn {{ !request('tipe') && !request('segmen') ? 'bg-brand-600 text-white' : 'border border-slate-200 bg-white' }}">Semua</a>
                @foreach($types as $t)
                    <a href="{{ route('portfolio.index', ['tipe' => $t]) }}" class="btn {{ request('tipe') === $t ? 'bg-brand-600 text-white' : 'border border-slate-200 bg-white' }}">{{ $t }}</a>
                @endforeach
                <span class="mx-2 hidden w-px bg-slate-200 sm:block"></span>
                @foreach($segments as $s)
                    <a href="{{ route('portfolio.index', ['segmen' => $s->slug]) }}" class="btn {{ request('segmen') === $s->slug ? 'bg-accent-500 text-brand-950' : 'border border-slate-200 bg-white' }}">{{ $s->name }}</a>
                @endforeach
            </div>
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($portfolios as $p)<x-portfolio-card :portfolio="$p"/>@empty<p class="text-slate-500">Belum ada proyek untuk filter ini.</p>@endforelse
            </div>
            <div class="mt-10">{{ $portfolios->links() }}</div>
        </div>
    </section>

    @if($mapPoints->isNotEmpty())
        @push('head')<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">@endpush
        <section class="pb-16">
            <div class="container-x">
                <x-section-heading title="Sebaran proyek" center/>
                <div id="map" class="mt-8 h-[460px] overflow-hidden rounded-3xl border border-slate-200"></div>
            </div>
        </section>
        @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
                const map = L.map('map', { scrollWheelZoom: false }).setView([-2.5, 117], 5);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);
                @json($mapPoints).forEach(p => L.circleMarker([p.lat, p.lng], { radius: 8, color: '#0e9447', fillColor: '#f7941d', fillOpacity: .9 })
                    .addTo(map).bindPopup(`<a href="/portfolio/${p.slug}"><b>${p.title}</b></a>`));
            </script>
        @endpush
    @endif
@endsection
