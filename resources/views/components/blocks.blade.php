@props(['blocks' => [], 'context' => []])
@foreach(($blocks ?? []) as $i => $block)
    @php($type = $block['type'] ?? null)
    @if($type && view()->exists('blocks.'.$type))
        @include('blocks.'.$type, ['data' => $block['data'] ?? [], 'index' => $i, 'context' => $context])
    @endif
@endforeach
