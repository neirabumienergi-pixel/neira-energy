@php($children = $context['category']?->children ?? collect())
@if($children->isNotEmpty())
<section class="section {{ $index % 2 ? 'bg-slate-50' : 'bg-white' }}">
    <div class="container-x">
        <x-section-heading :title="$data['heading'] ?? 'Pilihan Sistem'" center/>
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-{{ min(4, max(2, $children->count())) }}">
            @foreach($children as $child)
                <a href="{{ $child->url() }}" class="card group p-6">
                    <div class="grid h-12 w-12 place-items-center rounded-xl bg-accent-50 text-accent-600"><x-icon :name="$child->icon" class="h-6 w-6"/></div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900 group-hover:text-brand-600">{{ $child->name }}</h3>
                    @if($child->tagline)<p class="text-xs font-medium text-brand-600">{{ $child->tagline }}</p>@endif
                    <p class="mt-2 text-sm text-slate-600">{{ $child->short_description }}</p>
                    <span class="no-print mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-600">Lihat detail <x-heroicon-m-arrow-right class="h-4 w-4"/></span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
