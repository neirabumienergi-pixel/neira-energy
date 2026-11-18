@php($right = ($data['image_position'] ?? 'right') === 'right')
<section class="section {{ $index % 2 ? 'bg-slate-50' : 'bg-white' }}">
    <div class="container-x grid items-center gap-10 {{ !empty($data['image']) ? 'lg:grid-cols-2' : '' }}">
        <div class="{{ !empty($data['image']) && !$right ? 'lg:order-2' : '' }}">
            @if(!empty($data['heading']))<h2 class="section-title">{{ $data['heading'] }}</h2>@endif
            <div class="prose prose-slate mt-5 max-w-none prose-a:text-brand-600 prose-strong:text-slate-900">{!! $data['body'] ?? '' !!}</div>
        </div>
        @if(!empty($data['image']))
            <img src="{{ media($data['image']) }}" alt="{{ $data['heading'] ?? '' }}" class="aspect-[4/3] w-full rounded-3xl object-cover shadow-lg" loading="lazy">
        @endif
    </div>
</section>
