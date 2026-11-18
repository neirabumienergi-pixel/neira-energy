@extends('layouts.site')

@section('content')
    {{-- Hero slider --}}
    @if($banners->isNotEmpty())
        <section x-data="{ i: 0, n: {{ $banners->count() }}, t: null, go(k){ this.i = (k + this.n) % this.n }, start(){ if(this.n>1) this.t = setInterval(() => this.go(this.i + 1), 6500) } }"
                 x-init="start()" class="relative h-[85vh] min-h-[660px] overflow-hidden bg-brand-950 sm:min-h-[560px]">
            @foreach($banners as $k => $b)
                <div x-show="i === {{ $k }}" x-transition:enter="transition duration-700" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition duration-700" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     @if($k) x-cloak @endif class="absolute inset-0">
                    <picture>
                        @if($b->image_mobile)<source media="(max-width: 640px)" srcset="{{ media($b->image_mobile) }}">@endif
                        <img src="{{ media($b->image) }}" alt="{{ $b->title }}" class="absolute inset-0 h-full w-full object-cover" @if($k) loading="lazy" @endif>
                    </picture>
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-950/95 via-brand-950/70 to-brand-950/10"></div>
                    <div class="container-x relative flex h-full items-center pb-20 sm:pb-10">
                        <div class="max-w-3xl text-white">
                            @if($b->subtitle)<p class="eyebrow">{{ $b->subtitle }}</p>@endif
                            <h1 class="mt-3 text-[2rem] font-bold leading-tight text-white sm:text-5xl lg:text-[3.5rem]">{{ $b->title }}</h1>
                            @if($b->description)<p class="mt-5 text-lg text-white/85">{{ $b->description }}</p>@endif
                            <div class="mt-8 flex flex-wrap gap-3">
                                @if($b->cta_label)<a href="{{ $b->cta_url ?: wa_link() }}" class="btn-accent px-6 py-3">{{ $b->cta_label }} <x-heroicon-m-arrow-right class="h-4 w-4"/></a>@endif
                                @if($b->cta2_label)<a href="{{ $b->cta2_url ?: '#' }}" class="btn-ghost-light px-6 py-3">{{ $b->cta2_label }}</a>@endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @if($banners->count() > 1)
                <div class="absolute bottom-8 left-1/2 flex -translate-x-1/2 gap-2">
                    @foreach($banners as $k => $b)
                        <button @click="go({{ $k }}); clearInterval(t); start()" :class="i === {{ $k }} ? 'w-8 bg-accent-500' : 'w-2.5 bg-white/50'" class="h-2.5 rounded-full transition-all" aria-label="Slide {{ $k + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    {{-- Stats --}}
    @if($stats = setting('stats'))
        <section class="relative z-10 -mt-14">
            <div class="container-x">
                <div class="grid grid-cols-2 gap-px overflow-hidden rounded-2xl bg-slate-200 shadow-xl lg:grid-cols-4">
                    @foreach($stats as $s)
                        <div class="bg-white px-6 py-6 text-center">
                            <p class="text-3xl font-extrabold text-brand-600">{{ $s['value'] ?? '' }}<span class="ml-1 text-lg text-accent-500">{{ $s['unit'] ?? '' }}</span></p>
                            <p class="mt-1 text-xs text-slate-500 sm:text-sm">{{ $s['label'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Products --}}
    <section class="section">
        <div class="container-x">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <x-section-heading eyebrow="Produk & Layanan" title="Solusi energi surya end-to-end" text="Dari perencanaan, pengadaan, instalasi, hingga perizinan dan perawatan — satu mitra untuk seluruh kebutuhan energi surya Anda."/>
                <a href="{{ route('products.index') }}" class="btn-outline">Semua produk <x-heroicon-m-arrow-right class="h-4 w-4"/></a>
            </div>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-{{ min(5, max(3, $categories->count())) }}">
                @foreach($categories as $cat)
                    <a href="{{ $cat->url() }}" @if($cat->external_url) target="_blank" rel="noopener" @endif class="card group flex flex-col">
                        <div class="relative aspect-[4/3] overflow-hidden bg-brand-50">
                            @if($cat->image)<img src="{{ media($cat->image) }}" alt="{{ $cat->name }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">@endif
                            <div class="absolute left-4 top-4 grid h-11 w-11 place-items-center rounded-xl bg-white text-brand-600 shadow"><x-icon :name="$cat->icon" class="h-6 w-6"/></div>
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <h3 class="text-lg font-semibold text-slate-900 group-hover:text-brand-600">{{ $cat->name }}</h3>
                            <p class="mt-1 line-clamp-3 text-sm text-slate-500">{{ $cat->short_description }}</p>
                            @if($cat->children->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach($cat->children->take(4) as $c)<span class="chip">{{ $c->name }}</span>@endforeach
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Segments --}}
    @if($segments->isNotEmpty())
        <section class="section bg-brand-950 text-white">
            <div class="container-x">
                <div class="max-w-2xl">
                    <p class="eyebrow">Solusi untuk setiap segmen</p>
                    <h2 class="mt-2 text-3xl font-bold text-white sm:text-4xl">Kami memahami kebutuhan Anda</h2>
                </div>
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach($segments as $seg)
                        <a href="{{ route('segments.show', $seg) }}" class="group rounded-2xl border border-white/10 bg-white/5 p-6 transition hover:border-accent-500 hover:bg-white/10">
                            <x-icon :name="$seg->icon" class="h-8 w-8 text-sun-400"/>
                            <h3 class="mt-4 font-semibold text-white">{{ $seg->name }}</h3>
                            <p class="mt-2 line-clamp-3 text-sm text-white/70">{{ $seg->short_description }}</p>
                            <span class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-accent-400 group-hover:gap-2 transition-all">Selengkapnya <x-heroicon-m-arrow-right class="h-4 w-4"/></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Featured packages --}}
    @if($featuredPackages->isNotEmpty())
        <section class="section bg-slate-50">
            <div class="container-x">
                <x-section-heading eyebrow="Paket Unggulan" title="Paket siap pasang, harga transparan" center/>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($featuredPackages as $p)<x-package-card :package="$p"/>@endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Promos --}}
    @if($promos->isNotEmpty())
        <section class="section">
            <div class="container-x">
                <x-section-heading eyebrow="Promo" title="Penawaran spesial bulan ini"/>
                <div class="mt-8 grid gap-6 md:grid-cols-{{ min(3, $promos->count()) }}">
                    @foreach($promos as $promo)
                        <a href="{{ route('promos.show', $promo) }}" class="card group relative flex items-center gap-5 bg-gradient-to-br from-accent-50 to-white p-6">
                            <div class="grid h-20 w-20 shrink-0 place-items-center rounded-2xl bg-accent-500 text-center text-lg font-extrabold leading-tight text-white">{{ $promo->label }}</div>
                            <div>
                                <h3 class="font-semibold text-slate-900 group-hover:text-brand-600">{{ $promo->title }}</h3>
                                <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $promo->short_description }}</p>
                                @if($promo->ends_at)<p class="mt-2 text-xs font-medium text-accent-700">s.d. {{ $promo->ends_at->translatedFormat('d F Y') }}</p>@endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Why us --}}
    @if($why = setting('about.why'))
        <section class="section {{ $promos->isNotEmpty() ? 'bg-slate-50' : '' }}">
            <div class="container-x grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <p class="eyebrow">Kenapa Neira?</p>
                    <h2 class="section-title mt-2">Mitra energi surya end-to-end di Indonesia</h2>
                    <div class="mt-8 grid gap-5 sm:grid-cols-2">
                        @foreach($why as $w)
                            <div>
                                <div class="grid h-11 w-11 place-items-center rounded-xl bg-brand-500 text-white"><x-icon :name="$w['icon'] ?? null" class="h-6 w-6"/></div>
                                <h3 class="mt-3 font-semibold text-slate-900">{{ $w['title'] ?? '' }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $w['text'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="relative">
                    <img src="{{ media(setting('about.image'), 'images/logo.png') }}" alt="" class="aspect-[4/5] w-full rounded-3xl object-cover shadow-xl" loading="lazy">
                    @if($certifications->isNotEmpty())
                        <div class="absolute -bottom-6 -left-4 max-w-xs rounded-2xl bg-white p-5 shadow-xl sm:-left-8">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Legalitas</p>
                            <ul class="mt-2 space-y-1.5 text-sm">
                                @foreach($certifications->take(5) as $c)
                                    <li class="flex items-center gap-2"><x-heroicon-s-check-badge class="h-4 w-4 {{ $c->status === 'active' ? 'text-brand-500' : 'text-accent-500' }}"/>{{ $c->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Portfolio --}}
    @if($portfolios->isNotEmpty())
        <section class="section">
            <div class="container-x">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-section-heading eyebrow="Portfolio" title="Proyek yang telah kami bangun"/>
                    <a href="{{ route('portfolio.index') }}" class="btn-outline">Lihat semua <x-heroicon-m-arrow-right class="h-4 w-4"/></a>
                </div>
                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($portfolios as $p)<x-portfolio-card :portfolio="$p"/>@endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Clients --}}
    @if($clients->isNotEmpty())
        <section class="border-y border-slate-100 bg-white py-12">
            <div class="container-x">
                <p class="text-center text-sm font-semibold uppercase tracking-wider text-slate-400">Dipercaya oleh klien & partner</p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-x-10 gap-y-6">
                    @foreach($clients as $c)
                        @if($c->logo)
                            <img src="{{ media($c->logo) }}" alt="{{ $c->name }}" title="{{ $c->name }}" class="h-10 w-auto opacity-70 grayscale transition hover:opacity-100 hover:grayscale-0" loading="lazy">
                        @else
                            <span class="text-sm font-semibold text-slate-400">{{ $c->name }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Testimonials --}}
    @if($testimonials->isNotEmpty())
        <section class="section bg-slate-50">
            <div class="container-x">
                <x-section-heading eyebrow="Testimoni" title="Kata mereka tentang Neira" center/>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach($testimonials as $t)
                        <figure class="rounded-2xl bg-white p-6 shadow-sm">
                            <div class="text-accent-500">{{ str_repeat('★', $t->rating) }}</div>
                            <blockquote class="mt-3 text-slate-700">“{{ $t->content }}”</blockquote>
                            <figcaption class="mt-5 flex items-center gap-3">
                                @if($t->photo)<img src="{{ media($t->photo) }}" class="h-10 w-10 rounded-full object-cover" alt="">@endif
                                <div><p class="font-semibold text-slate-900">{{ $t->name }}</p><p class="text-xs text-slate-500">{{ trim($t->position.' · '.$t->company, ' ·') }}</p></div>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Blog --}}
    @if($posts->isNotEmpty())
        <section class="section">
            <div class="container-x">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-section-heading eyebrow="Artikel" title="Wawasan energi terbarukan"/>
                    <a href="{{ route('blog.index') }}" class="btn-outline">Semua artikel <x-heroicon-m-arrow-right class="h-4 w-4"/></a>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach($posts as $post)<x-post-card :post="$post"/>@endforeach
                </div>
            </div>
        </section>
    @endif

    @include('blocks.cta', ['data' => ['title' => 'Siap beralih ke energi surya?', 'text' => 'Konsultasikan kebutuhan Anda dengan tim engineer kami. Gratis survei & simulasi penghematan.', 'button_label' => 'Konsultasi via WhatsApp'], 'index' => 0])
@endsection
