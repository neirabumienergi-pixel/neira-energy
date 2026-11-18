@extends('layouts.site', ['seo' => ['title' => 'Tentang Kami', 'description' => setting('company.short_about')]])

@section('content')
    <x-page-hero :title="setting('about.heading', 'Tentang Kami')" :subtitle="setting('company.tagline')" eyebrow="Tentang Kami" :image="media(setting('about.image'))" :crumbs="['Tentang Kami' => null]"/>

    <section class="section">
        <div class="container-x grid gap-12 lg:grid-cols-2">
            <div class="prose prose-slate max-w-none prose-strong:text-slate-900">{!! setting('about.body') !!}</div>
            <div class="space-y-6">
                @if(setting('about.vision'))
                    <div class="rounded-3xl bg-brand-600 p-8 text-white">
                        <h2 class="text-sm font-semibold text-sun-300">Visi</h2>
                        <p class="mt-3 text-xl font-semibold leading-relaxed text-white">{{ setting('about.vision') }}</p>
                    </div>
                @endif
                @if($missions = setting('about.missions'))
                    <div class="rounded-3xl border border-slate-200 p-8">
                        <h2 class="text-lg font-bold text-slate-900">Misi</h2>
                        <ol class="mt-4 space-y-3">
                            @foreach($missions as $i => $m)
                                <li class="flex gap-4"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-accent-500 text-sm font-bold text-brand-950">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><span class="pt-1">{{ is_array($m) ? ($m['text'] ?? '') : $m }}</span></li>
                            @endforeach
                        </ol>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if($units = setting('units'))
        <section class="section bg-slate-50">
            <div class="container-x">
                <x-section-heading eyebrow="Ekosistem" title="Satu ekosistem energi terintegrasi" center/>
                <div class="mt-10 grid gap-6 md:grid-cols-2">
                    @foreach($units as $u)
                        <div class="rounded-3xl bg-white p-7 shadow-sm">
                            <div class="flex items-center gap-4">
                                @if(!empty($u['logo']))<img src="{{ media($u['logo']) }}" class="h-10 w-auto" alt="">@endif
                                <div><h3 class="text-lg font-semibold">{{ $u['name'] ?? '' }}</h3><p class="text-sm text-brand-600">{{ $u['label'] ?? '' }}</p></div>
                            </div>
                            <p class="mt-4 text-sm text-slate-600">{{ $u['description'] ?? '' }}</p>
                            @if(!empty($u['services']))
                                <ul class="mt-4 space-y-1.5 text-sm">
                                    @foreach($u['services'] as $s)<li class="flex items-start gap-2"><x-heroicon-s-check-circle class="mt-0.5 h-4 w-4 shrink-0 text-brand-500"/>{{ $s }}</li>@endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($why = setting('about.why'))
        @include('blocks.features', ['data' => ['heading' => 'Kenapa Neira berbeda?', 'items' => $why], 'index' => 0, 'context' => []])
    @endif

    @if($stats = setting('stats'))
        @include('blocks.stats', ['data' => ['heading' => 'Neira Energy dalam angka', 'items' => collect($stats)->map(fn ($s) => ['value' => trim(($s['value'] ?? '').' '.($s['unit'] ?? '')), 'label' => $s['label'] ?? ''])->all()], 'index' => 0, 'context' => []])
    @endif

    @if($certifications->isNotEmpty())
        <section id="legalitas" class="section">
            <div class="container-x">
                <x-section-heading eyebrow="Kepatuhan Regulasi" title="Legalitas & Sertifikasi" text="Seluruh aktivitas bisnis dijalankan berdasarkan kepatuhan regulasi, tata kelola yang baik, serta standar keselamatan dan mutu." center/>
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($certifications as $c)
                        <div class="flex gap-4 rounded-2xl border border-slate-200 p-5">
                            @if($c->logo)<img src="{{ media($c->logo) }}" class="h-12 w-12 object-contain" alt="">@else<x-heroicon-o-shield-check class="h-10 w-10 shrink-0 text-brand-500"/>@endif
                            <div>
                                <h3 class="font-semibold text-slate-900">{{ $c->name }}</h3>
                                @if($c->number)<p class="text-xs text-slate-500">No. {{ $c->number }}</p>@endif
                                <span class="mt-2 inline-block rounded-full px-2.5 py-0.5 text-xs font-medium {{ $c->status === 'active' ? 'bg-brand-50 text-brand-700' : 'bg-accent-50 text-accent-700' }}">{{ $c->status === 'active' ? 'Berlaku' : 'Dalam proses' }}</span>
                                @if($c->show_file && $c->file)<a href="{{ media($c->file) }}" target="_blank" class="ml-2 text-xs font-medium text-brand-600 hover:underline">Lihat dokumen</a>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($esg = setting('about.esg'))
        <section class="section bg-brand-950 text-white">
            <div class="container-x">
                <div class="mx-auto max-w-2xl text-center"><h2 class="text-3xl font-bold text-white">Komitmen ESG</h2><p class="mt-2 text-brand-100/80">Sustainability & governance di seluruh ekosistem Neira.</p></div>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach($esg as $e)
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-6"><h3 class="font-semibold text-sun-400">{{ $e['title'] ?? '' }}</h3><p class="mt-2 text-sm text-white/75">{{ $e['text'] ?? '' }}</p></div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(setting('about.team_image'))
        <section class="section">
            <div class="container-x">
                <x-section-heading eyebrow="Tim Kami" title="Tenaga profesional di balik setiap proyek" center/>
                <img src="{{ media(setting('about.team_image')) }}" alt="Tim Neira Energy" class="mt-10 w-full rounded-3xl object-cover" loading="lazy">
            </div>
        </section>
    @endif
@endsection
