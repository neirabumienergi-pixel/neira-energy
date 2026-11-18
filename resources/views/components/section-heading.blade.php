@props(['eyebrow' => null, 'title', 'text' => null, 'center' => false])
<div {{ $attributes->class(['max-w-2xl', 'mx-auto text-center' => $center]) }}>
    {{-- $eyebrow sengaja tidak dirender: judul berdiri sendiri --}}
    <h2 class="section-title text-balance">{{ $title }}</h2>
    @if($text)<p class="mt-4 text-slate-600">{{ $text }}</p>@endif
</div>
