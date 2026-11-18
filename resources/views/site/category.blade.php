@extends('layouts.site', ['seo' => ['title' => $category->seo['meta_title'] ?? $category->name, 'description' => $category->seo['meta_description'] ?? $category->short_description, 'image' => $category->seo['og_image'] ?? $category->image]])

@section('content')
    @php($crumbs = $category->parent ? ['Produk' => route('products.index'), $category->parent->name => $category->parent->url(), $category->name => null] : ['Produk' => route('products.index'), $category->name => null])
    @php($blocks = $category->brochure ?? [])
    @php($hasHero = ($blocks[0]['type'] ?? null) === 'hero')
    @unless($hasHero)
        <x-page-hero :title="$category->name" :subtitle="$category->short_description" :eyebrow="$category->tagline" :image="media($category->image)" :crumbs="$crumbs">
            <div class="mt-8 flex flex-wrap gap-3">
                @if($packages->isNotEmpty())<a href="#paket" class="btn-accent">Lihat paket</a>@endif
                <a href="{{ route('products.brochure', $category) }}" target="_blank" class="btn-ghost-light"><x-heroicon-o-document-arrow-down class="h-4 w-4"/> Unduh brosur</a>
            </div>
        </x-page-hero>
    @endunless

    <x-blocks :blocks="$blocks" :context="['category' => $category, 'packages' => $packages]"/>

    {{-- Fallback jika brosur belum memuat blok otomatis --}}
    @php($types = collect($blocks)->pluck('type'))
    @if(!$types->contains('subcategories'))
        @include('blocks.subcategories', ['data' => [], 'index' => 1, 'context' => ['category' => $category]])
    @endif
    @if(!$types->contains('packages'))
        @include('blocks.packages', ['data' => [], 'index' => 0, 'context' => ['packages' => $packages]])
    @endif

    @if($portfolios->isNotEmpty())
        <section class="section bg-slate-50">
            <div class="container-x">
                <x-section-heading eyebrow="Portfolio" :title="'Proyek '.$category->name"/>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($portfolios as $p)<x-portfolio-card :portfolio="$p"/>@endforeach
                </div>
            </div>
        </section>
    @endif

    @if(!$types->contains('cta'))
        @include('blocks.cta', ['data' => ['title' => 'Butuh '.$category->name.'?', 'text' => 'Tim kami siap membantu survei, desain, dan penawaran terbaik.', 'button_label' => 'Konsultasi via WhatsApp'], 'index' => 0])
    @endif
@endsection
