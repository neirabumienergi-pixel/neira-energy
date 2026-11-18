@extends('layouts.site', ['seo' => ['title' => 'Galeri', 'description' => 'Dokumentasi proyek dan kegiatan Neira Energy.']])

@section('content')
    <x-page-hero title="Galeri" subtitle="Dokumentasi proyek, kegiatan, dan tim Neira Energy." eyebrow="Galeri" :crumbs="['Galeri' => null]"/>
    <section class="section">
        <div class="container-x grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($galleries as $g)
                @php($cover = $g->cover ?: collect($g->items)->firstWhere('image')['image'] ?? null)
                <a href="{{ route('gallery.show', $g) }}" class="card group">
                    <div class="aspect-[4/3] overflow-hidden bg-slate-100">@if($cover)<img src="{{ media($cover) }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" alt="">@endif</div>
                    <div class="p-5"><h3 class="font-semibold group-hover:text-brand-600">{{ $g->title }}</h3>
                        <p class="mt-1 text-xs text-slate-500">{{ count($g->items ?? []) }} item{{ $g->event_date ? ' · '.$g->event_date->translatedFormat('d M Y') : '' }}</p></div>
                </a>
            @empty
                <p class="text-slate-500">Belum ada album.</p>
            @endforelse
        </div>
    </section>
@endsection
