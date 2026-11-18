@extends('layouts.site', ['seo' => ['title' => $portfolio->seo['meta_title'] ?? $portfolio->title, 'description' => $portfolio->seo['meta_description'] ?? $portfolio->summary, 'image' => $portfolio->cover]])

@section('content')
    <x-page-hero :title="$portfolio->title" :subtitle="$portfolio->summary" :image="media($portfolio->cover)" eyebrow="Portfolio" :crumbs="['Portfolio' => route('portfolio.index'), $portfolio->title => null]"/>
    <section class="section">
        <div class="container-x grid gap-10 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <img src="{{ media($portfolio->cover) }}" alt="{{ $portfolio->title }}" class="w-full rounded-3xl object-cover">
                @if($portfolio->description)<div class="prose prose-slate mt-8 max-w-none">{!! $portfolio->description !!}</div>@endif
                @if($portfolio->gallery)
                    <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach($portfolio->gallery as $g)<a href="{{ media($g) }}" target="_blank"><img src="{{ media($g) }}" class="aspect-[4/3] w-full rounded-xl object-cover" loading="lazy" alt=""></a>@endforeach
                    </div>
                @endif
            </div>
            <aside class="h-fit rounded-3xl border border-slate-200 p-6 lg:sticky lg:top-28">
                <h2 class="text-lg font-semibold">Detail proyek</h2>
                <dl class="mt-4 divide-y divide-slate-100 text-sm">
                    @foreach(array_filter(['Klien' => $portfolio->client_name, 'Lokasi' => trim($portfolio->location.', '.$portfolio->province, ', '), 'Kapasitas' => $portfolio->capacity_label, 'Sistem' => $portfolio->system_type, 'Segmen' => $portfolio->segment?->name, 'Tahun' => $portfolio->year]) as $k => $v)
                        <div class="flex justify-between gap-4 py-2.5"><dt class="text-slate-500">{{ $k }}</dt><dd class="text-right font-medium text-slate-900">{{ $v }}</dd></div>
                    @endforeach
                </dl>
                <a href="{{ wa_link('Halo Neira Energy, saya melihat proyek '.$portfolio->title.' dan ingin membangun yang serupa.') }}" target="_blank" rel="noopener" class="btn-primary mt-6 w-full">Bangun proyek serupa</a>
            </aside>
        </div>
    </section>
    @if($related->isNotEmpty())
        <section class="section bg-slate-50"><div class="container-x"><x-section-heading title="Proyek lainnya"/>
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">@foreach($related as $p)<x-portfolio-card :portfolio="$p"/>@endforeach</div></div></section>
    @endif
@endsection
