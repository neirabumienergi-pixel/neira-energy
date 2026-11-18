@extends('layouts.site', ['seo' => ['title' => $gallery->title, 'description' => $gallery->description, 'image' => $gallery->cover]])

@section('content')
    <x-page-hero :title="$gallery->title" :subtitle="$gallery->description" eyebrow="Galeri" :crumbs="['Galeri' => route('gallery.index'), $gallery->title => null]"/>
    @php($items = $gallery->items ?? [])
    <section class="section" x-data="{ open: null, items: @js(collect($items)->map(fn ($i) => ['type' => $i['type'] ?? 'image', 'src' => ($i['type'] ?? 'image') === 'video' ? null : media($i['image'] ?? null), 'video' => $i['video_url'] ?? null, 'caption' => $i['caption'] ?? ''])) }">
        <div class="container-x columns-1 gap-4 sm:columns-2 lg:columns-3">
            @foreach($items as $k => $item)
                @if(($item['type'] ?? 'image') === 'video' && !empty($item['video_url']))
                    @php(preg_match('~(?:v=|youtu\.be/|embed/|shorts/)([\w-]{11})~', $item['video_url'], $m))
                    <div class="mb-4 break-inside-avoid overflow-hidden rounded-2xl bg-black">
                        <iframe class="aspect-video w-full" src="https://www.youtube.com/embed/{{ $m[1] ?? '' }}" allowfullscreen loading="lazy"></iframe>
                        @if(!empty($item['caption']))<p class="bg-white p-3 text-sm">{{ $item['caption'] }}</p>@endif
                    </div>
                @elseif(!empty($item['image']))
                    <button @click="open = {{ $k }}" class="mb-4 block w-full break-inside-avoid overflow-hidden rounded-2xl text-left">
                        <img src="{{ media($item['image']) }}" alt="{{ $item['caption'] ?? '' }}" loading="lazy" class="w-full transition hover:opacity-90">
                        @if(!empty($item['caption']))<p class="bg-slate-50 p-3 text-sm">{{ $item['caption'] }}</p>@endif
                    </button>
                @endif
            @endforeach
        </div>
        <div x-show="open !== null" x-cloak @keydown.escape.window="open = null" @click.self="open = null" class="fixed inset-0 z-[60] grid place-items-center bg-black/90 p-4">
            <template x-if="open !== null">
                <figure class="max-w-5xl"><img :src="items[open].src" class="max-h-[80vh] w-auto rounded-xl"><figcaption x-text="items[open].caption" class="mt-3 text-center text-sm text-white/80"></figcaption></figure>
            </template>
            <button @click="open = null" class="absolute right-5 top-5 text-white"><x-heroicon-o-x-mark class="h-8 w-8"/></button>
        </div>
    </section>
@endsection
