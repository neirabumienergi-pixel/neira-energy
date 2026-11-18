<section class="section {{ $index % 2 ? 'bg-slate-50' : 'bg-white' }}">
    <div class="container-x">
        @if(!empty($data['heading']))<x-section-heading :title="$data['heading']" center/>@endif
        <div class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-3">
            @foreach(($data['images'] ?? []) as $img)
                <img src="{{ media($img) }}" alt="" loading="lazy" class="aspect-[4/3] w-full rounded-xl object-cover">
            @endforeach
        </div>
    </div>
</section>
