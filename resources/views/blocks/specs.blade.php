<section class="section {{ $index % 2 ? 'bg-slate-50' : 'bg-white' }}">
    <div class="container-x max-w-4xl">
        @if(!empty($data['heading']))<h2 class="section-title text-center">{{ $data['heading'] }}</h2>@endif
        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
            <table class="w-full text-sm">
                @foreach(($data['rows'] ?? []) as $row)
                    <tr class="odd:bg-slate-50/70"><th class="w-1/3 px-5 py-3 text-left font-medium text-slate-500">{{ $row['label'] ?? '' }}</th><td class="px-5 py-3 font-medium text-slate-900">{{ $row['value'] ?? '' }}</td></tr>
                @endforeach
            </table>
        </div>
    </div>
</section>
