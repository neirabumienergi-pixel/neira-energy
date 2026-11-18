@extends('layouts.site')

@section('content')
    @include('partials.hero')

    {{-- Angka + legalitas: bukti kepercayaan langsung di bawah hero --}}
    @php($stats = setting('stats') ?: [])
    @if($stats || $certifications->isNotEmpty())
        <section class="relative z-10 -mt-14">
            <div class="container-x">
                <div class="overflow-hidden rounded-2xl bg-white shadow-[0_18px_50px_-24px_rgba(4,36,20,.45)]">
                    @if($stats)
                        <dl class="grid grid-cols-2 divide-slate-200 lg:grid-cols-4 lg:divide-x [&>div:nth-child(-n+2)]:border-b [&>div:nth-child(-n+2)]:border-slate-200 lg:[&>div:nth-child(-n+2)]:border-b-0">
                            @foreach($stats as $s)
                                <div class="flex flex-col-reverse px-5 py-6 text-center sm:px-6">
                                    <dt class="mt-1 text-sm text-slate-600">{{ $s['label'] ?? '' }}</dt>
                                    <dd class="text-3xl font-extrabold tabular-nums tracking-tight text-brand-700">{{ $s['value'] ?? '' }}@if(!empty($s['unit']))<span class="ml-1 text-base font-bold text-accent-800">{{ $s['unit'] }}</span>@endif</dd>
                                </div>
                            @endforeach
                        </dl>
                    @endif
                    @if($certifications->isNotEmpty())
                        <div class="border-t border-slate-200 bg-slate-50 px-5 py-4 sm:px-6">
                            <ul class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-700">
                                <li class="font-semibold text-slate-900">Legal & tersertifikasi:</li>
                                @foreach($certifications as $c)
                                    <li class="flex items-center gap-1.5">
                                        @if($c->status === 'active')
                                            <x-heroicon-s-check-badge class="h-4 w-4 text-brand-600"/>{{ $c->name }}
                                        @else
                                            <x-heroicon-o-clock class="h-4 w-4 text-accent-800"/>{{ $c->name }} <span class="text-slate-500">(proses)</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                @if(collect($stats)->contains(fn ($s) => str_contains($s['label'] ?? '', '*')))
                    <p class="mt-2 text-right text-xs text-slate-500">* Berdasarkan faktor emisi grid & performa sistem terpasang.</p>
                @endif
            </div>
        </section>
    @endif

    {{-- Produk --}}
    <section class="section">
        <div class="container-x">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <x-section-heading title="Solusi energi surya end-to-end" text="Dari perencanaan, pengadaan, instalasi, hingga perizinan dan perawatan. Satu mitra untuk seluruh kebutuhan energi surya Anda."/>
                <a href="{{ route('products.index') }}" class="btn-outline">Semua produk <x-heroicon-m-arrow-right class="h-4 w-4"/></a>
            </div>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-{{ min(5, max(3, $categories->count())) }}">
                @foreach($categories as $cat)
                    <a href="{{ $cat->url() }}" @if($cat->external_url) target="_blank" rel="noopener" @endif class="card group flex flex-col">
                        <div class="relative aspect-[4/3] overflow-hidden bg-brand-900">
                            @if($cat->image)
                                <img src="{{ media($cat->image) }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="grid h-full place-items-center"><x-icon :name="$cat->icon" class="h-14 w-14 text-sun-400"/></div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <h3 class="text-lg font-semibold text-slate-900 group-hover:text-brand-700">{{ $cat->name }}</h3>
                            <p class="mt-1 line-clamp-3 text-sm text-slate-600">{{ $cat->short_description }}</p>
                            @if($cat->children->isNotEmpty())
                                <p class="mt-auto pt-4 text-xs leading-relaxed text-slate-500">{{ $cat->children->take(4)->pluck('name')->implode(' · ') }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Paket unggulan --}}
    @if($featuredPackages->isNotEmpty())
        <section class="section bg-slate-50">
            <div class="container-x">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-section-heading title="Paket siap pasang, harga transparan" text="Harga tertera per paket. Hitung dulu kebutuhan Anda di kalkulator untuk rekomendasi yang pas."/>
                    <a href="#kalkulator" class="btn-outline"><x-heroicon-o-calculator class="h-4 w-4"/> Hitung kebutuhan</a>
                </div>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($featuredPackages as $p)<x-package-card :package="$p"/>@endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Promo --}}
    @if($promos->isNotEmpty())
        <section class="section">
            <div class="container-x">
                <x-section-heading title="Penawaran spesial bulan ini"/>
                <div class="mt-8 grid gap-6 md:grid-cols-{{ min(3, $promos->count()) }}">
                    @foreach($promos as $promo)
                        <a href="{{ route('promos.show', $promo) }}" class="card group relative flex items-center gap-5 p-6">
                            <div class="grid h-20 w-20 shrink-0 place-items-center rounded-xl bg-sun-400 text-center text-lg font-extrabold leading-tight text-brand-950">{{ $promo->label }}</div>
                            <div>
                                <h3 class="font-semibold text-slate-900 group-hover:text-brand-700">{{ $promo->title }}</h3>
                                <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $promo->short_description }}</p>
                                @if($promo->ends_at)<p class="mt-2 text-xs font-medium text-accent-800">Berlaku s.d. {{ $promo->ends_at->translatedFormat('d F Y') }}</p>@endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Bukti: kemampuan khas Neira (diatur di Identitas Perusahaan → Tentang Kami) --}}
    @if($why = setting('about.why'))
        <section class="section {{ $promos->isNotEmpty() ? 'bg-slate-50' : '' }}">
            <div class="container-x">
                <x-section-heading title="Dikerjakan sendiri, dari workshop sampai izin terbit" text="Tiga hal yang membuat proyek Anda tidak bergantung pada pihak lain."/>
                <div class="mt-12 space-y-14 lg:space-y-20">
                    @foreach($why as $i => $w)
                        <article class="grid items-center gap-8 lg:grid-cols-12 lg:gap-12">
                            @if(!empty($w['image']))
                                <div class="lg:col-span-7 {{ $i % 2 ? 'lg:order-2' : '' }}">
                                    <img src="{{ media($w['image']) }}" alt="" loading="lazy" class="aspect-[16/10] w-full rounded-2xl object-cover">
                                </div>
                            @endif
                            <div class="{{ !empty($w['image']) ? 'lg:col-span-5' : 'lg:col-span-8' }}">
                                <h3 class="text-2xl font-bold tracking-tight text-balance text-slate-900 sm:text-3xl">{{ $w['title'] ?? '' }}</h3>
                                <p class="mt-4 max-w-prose leading-relaxed text-slate-600">{{ $w['text'] ?? '' }}</p>
                                @if(!empty($w['url']))
                                    <a href="{{ $w['url'] }}" class="mt-6 inline-flex items-center gap-1.5 font-semibold text-brand-700 hover:text-brand-800">{{ $w['link_label'] ?? 'Selengkapnya' }} <x-heroicon-m-arrow-right class="h-4 w-4"/></a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Portfolio --}}
    @if($portfolios->isNotEmpty())
        <section class="section">
            <div class="container-x">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-section-heading title="Proyek yang telah kami bangun"/>
                    <a href="{{ route('portfolio.index') }}" class="btn-outline">Lihat semua <x-heroicon-m-arrow-right class="h-4 w-4"/></a>
                </div>
                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($portfolios as $p)<x-portfolio-card :portfolio="$p"/>@endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Klien --}}
    @if($clients->isNotEmpty())
        <section class="border-y border-slate-200 bg-white py-12">
            <div class="container-x">
                <h2 class="text-center text-sm font-semibold text-slate-600">Dipercaya oleh klien & partner</h2>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-x-10 gap-y-6">
                    @foreach($clients as $c)
                        @if($c->logo)
                            <img src="{{ media($c->logo) }}" alt="{{ $c->name }}" title="{{ $c->name }}" class="h-10 w-auto opacity-70 grayscale transition hover:opacity-100 hover:grayscale-0" loading="lazy">
                        @else
                            <span class="text-sm font-semibold text-slate-600">{{ $c->name }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Testimoni --}}
    @if($testimonials->isNotEmpty())
        <section class="section bg-slate-50">
            <div class="container-x">
                <x-section-heading title="Kata mereka tentang Neira" center/>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach($testimonials as $t)
                        <figure class="rounded-2xl bg-white p-6 shadow-sm">
                            <div class="flex gap-0.5 text-accent-500" aria-label="Rating {{ $t->rating }} dari 5">@for($r = 0; $r < $t->rating; $r++)<x-heroicon-s-star class="h-4 w-4"/>@endfor</div>
                            <blockquote class="mt-3 text-slate-700">“{{ $t->content }}”</blockquote>
                            <figcaption class="mt-5 flex items-center gap-3">
                                @if($t->photo)<img src="{{ media($t->photo) }}" class="h-10 w-10 rounded-full object-cover" alt="">@endif
                                <div><p class="font-semibold text-slate-900">{{ $t->name }}</p><p class="text-xs text-slate-600">{{ trim($t->position.' · '.$t->company, ' ·') }}</p></div>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Artikel: tampil bila sudah ada minimal 3 --}}
    @if($posts->count() >= 3)
        <section class="section">
            <div class="container-x">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-section-heading title="Wawasan energi terbarukan"/>
                    <a href="{{ route('blog.index') }}" class="btn-outline">Semua artikel <x-heroicon-m-arrow-right class="h-4 w-4"/></a>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach($posts as $post)<x-post-card :post="$post"/>@endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Penutup: kembali ke kalkulator --}}
    <section class="no-print py-14">
        <div class="container-x">
            <div class="relative overflow-hidden rounded-2xl bg-brand-800 px-6 py-12 text-center sm:px-12">
                <h2 class="text-2xl font-bold text-balance text-white sm:text-3xl">Berapa yang bisa Anda hemat setiap bulan?</h2>
                <p class="mx-auto mt-3 max-w-xl text-brand-50">Geser tagihan listrik Anda di kalkulator, lalu kirim hasilnya ke tim kami. Survei dan desain gratis.</p>
                <div class="mt-7 flex flex-wrap justify-center gap-3">
                    <a href="#kalkulator" class="btn-accent px-6 py-3"><x-heroicon-o-calculator class="h-4 w-4"/> Hitung penghematan</a>
                    <a href="{{ wa_link() }}" target="_blank" rel="noopener" class="btn-ghost-light px-6 py-3">Konsultasi via WhatsApp</a>
                </div>
            </div>
        </div>
    </section>
@endsection
