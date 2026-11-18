<div class="divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white">
    @foreach($faqs as $faq)
        <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-medium text-slate-900">
                {{ $faq->question }}
                <x-heroicon-m-chevron-down class="h-5 w-5 shrink-0 text-slate-500 transition" ::class="open && 'rotate-180'"/>
            </button>
            <div x-show="open" x-collapse class="prose prose-sm prose-slate max-w-none px-5 pb-5">{!! $faq->answer !!}</div>
        </div>
    @endforeach
</div>
