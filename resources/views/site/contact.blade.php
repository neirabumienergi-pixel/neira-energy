@extends('layouts.site', ['seo' => ['title' => 'Kontak', 'description' => 'Hubungi Neira Energy untuk konsultasi, survei, dan penawaran PLTS & PJU.']])

@section('content')
    <x-page-hero title="Hubungi Kami" subtitle="Tim engineer & business development kami siap membantu kebutuhan energi Anda." eyebrow="Kontak" :crumbs="['Kontak' => null]"/>
    <section class="section">
        <div class="container-x grid gap-10 lg:grid-cols-5">
            <div class="space-y-4 lg:col-span-2">
                <a href="{{ wa_link() }}" target="_blank" rel="noopener" class="flex items-center gap-4 rounded-2xl bg-[#25D366] p-5 text-white transition hover:brightness-95">
                    <x-heroicon-s-chat-bubble-left-right class="h-8 w-8"/><div><p class="font-semibold">WhatsApp</p><p class="text-sm text-white/90">{{ setting('contact.whatsapp') }}</p></div>
                </a>
                @if(setting('contact.phone'))
                    <div class="flex items-center gap-4 rounded-2xl border border-slate-200 p-5"><x-heroicon-o-phone class="h-7 w-7 text-brand-500"/><div><p class="font-semibold">Telepon</p><p class="text-sm">{{ setting('contact.phone') }}</p></div></div>
                @endif
                @if(setting('contact.email'))
                    <a href="mailto:{{ setting('contact.email') }}" class="flex items-center gap-4 rounded-2xl border border-slate-200 p-5 hover:border-brand-500"><x-heroicon-o-envelope class="h-7 w-7 text-brand-500"/><div><p class="font-semibold">Email</p><p class="text-sm">{{ setting('contact.email') }}</p></div></a>
                @endif
                @foreach((setting('contact.addresses') ?: []) as $a)
                    <div class="flex gap-4 rounded-2xl border border-slate-200 p-5"><x-heroicon-o-map-pin class="h-7 w-7 shrink-0 text-accent-500"/>
                        <div><p class="font-semibold">{{ $a['label'] ?? '' }}</p><p class="text-sm">{{ $a['address'] ?? '' }}</p>
                            @if(!empty($a['maps_url']))<a href="{{ $a['maps_url'] }}" target="_blank" rel="noopener" class="mt-1 inline-block text-sm font-medium text-brand-600 hover:underline">Buka di Maps</a>@endif</div></div>
                @endforeach
                @if(setting('contact.hours'))<p class="text-sm text-slate-500">Jam operasional: {{ setting('contact.hours') }}</p>@endif
            </div>
            <div class="lg:col-span-3" x-data="{ name: '', company: '', segment: '', need: '', msg() { return encodeURIComponent(`Halo Neira Energy,\nNama: ${this.name}\nInstansi: ${this.company}\nSegmen: ${this.segment}\nKebutuhan: ${this.need}`) } }">
                <div class="rounded-3xl border border-slate-200 p-6 sm:p-8">
                    <h2 class="text-xl font-bold">Kirim pertanyaan</h2>
                    <p class="mt-1 text-sm text-slate-500">Pesan Anda akan dikirim melalui WhatsApp ke tim kami.</p>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <input x-model="name" class="input" placeholder="Nama lengkap">
                        <input x-model="company" class="input" placeholder="Perusahaan / instansi">
                        <select x-model="segment" class="input sm:col-span-2"><option value="">Pilih segmen</option>@foreach($segments as $s)<option>{{ $s->name }}</option>@endforeach</select>
                        <textarea x-model="need" rows="5" class="input sm:col-span-2" placeholder="Ceritakan kebutuhan Anda (lokasi, tagihan listrik, kapasitas, dll)"></textarea>
                    </div>
                    <a :href="'https://wa.me/{{ preg_replace('/\D/', '', (string) setting('contact.whatsapp')) }}?text=' + msg()" target="_blank" rel="noopener" class="btn-primary mt-5">Kirim via WhatsApp</a>
                </div>
                @if(setting('contact.maps_embed'))
                    <iframe src="{{ setting('contact.maps_embed') }}" class="mt-6 h-80 w-full rounded-3xl border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                @endif
            </div>
        </div>
    </section>
@endsection
