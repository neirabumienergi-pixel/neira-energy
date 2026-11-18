@props(['post'])
<a href="{{ route('blog.show', $post) }}" class="card group flex flex-col">
    <div class="aspect-[16/9] overflow-hidden bg-brand-50">
        @if($post->cover)<img src="{{ media($post->cover) }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">@endif
    </div>
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            @if($post->category)<span class="chip">{{ $post->category->name }}</span>@endif
            <span>{{ $post->published_at?->translatedFormat('d M Y') }}</span>
        </div>
        <h3 class="mt-3 line-clamp-2 text-lg font-semibold text-slate-900 group-hover:text-brand-600">{{ $post->title }}</h3>
        <p class="mt-2 line-clamp-3 text-sm text-slate-500">{{ $post->excerpt }}</p>
    </div>
</a>
