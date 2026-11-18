@extends('layouts.site', ['seo' => ['title' => 'Promo', 'description' => 'Promo dan penawaran spesial paket PLTS & PJU Neira Energy.']])

@section('content')
    <x-page-hero title="Promo & Penawaran" subtitle="Hemat lebih banyak untuk investasi energi surya Anda." eyebrow="Promo" :crumbs="['Promo' => null]"/>
    <section class="section">
        <div class="container-x grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($promos as $promo)
                <a href="{{ route('promos.show', $promo) }}" class="card group flex flex-col">
                    <div class="relative aspect-[16/9] bg-gradient-to-br from-accent-500 to-accent-700">
                        @if($promo->image)<img src="{{ media($promo->image) }}" alt="{{ $promo->title }}" class="h-full w-full object-cover">@else
                            <div class="grid h-full place-items-center text-5xl font-extrabold text-white">{{ $promo->label }}</div>@endif
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <h3 class="text-lg font-semibold group-hover:text-brand-600">{{ $promo->title }}</h3>
                        <p class="mt-2 text-sm text-slate-500">{{ $promo->short_description }}</p>
                        <div class="mt-auto flex items-center justify-between pt-4 text-xs">
                            @if($promo->code)<span class="rounded-md border border-dashed border-accent-500 px-2 py-1 font-mono font-semibold text-accent-700">{{ $promo->code }}</span>@endif
                            @if($promo->ends_at)<span class="text-slate-500">Berlaku s.d. {{ $promo->ends_at->translatedFormat('d M Y') }}</span>@endif
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-slate-500">Belum ada promo yang berjalan. Hubungi kami untuk penawaran terbaik.</p>
            @endforelse
        </div>
    </section>
@endsection
