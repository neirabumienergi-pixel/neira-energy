<section class="section {{ $index % 2 ? 'bg-slate-50' : 'bg-white' }}">
    <div class="container-x">
        @if(!empty($data['heading']))<x-section-heading :title="$data['heading']" :text="$data['intro'] ?? null" center/>@endif
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-{{ min(4, max(2, count($data['items'] ?? []))) }}">
            @foreach(($data['items'] ?? []) as $item)
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="grid h-12 w-12 place-items-center rounded-xl bg-brand-50 text-brand-600"><x-icon :name="$item['icon'] ?? null" class="h-6 w-6"/></div>
                    <h3 class="mt-4 font-semibold text-slate-900">{{ $item['title'] ?? '' }}</h3>
                    @if(!empty($item['text']))<p class="mt-2 text-sm text-slate-600">{{ $item['text'] }}</p>@endif
                </div>
            @endforeach
        </div>
    </div>
</section>
