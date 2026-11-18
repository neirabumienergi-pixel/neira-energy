@extends('layouts.site', ['seo' => ['title' => 'FAQ', 'description' => 'Pertanyaan yang sering diajukan seputar PLTS, PJU, perizinan, dan layanan Neira Energy.']])

@section('content')
    <x-page-hero title="Pertanyaan Umum" subtitle="Jawaban atas pertanyaan yang paling sering kami terima." eyebrow="FAQ" :crumbs="['FAQ' => null]"/>
    <section class="section">
        <div class="container-x max-w-3xl space-y-10">
            @forelse($groups as $group => $faqs)
                <div><h2 class="mb-4 text-xl font-bold">{{ $group }}</h2>@include('partials.faq-list', ['faqs' => $faqs])</div>
            @empty
                <p class="text-slate-500">Belum ada FAQ.</p>
            @endforelse
            <div class="rounded-2xl bg-brand-50 p-6 text-center">
                <p class="font-semibold text-slate-900">Tidak menemukan jawaban?</p>
                <a href="{{ wa_link() }}" target="_blank" rel="noopener" class="btn-primary mt-3">Tanya langsung via WhatsApp</a>
            </div>
        </div>
    </section>
@endsection
