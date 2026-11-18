@php
    $seo = $seo ?? [];
    $siteName = setting('company.name', 'Neira Energy');
    $metaTitle = ($seo['title'] ?? null) ? $seo['title'].' | '.$siteName : setting('seo.meta_title', $siteName);
    $metaDesc = $seo['description'] ?? setting('seo.meta_description');
    $metaImage = media($seo['image'] ?? setting('seo.og_image'), 'images/logo.png');
    $logo = media(setting('company.logo'), 'images/logo.png');
    $tracking = setting('tracking', []) ?: [];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $metaTitle }}</title>
    @if($metaDesc)<meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDesc), 165) }}">@endif
    <meta property="og:title" content="{{ $metaTitle }}">
    @if($metaDesc)<meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDesc), 200) }}">@endif
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ media(setting('company.favicon'), 'images/logo-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @if(!empty($tracking['gtm']))
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $tracking['gtm'] }}');</script>
    @endif
    @if(!empty($tracking['ga4']))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $tracking['ga4'] }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $tracking['ga4'] }}');</script>
    @endif
    @if(!empty($tracking['meta_pixel']))
        <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{{ $tracking['meta_pixel'] }}');fbq('track','PageView');</script>
    @endif
</head>
<body class="flex min-h-screen flex-col">
    {{-- Header --}}
    <header x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 10"
            :class="scrolled ? 'shadow-md' : ''" class="no-print sticky top-0 z-50 border-b border-slate-100 bg-white/95 backdrop-blur transition">
        <div class="container-x flex h-16 items-center justify-between gap-6 lg:h-20">
            <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ $siteName }}">
                <img src="{{ $logo }}" alt="{{ $siteName }}" class="h-9 w-auto lg:h-11">
            </a>

            <nav class="hidden items-center gap-1 text-sm font-medium text-slate-700 lg:flex">
                <div x-data="{ m: false }" @mouseenter="m = true" @mouseleave="m = false" class="relative">
                    <a href="{{ route('products.index') }}" class="flex items-center gap-1 rounded-full px-3 py-2 hover:text-brand-600">
                        Produk & Layanan <x-heroicon-m-chevron-down class="h-4 w-4"/>
                    </a>
                    <div x-show="m" x-transition.opacity x-cloak class="absolute left-1/2 top-full w-[720px] -translate-x-1/2 pt-3">
                        <div class="grid grid-cols-2 gap-1 rounded-2xl border border-slate-100 bg-white p-4 shadow-xl">
                            @foreach($navCategories as $cat)
                                <div class="rounded-xl p-3 hover:bg-brand-50/60">
                                    <a href="{{ $cat->url() }}" @if($cat->external_url) target="_blank" rel="noopener" @endif class="flex items-center gap-2 font-semibold text-slate-900 hover:text-brand-600">
                                        <x-icon :name="$cat->icon" class="h-5 w-5 text-brand-500"/> {{ $cat->name }}
                                    </a>
                                    @if($cat->children->isNotEmpty())
                                        <div class="mt-1.5 flex flex-wrap gap-x-3 gap-y-1 pl-7 text-xs text-slate-500">
                                            @foreach($cat->children as $child)
                                                <a href="{{ $child->url() }}" class="hover:text-brand-600">{{ $child->name }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div x-data="{ m: false }" @mouseenter="m = true" @mouseleave="m = false" class="relative">
                    <button class="flex items-center gap-1 rounded-full px-3 py-2 hover:text-brand-600">Solusi <x-heroicon-m-chevron-down class="h-4 w-4"/></button>
                    <div x-show="m" x-transition.opacity x-cloak class="absolute left-0 top-full w-72 pt-3">
                        <div class="rounded-2xl border border-slate-100 bg-white p-2 shadow-xl">
                            @foreach($navSegments as $seg)
                                <a href="{{ route('segments.show', $seg) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-brand-50/60 hover:text-brand-600">
                                    <x-icon :name="$seg->icon" class="h-5 w-5 text-accent-500"/> {{ $seg->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <a href="{{ route('portfolio.index') }}" class="rounded-full px-3 py-2 hover:text-brand-600">Portfolio</a>
                <a href="{{ route('about') }}" class="rounded-full px-3 py-2 hover:text-brand-600">Tentang</a>
                <a href="{{ route('blog.index') }}" class="rounded-full px-3 py-2 hover:text-brand-600">Artikel</a>
                @if($hasPromo)
                    <a href="{{ route('promos.index') }}" class="rounded-full px-3 py-2 text-accent-600 hover:text-accent-700">Promo</a>
                @endif
                <a href="{{ route('contact') }}" class="rounded-full px-3 py-2 hover:text-brand-600">Kontak</a>
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ wa_link() }}" target="_blank" rel="noopener" class="btn-primary hidden sm:inline-flex">
                    <x-heroicon-s-chat-bubble-left-right class="h-4 w-4"/> Konsultasi via WhatsApp
                </a>
                <button @click="open = !open" class="rounded-lg p-2 text-slate-700 lg:hidden" aria-label="Menu">
                    <x-heroicon-o-bars-3 x-show="!open" class="h-6 w-6"/>
                    <x-heroicon-o-x-mark x-show="open" x-cloak class="h-6 w-6"/>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" x-collapse x-cloak class="border-t border-slate-100 bg-white lg:hidden">
            <div class="container-x max-h-[75vh] space-y-1 overflow-y-auto py-4 text-sm font-medium">
                <p class="px-2 pt-1 text-xs font-semibold uppercase tracking-wider text-slate-500">Produk & Layanan</p>
                @foreach($navCategories as $cat)
                    <a href="{{ $cat->url() }}" class="block rounded-lg px-2 py-2 hover:bg-brand-50">{{ $cat->name }}</a>
                @endforeach
                <p class="px-2 pt-3 text-xs font-semibold uppercase tracking-wider text-slate-500">Solusi</p>
                @foreach($navSegments as $seg)
                    <a href="{{ route('segments.show', $seg) }}" class="block rounded-lg px-2 py-2 hover:bg-brand-50">{{ $seg->name }}</a>
                @endforeach
                <div class="my-2 border-t border-slate-100"></div>
                <a href="{{ route('portfolio.index') }}" class="block rounded-lg px-2 py-2 hover:bg-brand-50">Portfolio</a>
                <a href="{{ route('about') }}" class="block rounded-lg px-2 py-2 hover:bg-brand-50">Tentang Kami</a>
                <a href="{{ route('blog.index') }}" class="block rounded-lg px-2 py-2 hover:bg-brand-50">Artikel</a>
                @if($hasPromo)<a href="{{ route('promos.index') }}" class="block rounded-lg px-2 py-2 text-accent-600 hover:bg-brand-50">Promo</a>@endif
                <a href="{{ route('gallery.index') }}" class="block rounded-lg px-2 py-2 hover:bg-brand-50">Galeri</a>
                <a href="{{ route('faq') }}" class="block rounded-lg px-2 py-2 hover:bg-brand-50">FAQ</a>
                <a href="{{ route('contact') }}" class="block rounded-lg px-2 py-2 hover:bg-brand-50">Kontak</a>
                <a href="{{ wa_link() }}" target="_blank" rel="noopener" class="btn-primary mt-3 w-full">Konsultasi via WhatsApp</a>
            </div>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="no-print bg-brand-950 text-brand-100/80">
        <div class="container-x grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                @if(setting('company.logo_white'))
                    <img src="{{ media(setting('company.logo_white')) }}" alt="{{ $siteName }}" class="h-11 w-auto">
                @else
                    <span class="inline-block rounded-xl bg-white px-3 py-2"><img src="{{ $logo }}" alt="{{ $siteName }}" class="h-9 w-auto"></span>
                @endif
                <p class="mt-5 text-sm leading-relaxed">{{ setting('company.short_about') }}</p>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach((setting('company.badges') ?: []) as $badge)
                        <span class="rounded-full border border-white/15 px-3 py-1 text-xs text-white/80">{{ $badge }}</span>
                    @endforeach
                </div>
            </div>
            <div class="lg:col-span-2">
                <h4 class="text-sm font-semibold text-white">Produk</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    @foreach($navCategories as $cat)
                        <li><a href="{{ $cat->url() }}" class="hover:text-white">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="lg:col-span-2">
                <h4 class="text-sm font-semibold text-white">Perusahaan</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-white">Tentang Kami</a></li>
                    <li><a href="{{ route('portfolio.index') }}" class="hover:text-white">Portfolio</a></li>
                    <li><a href="{{ route('blog.index') }}" class="hover:text-white">Artikel</a></li>
                    <li><a href="{{ route('gallery.index') }}" class="hover:text-white">Galeri</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-white">FAQ</a></li>
                    @foreach($navPages as $p)
                        <li><a href="{{ route('page.show', $p) }}" class="hover:text-white">{{ $p->title }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="lg:col-span-4">
                <h4 class="text-sm font-semibold text-white">Hubungi Kami</h4>
                <ul class="mt-4 space-y-3 text-sm">
                    @foreach((setting('contact.addresses') ?: []) as $addr)
                        <li class="flex gap-3"><x-heroicon-o-map-pin class="mt-0.5 h-5 w-5 shrink-0 text-accent-400"/>
                            <span><strong class="text-white">{{ $addr['label'] ?? '' }}</strong><br>{{ $addr['address'] ?? '' }}</span></li>
                    @endforeach
                    @if(setting('contact.phone'))
                        <li class="flex gap-3"><x-heroicon-o-phone class="h-5 w-5 shrink-0 text-accent-400"/>{{ setting('contact.phone') }}</li>
                    @endif
                    @if(setting('contact.email'))
                        <li class="flex gap-3"><x-heroicon-o-envelope class="h-5 w-5 shrink-0 text-accent-400"/><a href="mailto:{{ setting('contact.email') }}" class="hover:text-white">{{ setting('contact.email') }}</a></li>
                    @endif
                </ul>
                <div class="mt-5 flex gap-3">
                    @foreach(['instagram' => 'IG', 'facebook' => 'FB', 'linkedin' => 'in', 'youtube' => 'YT', 'tiktok' => 'TT'] as $k => $lbl)
                        @if(setting("social.$k"))
                            <a href="{{ setting("social.$k") }}" target="_blank" rel="noopener" class="grid h-9 w-9 place-items-center rounded-full border border-white/20 text-xs font-semibold text-white hover:bg-white/10" aria-label="{{ $k }}">{{ $lbl }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="container-x flex flex-col items-center justify-between gap-2 py-5 text-xs sm:flex-row">
                <p>&copy; {{ date('Y') }} {{ setting('company.legal_name', $siteName) }}. Hak cipta dilindungi.</p>
                <p>{{ setting('company.slogan') }}</p>
            </div>
        </div>
    </footer>

    {{-- Floating WhatsApp --}}
    <a href="{{ wa_link() }}" target="_blank" rel="noopener" aria-label="Konsultasi via WhatsApp"
       x-data="{ show: window.innerWidth >= 1024 || window.scrollY > 900 }" @scroll.window.throttle.150ms="show = window.innerWidth >= 1024 || window.scrollY > 900"
       :class="show ? 'opacity-100 translate-y-0' : 'pointer-events-none opacity-0 translate-y-3'"
       class="no-print fixed bottom-5 right-5 z-40 grid h-14 w-14 place-items-center rounded-full bg-[#25D366] text-white shadow-lg ring-4 ring-white transition hover:scale-105">
        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="currentColor"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.42.25-.7.25-1.29.17-1.42-.07-.12-.27-.2-.57-.35M12.05 21.5h-.01a9.4 9.4 0 0 1-4.8-1.32l-.34-.2-3.57.94.95-3.48-.22-.36a9.4 9.4 0 0 1-1.44-5.01c0-5.2 4.23-9.44 9.44-9.44 2.52 0 4.89.98 6.67 2.77a9.37 9.37 0 0 1 2.76 6.68c0 5.2-4.24 9.43-9.44 9.43m8.03-17.46A11.27 11.27 0 0 0 12.04.72C5.79.72.7 5.8.7 12.06c0 2 .52 3.95 1.52 5.67L.6 23.6l6.01-1.58a11.3 11.3 0 0 0 5.43 1.38h.01c6.25 0 11.34-5.08 11.34-11.34 0-3.03-1.18-5.88-3.32-8.02"/></svg>
    </a>
    @stack('scripts')
</body>
</html>
