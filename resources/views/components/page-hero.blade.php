@props(['title', 'subtitle' => null, 'eyebrow' => null, 'image' => null, 'crumbs' => []])
<section class="relative overflow-hidden bg-brand-950">
    @if($image)
        <img src="{{ $image }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30">
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-brand-950 via-brand-950/85 to-brand-900/40"></div>
    <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-accent-500/20 blur-3xl"></div>
    <div class="container-x relative py-14 sm:py-20">
        @if($crumbs)
            <nav class="mb-4 flex flex-wrap items-center gap-1.5 text-xs text-brand-100/80">
                <a href="{{ route('home') }}" class="hover:text-white">Beranda</a>
                @foreach($crumbs as $label => $url)
                    <x-heroicon-m-chevron-right class="h-3.5 w-3.5"/>
                    @if($url)<a href="{{ $url }}" class="hover:text-white">{{ $label }}</a>@else<span class="text-white">{{ $label }}</span>@endif
                @endforeach
            </nav>
        @endif
        <h1 class="max-w-3xl text-balance text-3xl font-bold tracking-tight text-white sm:text-5xl">{{ $title }}</h1>
        @if($subtitle)<p class="mt-4 max-w-2xl text-base text-brand-100/85 sm:text-lg">{{ $subtitle }}</p>@endif
        {{ $slot }}
    </div>
</section>
