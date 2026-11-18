<section class="bg-brand-600 py-14 text-white">
    <div class="container-x">
        @if(!empty($data['heading']))<h2 class="mb-8 text-center text-2xl font-bold text-white">{{ $data['heading'] }}</h2>@endif
        <div class="grid grid-cols-2 gap-6 text-center lg:grid-cols-{{ max(2, min(4, count($data['items'] ?? []))) }}">
            @foreach(($data['items'] ?? []) as $item)
                <div><p class="text-3xl font-extrabold text-sun-400 sm:text-4xl">{{ $item['value'] ?? '' }}</p><p class="mt-1 text-sm text-brand-50">{{ $item['label'] ?? '' }}</p></div>
            @endforeach
        </div>
    </div>
</section>
