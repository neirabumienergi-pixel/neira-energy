@extends('layouts.site', ['seo' => ['title' => $post->seo['meta_title'] ?? $post->title, 'description' => $post->seo['meta_description'] ?? $post->excerpt, 'image' => $post->seo['og_image'] ?? $post->cover]])

@section('content')
    <article>
        <header class="bg-slate-50 py-12">
            <div class="container-x max-w-3xl">
                <nav class="flex items-center gap-1.5 text-xs text-slate-500"><a href="{{ route('blog.index') }}" class="hover:text-brand-600">Artikel</a>
                    @if($post->category)<x-heroicon-m-chevron-right class="h-3.5 w-3.5"/><a href="{{ route('blog.category', $post->category) }}" class="hover:text-brand-600">{{ $post->category->name }}</a>@endif</nav>
                <h1 class="mt-4 text-3xl font-bold leading-tight sm:text-4xl">{{ $post->title }}</h1>
                <p class="mt-4 text-sm text-slate-500">{{ $post->author?->name ?? 'Tim Neira' }} · {{ $post->published_at->translatedFormat('d F Y') }} · {{ $post->readingMinutes() }} menit baca</p>
            </div>
        </header>
        <div class="container-x max-w-3xl py-10">
            @if($post->cover)<img src="{{ media($post->cover) }}" alt="{{ $post->title }}" class="mb-8 w-full rounded-3xl">@endif
            <div class="prose prose-slate prose-lg max-w-none prose-a:text-brand-600 prose-headings:text-slate-900 prose-img:rounded-2xl">{!! $post->content !!}</div>
            @if($post->tags)<div class="mt-8 flex flex-wrap gap-2">@foreach($post->tags as $t)<span class="chip">#{{ $t }}</span>@endforeach</div>@endif
            <div class="mt-8 flex items-center gap-3 border-t border-slate-200 pt-6 text-sm">
                <span class="font-medium">Bagikan:</span>
                <a class="text-brand-600 hover:underline" target="_blank" rel="noopener" href="https://wa.me/?text={{ rawurlencode($post->title.' '.url()->current()) }}">WhatsApp</a>
                <a class="text-brand-600 hover:underline" target="_blank" rel="noopener" href="https://www.linkedin.com/sharing/share-offsite/?url={{ rawurlencode(url()->current()) }}">LinkedIn</a>
                <a class="text-brand-600 hover:underline" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(url()->current()) }}">Facebook</a>
            </div>
        </div>
    </article>
    @if($related->isNotEmpty())
        <section class="section bg-slate-50"><div class="container-x"><x-section-heading title="Artikel terkait"/>
            <div class="mt-8 grid gap-6 md:grid-cols-3">@foreach($related as $p)<x-post-card :post="$p"/>@endforeach</div></div></section>
    @endif
@endsection
