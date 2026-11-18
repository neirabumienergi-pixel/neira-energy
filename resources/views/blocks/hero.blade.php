<section class="relative overflow-hidden bg-brand-950 text-white">
    @if(!empty($data['image']))<img src="{{ media($data['image']) }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-40">@endif
    <div class="absolute inset-0 bg-gradient-to-r from-brand-950 via-brand-950/80 to-transparent"></div>
    <div class="container-x relative py-16 sm:py-24">
        @if(!empty($data['eyebrow']))<p class="eyebrow">{{ $data['eyebrow'] }}</p>@endif
        <h2 class="mt-2 max-w-2xl text-3xl font-bold text-white sm:text-5xl">{{ $data['title'] ?? '' }}</h2>
        @if(!empty($data['text']))<p class="mt-4 max-w-xl text-lg text-white/85">{{ $data['text'] }}</p>@endif
        @if(!empty($data['cta_label']))
            <a href="{{ $data['cta_url'] ?: wa_link() }}" class="btn-accent no-print mt-8">{{ $data['cta_label'] }} <x-heroicon-m-arrow-right class="h-4 w-4"/></a>
        @endif
    </div>
</section>
