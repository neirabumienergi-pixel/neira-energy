<section class="no-print py-14">
    <div class="container-x">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 to-brand-800 px-6 py-12 text-center text-white sm:px-12">
            <div class="absolute -right-10 -top-10 h-48 w-48 rounded-full bg-sun-400/30 blur-2xl"></div>
            <h2 class="relative text-2xl font-bold text-white sm:text-3xl">{{ $data['title'] ?? '' }}</h2>
            @if(!empty($data['text']))<p class="relative mx-auto mt-3 max-w-2xl text-brand-50">{{ $data['text'] }}</p>@endif
            <a href="{{ $data['button_url'] ?? null ?: wa_link() }}" class="btn-accent relative mt-7" @if(empty($data['button_url'])) target="_blank" rel="noopener" @endif>
                {{ $data['button_label'] ?? 'Hubungi Kami' }} <x-heroicon-m-arrow-right class="h-4 w-4"/>
            </a>
        </div>
    </div>
</section>
