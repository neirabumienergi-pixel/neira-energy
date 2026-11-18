<section class="section {{ $index % 2 ? 'bg-slate-50' : 'bg-white' }}">
    <div class="container-x">
        @if(!empty($data['heading']))<x-section-heading :title="$data['heading']" center/>@endif
        <ol class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-{{ max(2, min(5, count($data['items'] ?? []))) }}">
            @foreach(($data['items'] ?? []) as $n => $item)
                <li class="relative rounded-2xl border border-slate-200 bg-white p-6">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-accent-500 text-sm font-bold text-white">{{ $n + 1 }}</span>
                    <h3 class="mt-4 font-semibold text-slate-900">{{ $item['title'] ?? '' }}</h3>
                    @if(!empty($item['text']))<p class="mt-2 text-sm text-slate-600">{{ $item['text'] }}</p>@endif
                </li>
            @endforeach
        </ol>
    </div>
</section>
