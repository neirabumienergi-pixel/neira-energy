@php($logo = media(setting('company.logo'), 'images/logo.png'))
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Brosur {{ $category->name }} | {{ setting('company.name', 'Neira Energy') }}</title>
    <meta name="robots" content="noindex">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .sheet { max-width: 210mm; margin: 0 auto; background: #fff; }
        .sheet .container-x { max-width: 100%; padding-left: 12mm; padding-right: 12mm; }
        .sheet .section { padding-top: 10mm; padding-bottom: 10mm; }
        .sheet section { break-inside: avoid; }
        @media screen { body { background: #e2e8f0; } .sheet { margin: 24px auto; box-shadow: 0 10px 40px rgba(0,0,0,.12); } }
        @media print { .sheet { margin: 0; box-shadow: none; } }
    </style>
</head>
<body>
    <div class="no-print sticky top-0 z-50 flex items-center justify-center gap-3 bg-brand-950 px-4 py-3 text-sm text-white">
        <span class="hidden sm:inline">Brosur {{ $category->name }}</span>
        <button onclick="window.print()" class="btn-accent py-1.5"><x-heroicon-o-printer class="h-4 w-4"/> Cetak / Simpan PDF</button>
        <a href="{{ route('products.category', $category) }}" class="btn-ghost-light py-1.5">Kembali</a>
    </div>
    <div class="sheet">
        <header class="flex items-center justify-between border-b-4 border-accent-500 px-[12mm] py-6">
            <img src="{{ $logo }}" alt="" class="h-12 w-auto">
            <div class="text-right text-xs text-slate-500">
                <p class="font-semibold text-slate-900">{{ setting('company.legal_name') }}</p>
                <p>{{ setting('contact.phone') }} · {{ setting('contact.email') }}</p>
                <p>{{ preg_replace('#^https?://#', '', config('app.url')) }}</p>
            </div>
        </header>

        @php($blocks = $category->brochure ?? [])
        @if(($blocks[0]['type'] ?? null) !== 'hero')
            <section class="relative overflow-hidden bg-brand-950 text-white">
                @if($category->image)<img src="{{ media($category->image) }}" class="absolute inset-0 h-full w-full object-cover opacity-35" alt="">@endif
                <div class="relative px-[12mm] py-14">
                    @if($category->tagline)<p class="eyebrow">{{ $category->tagline }}</p>@endif
                    <h1 class="mt-2 text-4xl font-bold text-white">{{ $category->name }}</h1>
                    <p class="mt-3 max-w-xl text-white/85">{{ $category->short_description }}</p>
                </div>
            </section>
        @endif

        <x-blocks :blocks="$blocks" :context="['category' => $category, 'packages' => $packages, 'print' => true]"/>
        @php($types = collect($blocks)->pluck('type'))
        @if(!$types->contains('subcategories'))
            @include('blocks.subcategories', ['data' => [], 'index' => 1, 'context' => ['category' => $category]])
        @endif
        @if(!$types->contains('packages'))
            @include('blocks.packages', ['data' => [], 'index' => 0, 'context' => ['packages' => $packages, 'print' => true]])
        @endif

        <footer class="mt-6 bg-brand-950 px-[12mm] py-8 text-sm text-brand-100">
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <p class="text-lg font-semibold text-white">Hubungi kami</p>
                    <p class="mt-2">WhatsApp: {{ setting('contact.whatsapp') }}</p>
                    <p>Email: {{ setting('contact.email') }}</p>
                    <p>Web: {{ preg_replace('#^https?://#', '', config('app.url')) }}</p>
                </div>
                <div class="space-y-2 text-xs">
                    @foreach((setting('contact.addresses') ?: []) as $a)
                        <p><strong class="text-white">{{ $a['label'] ?? '' }}:</strong> {{ $a['address'] ?? '' }}</p>
                    @endforeach
                </div>
            </div>
            <p class="mt-6 text-[11px] text-brand-100/60">Harga & spesifikasi dapat berubah sewaktu-waktu. Dokumen diperbarui {{ now()->translatedFormat('d F Y') }}.</p>
        </footer>
    </div>
</body>
</html>
