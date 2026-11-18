@extends('layouts.site', ['seo' => ['title' => $promo->title, 'description' => $promo->short_description, 'image' => $promo->image]])

@section('content')
    <x-page-hero :title="$promo->title" :subtitle="$promo->short_description" :eyebrow="'Diskon '.$promo->label" :image="media($promo->image)" :crumbs="['Promo' => route('promos.index'), $promo->title => null]">
        <div class="mt-6 flex flex-wrap items-center gap-3 text-sm text-white/85">
            @if($promo->code)<span class="rounded-lg border border-dashed border-sun-400 px-3 py-1.5 font-mono font-semibold text-sun-400">Kode: {{ $promo->code }}</span>@endif
            @if($promo->ends_at)<span>Berlaku s.d. {{ $promo->ends_at->translatedFormat('d F Y') }}</span>@endif
            @unless($promo->isRunning())<span class="rounded-full bg-red-500 px-3 py-1 text-xs font-semibold text-white">Promo telah berakhir</span>@endunless
        </div>
    </x-page-hero>
    @if($promo->content)<section class="section"><div class="container-x prose prose-slate max-w-3xl">{!! $promo->content !!}</div></section>@endif
    @if($packages->isNotEmpty() && $promo->isRunning())
        <section class="section bg-slate-50"><div class="container-x"><x-section-heading title="Paket yang mendapat promo" center/>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">@foreach($packages as $p)<x-package-card :package="$p"/>@endforeach</div></div></section>
    @endif
    @include('blocks.cta', ['data' => ['title' => 'Klaim promo ini sekarang', 'text' => 'Hubungi tim kami dan sebutkan kode promo untuk mendapatkan penawaran.', 'button_label' => 'Klaim via WhatsApp'], 'index' => 0])
@endsection
