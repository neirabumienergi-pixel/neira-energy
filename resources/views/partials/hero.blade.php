{{-- Hero interaktif: banner / segmen (kiri) + kalkulator & simulasi (kanan). Logika: resources/js/hero.js --}}
<section x-data="solarHero(@js($hero))" class="relative overflow-hidden bg-brand-950 pb-24">
    {{-- Latar --}}
    <template x-for="img in [bgImage]" :key="img">
        <img :src="img" x-show="img" alt="" class="absolute inset-0 h-full w-full object-cover"
             x-transition:enter="transition duration-700" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
    </template>
    <div class="absolute inset-0 bg-gradient-to-r from-brand-950 via-brand-950/85 to-brand-950/40"></div>
    <div class="absolute -right-32 top-10 h-96 w-96 rounded-full bg-accent-500/20 blur-3xl"></div>

    <div class="container-x relative grid items-center gap-10 pt-10 lg:min-h-[78vh] lg:grid-cols-12 lg:pt-14">
        {{-- Kiri --}}
        <div class="lg:col-span-7">
            @if(count($hero['segments']))
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Saya adalah</p>
                <div class="-mx-4 mt-3 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] sm:mx-0 sm:flex-wrap sm:overflow-visible sm:px-0">
                    <template x-for="(s, i) in segments" :key="s.slug">
                        <button type="button" @click="pick(i)"
                                :class="seg === i ? 'bg-accent-500 text-white border-accent-500 shadow-lg shadow-accent-500/30' : 'border-white/25 bg-white/5 text-white/85 hover:bg-white/15'"
                                class="inline-flex shrink-0 items-center gap-2 rounded-full border px-4 py-2 text-sm font-medium backdrop-blur transition">
                            <span x-html="s.icon" class="h-4 w-4 [&>svg]:h-4 [&>svg]:w-4"></span><span x-text="s.name"></span>
                        </button>
                    </template>
                </div>
            @endif

            {{-- Konten banner (default) --}}
            <div x-show="seg === null" class="mt-8 min-h-[300px]">
                @forelse($banners as $k => $b)
                    <div x-show="slide === {{ $k }}" @if($k) x-cloak @endif
                         x-transition:enter="transition duration-500" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                        @if($b->subtitle)<p class="eyebrow">{{ $b->subtitle }}</p>@endif
                        <h1 class="mt-3 text-[2rem] font-bold leading-tight text-white sm:text-5xl">{{ $b->title }}</h1>
                        @if($b->description)<p class="mt-5 max-w-2xl text-lg text-white/80">{{ $b->description }}</p>@endif
                        <div class="mt-8 flex flex-wrap gap-3">
                            @if($b->cta_label)<a href="{{ $b->cta_url ?: wa_link() }}" class="btn-accent px-6 py-3">{{ $b->cta_label }} <x-heroicon-m-arrow-right class="h-4 w-4"/></a>@endif
                            @if($b->cta2_label)<a href="{{ $b->cta2_url ?: '#' }}" class="btn-ghost-light px-6 py-3">{{ $b->cta2_label }}</a>@endif
                        </div>
                    </div>
                @empty
                    <h1 class="text-[2rem] font-bold leading-tight text-white sm:text-5xl">{{ setting('company.tagline', 'Solusi energi surya end-to-end') }}</h1>
                @endforelse
                @if($banners->count() > 1)
                    <div class="mt-10 flex gap-2">
                        @foreach($banners as $k => $b)
                            <button @click="slide = {{ $k }}; startSlides()" :class="slide === {{ $k }} ? 'w-8 bg-accent-500' : 'w-2.5 bg-white/40'" class="h-2.5 rounded-full transition-all" aria-label="Slide {{ $k + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Konten segmen --}}
            <template x-if="current">
                <div class="mt-8 min-h-[300px]" x-transition:enter="transition duration-500" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <p class="eyebrow" x-text="current.tagline"></p>
                    <h1 class="mt-3 text-[2rem] font-bold leading-tight text-white sm:text-5xl" x-text="current.title"></h1>
                    <p class="mt-5 max-w-2xl text-lg text-white/80" x-text="current.text"></p>
                    <ul class="mt-6 grid gap-2 sm:grid-cols-2" x-show="current.points.length">
                        <template x-for="pt in current.points"><li class="flex items-start gap-2 text-sm text-white/90"><x-heroicon-s-check-circle class="mt-0.5 h-5 w-5 shrink-0 text-sun-400"/><span x-text="pt"></span></li></template>
                    </ul>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a :href="current.cta_url || waLink()" target="_blank" rel="noopener" class="btn-accent px-6 py-3"><span x-text="current.cta_label"></span> <x-heroicon-m-arrow-right class="h-4 w-4"/></a>
                        <a :href="current.url" class="btn-ghost-light px-6 py-3">Lihat solusi</a>
                    </div>
                </div>
            </template>
        </div>

        {{-- Kanan: kartu interaktif --}}
        <div class="lg:col-span-5">
            <div class="overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-black/5">
                <div class="grid grid-cols-2 border-b border-slate-100 text-sm font-semibold">
                    <button @click="tab = 'calc'" :class="tab === 'calc' ? 'text-brand-600 border-brand-500' : 'text-slate-400 border-transparent hover:text-slate-600'" class="flex items-center justify-center gap-2 border-b-2 py-3.5 transition">
                        <x-heroicon-o-calculator class="h-5 w-5"/> Hitung Hemat
                    </button>
                    <button @click="tab = 'sim'" :class="tab === 'sim' ? 'text-brand-600 border-brand-500' : 'text-slate-400 border-transparent hover:text-slate-600'" class="flex items-center justify-center gap-2 border-b-2 py-3.5 transition">
                        <x-heroicon-o-sun class="h-5 w-5"/> Simulasi Surya
                    </button>
                </div>

                <div class="p-5 sm:p-6">
                    {{-- Profil --}}
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs font-medium text-slate-500">Jenis pemakaian</p>
                        <div class="inline-flex rounded-full bg-slate-100 p-1 text-xs font-semibold">
                            <button @click="profile = 'residential'" :class="profile === 'residential' ? 'bg-white text-brand-600 shadow' : 'text-slate-500'" class="rounded-full px-3 py-1.5 transition">Rumah</button>
                            <button @click="profile = 'business'" :class="profile === 'business' ? 'bg-white text-brand-600 shadow' : 'text-slate-500'" class="rounded-full px-3 py-1.5 transition">Bisnis / Instansi</button>
                        </div>
                    </div>

                    {{-- Slider tagihan --}}
                    <div class="mt-5">
                        <div class="flex items-end justify-between">
                            <label for="bill" class="text-sm font-medium text-slate-700">Tagihan listrik / bulan</label>
                            <p class="text-xl font-bold text-slate-900" x-text="rp(bill)"></p>
                        </div>
                        <input id="bill" type="range" min="0" max="1000" x-model.number="billPos"
                               class="mt-3 h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-200 accent-brand-500">
                        <div class="mt-1 flex justify-between text-[11px] text-slate-400"><span x-text="rpShort(cfg.min_bill)"></span><span x-text="rpShort(cfg.max_bill)"></span></div>
                    </div>

                    {{-- TAB: Kalkulator --}}
                    <div x-show="tab === 'calc'">
                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-brand-50 p-3.5">
                                <p class="text-[11px] font-medium text-brand-700/70">Kapasitas disarankan</p>
                                <p class="mt-0.5 text-xl font-bold text-brand-700"><span x-text="fmt(result.kwp, 1)"></span> <span class="text-sm">kWp</span></p>
                            </div>
                            <div class="rounded-2xl bg-accent-50 p-3.5">
                                <p class="text-[11px] font-medium text-accent-700/70">Hemat / bulan</p>
                                <p class="mt-0.5 text-xl font-bold text-accent-700" x-text="rpShort(result.saving)"></p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-3.5">
                                <p class="text-[11px] font-medium text-slate-500">Balik modal</p>
                                <p class="mt-0.5 text-xl font-bold text-slate-900"><span x-text="result.payback ? fmt(result.payback, 1) : '-'"></span> <span class="text-sm">tahun</span></p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-3.5">
                                <p class="text-[11px] font-medium text-slate-500">CO₂ dihindari</p>
                                <p class="mt-0.5 text-xl font-bold text-slate-900"><span x-text="fmt(result.co2, 1)"></span> <span class="text-sm">ton/thn</span></p>
                            </div>
                        </div>

                        <template x-if="result.pkg">
                            <a :href="result.pkg.url" class="mt-4 flex items-center gap-3 rounded-2xl border border-slate-200 p-3 transition hover:border-brand-500">
                                <img :src="result.pkg.image" x-show="result.pkg.image" alt="" class="h-14 w-14 shrink-0 rounded-xl object-cover">
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wider text-accent-600">Paket rekomendasi</p>
                                    <p class="truncate font-semibold text-slate-900" x-text="result.pkg.name"></p>
                                    <p class="text-sm text-slate-500" x-text="result.pkg.price ? rp(result.pkg.price) : 'Hubungi kami'"></p>
                                </div>
                                <x-heroicon-m-chevron-right class="h-5 w-5 shrink-0 text-slate-400"/>
                            </a>
                        </template>
                        <template x-if="!result.pkg">
                            <p class="mt-4 rounded-2xl border border-dashed border-slate-300 p-3 text-sm text-slate-600">
                                Kebutuhan ±<b x-text="fmt(result.kwp, 1)"></b> kWp — di atas paket standar. Estimasi investasi <b x-text="rpShort(result.cost)"></b>; tim kami siapkan desain khusus.
                            </p>
                        </template>
                    </div>

                    {{-- TAB: Simulasi --}}
                    <div x-show="tab === 'sim'" x-cloak>
                        <div class="relative mt-5 h-36 overflow-hidden rounded-2xl bg-gradient-to-b transition-colors duration-700" :class="skyClass">
                            <svg viewBox="0 0 300 130" class="absolute inset-0 h-full w-full" preserveAspectRatio="none">
                                <path d="M20 110 Q150 -70 280 110" fill="none" stroke="white" stroke-opacity=".25" stroke-dasharray="3 4"/>
                            </svg>
                            {{-- Matahari / bulan --}}
                            <div class="absolute h-9 w-9 -translate-x-1/2 -translate-y-1/2 transition-all duration-100"
                                 :style="`left:${sunPos.x / 3}%; top:${sunPos.y / 1.3}%`" x-show="!isNight">
                                <div class="h-full w-full rounded-full bg-sun-400 shadow-[0_0_30px_10px_rgba(249,216,74,.6)]"></div>
                            </div>
                            <div x-show="isNight" class="absolute right-8 top-5 h-8 w-8 rounded-full bg-slate-100 shadow-[inset_-8px_-4px_0_0_rgba(100,116,139,.6)]"></div>
                            <template x-if="isNight"><div class="absolute inset-0">
                                <span class="absolute left-[15%] top-[20%] h-1 w-1 rounded-full bg-white/80"></span><span class="absolute left-[40%] top-[12%] h-0.5 w-0.5 rounded-full bg-white"></span>
                                <span class="absolute left-[62%] top-[30%] h-1 w-1 rounded-full bg-white/70"></span><span class="absolute left-[28%] top-[42%] h-0.5 w-0.5 rounded-full bg-white"></span>
                            </div></template>
                            {{-- Rumah + panel --}}
                            <div class="absolute bottom-0 left-1/2 -translate-x-1/2">
                                <svg width="150" height="62" viewBox="0 0 150 62">
                                    <polygon points="15,30 75,4 135,30" fill="#334155"/>
                                    <polygon points="38,24 75,8 112,24" :fill="now.prod > 0.01 ? '#1d4ed8' : '#1e293b'" stroke="#93c5fd" stroke-width=".8"/>
                                    <line x1="56" y1="16" x2="56" y2="24" stroke="#93c5fd" stroke-width=".6"/><line x1="75" y1="8" x2="75" y2="24" stroke="#93c5fd" stroke-width=".6"/><line x1="94" y1="16" x2="94" y2="24" stroke="#93c5fd" stroke-width=".6"/>
                                    <rect x="25" y="30" width="100" height="32" fill="#f8fafc"/>
                                    <rect x="40" y="38" width="16" height="12" :fill="now.load > 0.01 && isNight ? '#fde68a' : '#cbd5e1'"/>
                                    <rect x="94" y="38" width="16" height="12" :fill="now.load > 0.01 && isNight ? '#fde68a' : '#cbd5e1'"/>
                                    <rect x="67" y="42" width="16" height="20" fill="#0e9447"/>
                                </svg>
                            </div>
                            <div class="absolute left-3 top-3 rounded-full bg-black/30 px-2.5 py-1 font-mono text-xs font-semibold text-white backdrop-blur" x-text="hourLabel"></div>
                        </div>

                        <div class="mt-3 flex items-center gap-3">
                            <button @click="togglePlay()" class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand-500 text-white hover:bg-brand-600" :aria-label="playing ? 'Jeda' : 'Putar'">
                                <x-heroicon-s-play x-show="!playing" class="h-4 w-4"/><x-heroicon-s-pause x-show="playing" x-cloak class="h-4 w-4"/>
                            </button>
                            <input type="range" min="0" max="24" step="0.25" x-model.number="hour" class="h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-200 accent-accent-500" aria-label="Jam">
                        </div>

                        <svg viewBox="0 0 300 90" class="mt-3 h-20 w-full" preserveAspectRatio="none">
                            <polygon :points="'0,90 ' + chart(h => prodAt(h)) + ' 300,90'" fill="#f9d84a" fill-opacity=".45"/>
                            <polyline :points="chart(h => prodAt(h))" fill="none" stroke="#f7941d" stroke-width="1.5"/>
                            <polyline :points="chart(h => loadAt(h))" fill="none" stroke="#0e9447" stroke-width="1.5" stroke-dasharray="4 2"/>
                            <line :x1="hour / 24 * 300" :x2="hour / 24 * 300" y1="0" y2="90" stroke="#0f172a" stroke-width="1" stroke-opacity=".5"/>
                        </svg>
                        <div class="flex justify-between text-[10px] text-slate-400"><span>00</span><span>06</span><span>12</span><span>18</span><span>24</span></div>

                        <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                            <div class="rounded-xl bg-amber-50 p-2"><p class="text-[10px] text-amber-700">Produksi PLTS</p><p class="font-bold text-amber-700"><span x-text="fmt(now.prod, 1)"></span> kW</p></div>
                            <div class="rounded-xl bg-brand-50 p-2"><p class="text-[10px] text-brand-700">Pemakaian</p><p class="font-bold text-brand-700"><span x-text="fmt(now.load, 1)"></span> kW</p></div>
                            <div class="rounded-xl bg-slate-100 p-2"><p class="text-[10px] text-slate-500">Dari PLN</p><p class="font-bold text-slate-800"><span x-text="fmt(now.grid, 1)"></span> kW</p></div>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">
                            <span x-show="now.prod > 0.01 && now.grid < 0.01">☀️ Saat ini kebutuhan listrik sepenuhnya dari panel surya.</span>
                            <span x-show="now.prod > 0.01 && now.grid >= 0.01">⚡ Panel surya menanggung sebagian, sisanya dari PLN.</span>
                            <span x-show="now.prod <= 0.01">🌙 Malam hari: listrik dari PLN — atau baterai pada sistem hybrid.</span>
                            Rata-rata PLTS <b x-text="fmt(result.kwp, 1)"></b> kWp memenuhi <b x-text="dayCoverage + '%'"></b> kebutuhan harian.
                        </p>
                    </div>

                    <a :href="waLink()" target="_blank" rel="noopener" class="btn-primary mt-5 w-full py-3">Minta penawaran gratis</a>
                    <p class="mt-2 text-center text-[10px] leading-snug text-slate-400">Estimasi berdasarkan asumsi rata-rata. Angka final setelah survei lokasi.</p>
                </div>
            </div>
        </div>
    </div>
</section>
