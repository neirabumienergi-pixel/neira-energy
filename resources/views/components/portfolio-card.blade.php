@props(['portfolio'])
<a href="{{ route('portfolio.show', $portfolio) }}" class="group relative block aspect-[4/3] overflow-hidden rounded-2xl bg-slate-200">
    @if($portfolio->cover)
        <img src="{{ media($portfolio->cover) }}" alt="{{ $portfolio->title }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
    <div class="absolute inset-x-0 bottom-0 p-5 text-white">
        <div class="flex flex-wrap gap-2">
            @if($portfolio->capacity_label)<span class="rounded-full bg-accent-500 px-2.5 py-0.5 text-xs font-semibold">{{ $portfolio->capacity_label }}</span>@endif
            @if($portfolio->system_type)<span class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs backdrop-blur">{{ $portfolio->system_type }}</span>@endif
        </div>
        <h3 class="mt-2 text-lg font-semibold leading-snug text-white">{{ $portfolio->title }}</h3>
        @if($portfolio->location)<p class="mt-0.5 flex items-center gap-1 text-xs text-white/80"><x-heroicon-m-map-pin class="h-3.5 w-3.5"/>{{ $portfolio->location }}{{ $portfolio->province ? ', '.$portfolio->province : '' }}</p>@endif
    </div>
</a>
