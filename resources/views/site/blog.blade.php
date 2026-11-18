@extends('layouts.site', ['seo' => ['title' => $activeCategory?->name ?? 'Artikel', 'description' => 'Artikel, berita, dan wawasan seputar energi surya dan energi terbarukan.']])

@section('content')
    <x-page-hero :title="$activeCategory?->name ?? 'Artikel & Berita'" subtitle="Wawasan seputar PLTS, regulasi, dan transisi energi Indonesia." eyebrow="Blog" :crumbs="['Artikel' => null]"/>
    <section class="section">
        <div class="container-x">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('blog.index') }}" class="btn {{ !$activeCategory ? 'bg-brand-600 text-white' : 'border border-slate-200' }}">Semua</a>
                    @foreach($categories as $c)<a href="{{ route('blog.category', $c) }}" class="btn {{ $activeCategory?->is($c) ? 'bg-brand-600 text-white' : 'border border-slate-200' }}">{{ $c->name }}</a>@endforeach
                </div>
                <form class="w-full sm:w-72"><input name="q" value="{{ request('q') }}" class="input" placeholder="Cari artikel..."></form>
            </div>
            <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse($posts as $post)<x-post-card :post="$post"/>@empty<p class="text-slate-500">Belum ada artikel.</p>@endforelse
            </div>
            <div class="mt-10">{{ $posts->links() }}</div>
        </div>
    </section>
@endsection
