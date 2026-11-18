@props(['eyebrow' => null, 'title', 'text' => null, 'center' => false])
<div {{ $attributes->class(['max-w-2xl', 'mx-auto text-center' => $center]) }}>
    @if($eyebrow)<p class="eyebrow">{{ $eyebrow }}</p>@endif
    <h2 class="section-title mt-2">{{ $title }}</h2>
    @if($text)<p class="mt-4 text-slate-600">{{ $text }}</p>@endif
</div>
