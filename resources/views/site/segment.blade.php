@extends('layouts.site', ['seo' => ['title' => $segment->seo['meta_title'] ?? 'Solusi '.$segment->name, 'description' => $segment->seo['meta_description'] ?? $segment->short_description, 'image' => $segment->image]])

@section('content')
    <x-page-hero :title="$segment->name" :subtitle="$segment->short_description" :eyebrow="$segment->tagline ?: 'Solusi'" :image="media($segment->image)" :crumbs="['Solusi' => null, $segment->name => null]">
        @if($segment->examples)
            <div class="mt-6 flex flex-wrap gap-2">@foreach($segment->examples as $e)<span class="rounded-full bg-white/10 px-3 py-1 text-xs text-white">{{ $e }}</span>@endforeach</div>
        @endif
        <a href="{{ wa_link('Halo Neira Energy, saya dari segmen '.$segment->name.' dan ingin konsultasi.') }}" target="_blank" rel="noopener" class="btn-accent mt-8">Konsultasi kebutuhan Anda</a>
    </x-page-hero>

    @if($segment->content)
        <section class="section"><div class="container-x prose prose-slate max-w-3xl prose-strong:text-slate-900">{!! $segment->content !!}</div></section>
    @endif

    <section class="section bg-slate-50">
        <div class="container-x">
            <x-section-heading title="Produk yang relevan" center/>
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-{{ min(5, max(3, $categories->count())) }}">
                @foreach($categories as $cat)
                    <a href="{{ $cat->url() }}" class="card group p-6">
                        <x-icon :name="$cat->icon" class="h-8 w-8 text-brand-500"/>
                        <h3 class="mt-3 font-semibold group-hover:text-brand-600">{{ $cat->name }}</h3>
                        <p class="mt-1 line-clamp-3 text-sm text-slate-500">{{ $cat->short_description }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if($portfolios->isNotEmpty())
        <section class="section">
            <div class="container-x">
                <x-section-heading eyebrow="Portfolio" :title="'Proyek segmen '.$segment->name"/>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">@foreach($portfolios as $p)<x-portfolio-card :portfolio="$p"/>@endforeach</div>
            </div>
        </section>
    @endif
@endsection
