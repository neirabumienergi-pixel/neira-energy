@php($faqs = \App\Models\Faq::active()->ordered()->when($data['group'] ?? null, fn ($q, $g) => $q->where('group', $g))->get())
@if($faqs->isNotEmpty())
<section class="section {{ $index % 2 ? 'bg-slate-50' : 'bg-white' }}">
    <div class="container-x max-w-3xl">
        <h2 class="section-title text-center">{{ $data['heading'] ?? 'Pertanyaan Umum' }}</h2>
        <div class="mt-8">@include('partials.faq-list', ['faqs' => $faqs])</div>
    </div>
</section>
@endif
