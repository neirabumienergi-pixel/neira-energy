@props(['name' => null, 'fallback' => 'heroicon-o-bolt'])
@php
    $html = '';
    foreach (array_filter([$name, $fallback]) as $n) {
        try { $html = svg($n, $attributes->get('class', 'h-6 w-6'))->toHtml(); break; } catch (\Throwable $e) { continue; }
    }
@endphp
{!! $html !!}
