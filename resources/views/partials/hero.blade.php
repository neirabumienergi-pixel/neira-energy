{{-- Hero interaktif: banner / segmen (kiri) + kalkulator & simulasi (kanan). Logika: resources/js/hero.js --}}
@php($waIcon = '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.42.25-.7.25-1.29.17-1.42-.07-.12-.27-.2-.57-.35M12.05 21.5a9.4 9.4 0 0 1-4.8-1.32l-.34-.2-3.57.94.95-3.48-.22-.36a9.4 9.4 0 0 1-1.44-5.01c0-5.2 4.23-9.44 9.44-9.44a9.44 9.44 0 0 1 9.43 9.45c0 5.2-4.24 9.43-9.44 9.43m8.03-17.46A11.27 11.27 0 0 0 12.04.72C5.79.72.7 5.8.7 12.06c0 2 .52 3.95 1.52 5.67L.6 23.6l6.01-1.58a11.3 11.3 0 0 0 5.43 1.38c6.25 0 11.34-5.08 11.34-11.34 0-3.03-1.18-5.88-3.32-8.02"/></svg>')
<section x-data="solarHero(@js($hero))" class="relative overflow-hidden bg-brand-950 pb-24">
    {{-- Latar: gambar / video --}}
    <template x-for="b in [bg]" :key="b.key">
        <div class="absolute inset-0" x-transition:enter="transition duration-700" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <img :src="b.image" x-show="b.image" alt="" class="absolute inset-0 h-full w-full object-cover">
            <template x-if="b.video">
                <video :src="b.video" :poster="b.image" autoplay muted loop playsinline preload="auto" aria-hidden="true"
                       x-init="$el.muted = true; $el.play().catch(() => {})" class="absolute inset-0 h-full w-full object-cover"></video>
            </template>
        </div>
    </template>
    @php($overlay = ['light' => 'from-brand-950/85 via-brand-950/55 to-brand-950/10', 'medium' => 'from-brand-950 via-brand-950/85 to-brand-950/40', 'dark' => 'from-brand-950 via-brand-950/95 to-brand-950/75'][setting('hero.overlay', 'medium')] ?? 'from-brand-950 via-brand-950/85 to-brand-950/40')
    <div class="absolute inset-0 bg-gradient-to-r {{ $overlay }}"></div>
    @if($hero['display']['motion'])
        @include('partials.hero-motion')
    @endif

    <div class="container-x relative grid grid-cols-1 gap-10 pt-10 lg:min-h-[78vh] lg:grid-cols-12 lg:items-center lg:pt-14">
        {{-- Kiri --}}
        <div class="min-w-0 lg:col-span-7">
            @if(count($hero['segments']))
                <p id="seg-label" class="text-sm font-medium text-white/75">Saya adalah</p>
                <div role="group" aria-labelledby="seg-label"
                     class="-mx-4 mt-3 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] sm:mx-0 sm:flex-wrap sm:overflow-visible sm:px-0">
                    <template x-for="(s, i) in segments" :key="s.slug">
                        <button type="button" @click="pick(i)" :aria-pressed="seg === i"
                                :class="seg === i ? 'bg-sun-400 text-brand-950 border-sun-400' : 'border-white/30 bg-brand-950/40 text-white hover:border-white/60'"
                                class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-full border px-4 text-sm font-medium backdrop-blur transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sun-400">
                            <span x-html="s.icon" class="h-4 w-4 [&>svg]:h-4 [&>svg]:w-4"></span><span x-text="s.name"></span>
                        </button>
                    </template>
                </div>
            @endif

            {{-- Konten banner (default) --}}
            <div x-show="seg === null" class="mt-8">
                @forelse($banners as $k => $b)
                    <div x-show="slide === {{ $k }}" @if($k) x-cloak @endif
                         x-transition:enter="transition duration-500 ease-out" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                        @if($k === 0)
                            <h1 class="max-w-[18ch] text-[2.1rem] font-bold leading-[1.1] tracking-[-0.02em] text-balance text-white sm:text-5xl lg:text-[3.4rem]">{{ $b->title }}</h1>
                        @else
                            <p class="max-w-[18ch] text-[2.1rem] font-bold leading-[1.1] tracking-[-0.02em] text-balance text-white sm:text-5xl lg:text-[3.4rem]">{{ $b->title }}</p>
                        @endif
                        @if($b->description)<p class="mt-5 max-w-xl text-lg leading-relaxed text-white/85">{{ $b->description }}</p>@endif
                        <div class="mt-8 flex flex-wrap gap-3">
                            @if($b->cta_label)<a href="{{ $b->cta_url ?: '#kalkulator' }}" class="btn-accent px-6 py-3">{{ $b->cta_label }} <x-heroicon-m-arrow-right class="h-4 w-4"/></a>@endif
                            @if($b->cta2_label)<a href="{{ $b->cta2_url ?: '#' }}" class="btn-ghost-light px-6 py-3">{{ $b->cta2_label }}</a>@endif
                        </div>
                    </div>
                @empty
                    <h1 class="max-w-[18ch] text-[2.1rem] font-bold leading-[1.1] tracking-[-0.02em] text-white sm:text-5xl">{{ setting('company.tagline', 'Hemat listrik dengan energi surya') }}</h1>
                @endforelse
                @if($banners->count() > 1)
                    <div class="mt-8 flex gap-1" aria-label="Pilih slide">
                        @foreach($banners as $k => $b)
                            <button @click="slide = {{ $k }}; interact()" class="group p-2" :aria-current="slide === {{ $k }}" aria-label="Slide {{ $k + 1 }}">
                                <span :class="slide === {{ $k }} ? 'w-8 bg-sun-400' : 'w-2.5 bg-white/50 group-hover:bg-white/80'" class="block h-2.5 rounded-full transition-all"></span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Konten segmen --}}
            <template x-if="current">
                <div class="mt-8" x-transition:enter="transition duration-500 ease-out" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <p class="max-w-[20ch] text-[2.1rem] font-bold leading-[1.1] tracking-[-0.02em] text-balance text-white sm:text-5xl" x-text="current.title"></p>
                    <p class="mt-5 max-w-xl text-lg leading-relaxed text-white/85" x-text="current.text"></p>
                    <ul class="mt-6 grid max-w-xl gap-2.5 sm:grid-cols-2" x-show="current.points.length">
                        <template x-for="pt in current.points"><li class="flex items-start gap-2 text-sm text-white/90"><x-heroicon-s-check-circle class="mt-0.5 h-5 w-5 shrink-0 text-sun-400"/><span x-text="pt"></span></li></template>
                    </ul>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <template x-if="current.cta_url"><a :href="current.cta_url" class="btn-accent px-6 py-3"><span x-text="current.cta_label"></span> <x-heroicon-m-arrow-right class="h-4 w-4"/></a></template>
                        <a :href="current.url" class="btn-ghost-light px-6 py-3" x-text="'Lihat solusi ' + current.name"></a>
                    </div>
                </div>
            </template>
        </div>

        {{-- Kanan: kartu interaktif --}}
        <div id="kalkulator" class="min-w-0 scroll-mt-24 lg:col-span-5">
            <div class="overflow-hidden rounded-2xl bg-white shadow-[0_24px_60px_-20px_rgba(4,36,20,.55)]">
                <div role="tablist" aria-label="Alat hitung" class="grid grid-cols-2 border-b border-slate-200 text-sm font-semibold">
                    <button role="tab" id="tab-calc" aria-controls="pane-calc" :aria-selected="tab === 'calc'" @click="tab = 'calc'; interact()"
                            :class="tab === 'calc' ? 'text-brand-700 border-brand-600' : 'text-slate-500 border-transparent hover:text-slate-800'"
                            class="flex min-h-12 items-center justify-center gap-2 border-b-2 transition">
                        <x-heroicon-o-calculator class="h-5 w-5"/> Hitung Hemat
                    </button>
                    <button role="tab" id="tab-sim" aria-controls="pane-sim" :aria-selected="tab === 'sim'" @click="tab = 'sim'; interact()"
                            :class="tab === 'sim' ? 'text-brand-700 border-brand-600' : 'text-slate-500 border-transparent hover:text-slate-800'"
                            class="flex min-h-12 items-center justify-center gap-2 border-b-2 transition">
                        <x-heroicon-o-sun class="h-5 w-5"/> Simulasi Surya
                    </button>
                </div>

                <div class="p-5 sm:p-6">
                    {{-- Profil: dari segmen bila dipilih; toggle hanya bila belum --}}
                    <div class="flex min-h-9 items-center justify-between gap-3">
                        <template x-if="current">
                            <p class="text-sm text-slate-600">Dihitung untuk <b class="font-semibold text-slate-900" x-text="current.name"></b>
                                <button type="button" @click="pick(seg)" class="ml-1 font-medium text-brand-700 underline underline-offset-2 hover:text-brand-800">ubah</button></p>
                        </template>
                        <template x-if="!current">
                            <div class="flex w-full items-center justify-between gap-3">
                                <span id="profile-label" class="text-sm text-slate-600">Jenis pemakaian</span>
                                <div role="group" aria-labelledby="profile-label" class="inline-flex rounded-full bg-slate-100 p-1 text-xs font-semibold">
                                    <button type="button" @click="setProfile('residential')" :aria-pressed="profile === 'residential'" :class="profile === 'residential' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-600'" class="min-h-8 rounded-full px-3.5 transition">Rumah</button>
                                    <button type="button" @click="setProfile('business')" :aria-pressed="profile === 'business'" :class="profile === 'business' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-600'" class="min-h-8 rounded-full px-3.5 transition">Bisnis / Instansi</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Input: tagihan (rumah/bisnis) atau kapasitas (proyek) --}}
                    <div class="mt-4" x-show="!isProject">
                        <div class="flex items-end justify-between gap-3">
                            <label for="bill" class="text-sm font-medium text-slate-700">Tagihan listrik / bulan</label>
                            <p class="text-xl font-bold tabular-nums text-slate-900" x-text="rp(bill)"></p>
                        </div>
                        <input id="bill" type="range" min="0" max="1000" x-model.number="billPos" @input="billTouched = true; interact()" :aria-valuetext="rp(bill)"
                               class="mt-3 h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-200 accent-brand-600">
                        <div class="mt-1.5 flex justify-between text-xs text-slate-500"><span x-text="rpShort(cfg.min_bill)"></span><span x-text="rpShort(cfg.max_bill)"></span></div>
                    </div>
                    <div class="mt-4" x-show="isProject" x-cloak>
                        <div class="flex items-end justify-between gap-3">
                            <label for="kwp" class="text-sm font-medium text-slate-700">Kapasitas proyek</label>
                            <p class="text-xl font-bold tabular-nums text-slate-900"><span x-text="fmt(projectKwp)"></span> kWp</p>
                        </div>
                        <input id="kwp" type="range" min="0" max="1000" x-model.number="kwpPos" @input="interact()" :aria-valuetext="fmt(projectKwp) + ' kWp'"
                               class="mt-3 h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-200 accent-brand-600">
                        <div class="mt-1.5 flex justify-between text-xs text-slate-500"><span>10 kWp</span><span>5 MWp</span></div>
                    </div>

                    {{-- Panel ditumpuk di sel grid yang sama agar tinggi kartu tidak melompat saat ganti tab --}}
                    <div class="mt-5 grid">
                        {{-- TAB: Kalkulator --}}
                        <div id="pane-calc" role="tabpanel" aria-labelledby="tab-calc" :aria-hidden="tab !== 'calc'" :class="tab === 'calc' ? '' : 'invisible'" class="col-start-1 row-start-1">
                            <dl class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-brand-50 p-3.5">
                                    <dt class="text-xs font-medium text-brand-800" x-text="isProject ? 'Kapasitas' : 'Kapasitas disarankan'"></dt>
                                    <dd class="mt-0.5 text-xl font-bold tabular-nums text-brand-800"><span x-text="fmt(result.kwp, 1)"></span> <abbr title="kilowatt-peak: kapasitas puncak panel surya" class="cursor-help text-sm no-underline">kWp</abbr></dd>
                                </div>
                                <div class="rounded-xl bg-accent-50 p-3.5">
                                    <dt class="text-xs font-medium text-accent-800" x-text="isProject ? 'Produksi / bulan' : 'Hemat / bulan'"></dt>
                                    <dd class="mt-0.5 text-xl font-bold tabular-nums text-accent-800" x-text="isProject ? '±' + fmt(result.production) + ' kWh' : rpShort(result.saving)"></dd>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3.5">
                                    <dt class="text-xs font-medium text-slate-600" x-text="isProject ? 'Estimasi investasi' : 'Balik modal'"></dt>
                                    <dd class="mt-0.5 text-xl font-bold tabular-nums text-slate-900">
                                        <span x-show="isProject" x-text="rpShort(result.cost)"></span>
                                        <span x-show="!isProject"><span x-text="paybackRange || '-'"></span> <span class="text-sm">tahun</span></span>
                                    </dd>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3.5">
                                    <dt class="text-xs font-medium text-slate-600">CO₂ dihindari</dt>
                                    <dd class="mt-0.5 text-xl font-bold tabular-nums text-slate-900"><span x-text="fmt(result.co2, 1)"></span> <span class="text-sm">ton/tahun</span></dd>
                                </div>
                            </dl>

                            <template x-if="result.pkg">
                                <a :href="result.pkg.url" class="mt-4 flex items-center gap-3 rounded-xl border border-slate-200 p-3 transition hover:border-brand-600">
                                    <img :src="result.pkg.image" x-show="result.pkg.image" alt="" class="h-14 w-14 shrink-0 rounded-lg object-cover">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-accent-800">Paket yang cocok</p>
                                        <p class="truncate font-semibold text-slate-900" x-text="result.pkg.name"></p>
                                        <p class="text-sm tabular-nums text-slate-600" x-text="result.pkg.price ? rp(result.pkg.price) : 'Hubungi kami'"></p>
                                    </div>
                                    <x-heroicon-m-chevron-right class="h-5 w-5 shrink-0 text-slate-500"/>
                                </a>
                            </template>
                            <template x-if="!result.pkg && !isProject">
                                <p class="mt-4 rounded-xl border border-dashed border-slate-300 p-3 text-sm text-slate-600">
                                    Kebutuhan ±<b x-text="fmt(result.kwp, 1)"></b> kWp, di atas paket standar. Estimasi investasi <b x-text="rpShort(result.cost)"></b>; tim kami siapkan desain khusus.
                                </p>
                            </template>
                            <template x-if="isProject">
                                <p class="mt-4 rounded-xl border border-dashed border-slate-300 p-3 text-sm text-slate-600">
                                    Estimasi awal berdasarkan harga per kWp. RAB final mengikuti desain, lokasi, dan spesifikasi proyek.
                                </p>
                            </template>
                            <ul class="mt-3 flex flex-wrap gap-x-4 gap-y-1.5 text-xs text-slate-600">
                                <li class="flex items-center gap-1.5"><x-heroicon-s-check-circle class="h-4 w-4 text-brand-600"/>Survei & desain gratis</li>
                                <li class="flex items-center gap-1.5"><x-heroicon-s-check-circle class="h-4 w-4 text-brand-600"/>Urus izin PLN, NIDI & SLO</li>
                                <li class="flex items-center gap-1.5" x-show="result.pkg?.warranty"><x-heroicon-s-check-circle class="h-4 w-4 text-brand-600"/><span x-text="'Garansi ' + (result.pkg?.warranty || '')"></span></li>
                            </ul>
                        </div>

                        {{-- TAB: Simulasi --}}
                        <div id="pane-sim" role="tabpanel" aria-labelledby="tab-sim" :aria-hidden="tab !== 'sim'" :class="tab === 'sim' ? '' : 'invisible'" class="col-start-1 row-start-1">
                            <div class="relative h-28 overflow-hidden rounded-xl bg-gradient-to-b transition-colors duration-700" :class="skyClass">
                                <svg viewBox="0 0 300 130" class="absolute inset-0 h-full w-full" preserveAspectRatio="none" aria-hidden="true">
                                    <path d="M20 110 Q150 -70 280 110" fill="none" stroke="white" stroke-opacity=".25" stroke-dasharray="3 4"/>
                                </svg>
                                <div x-show="!isNight" class="absolute inset-0">
                                    <div class="absolute h-8 w-8 -translate-x-1/2 -translate-y-1/2 rounded-full bg-sun-400 shadow-[0_0_30px_10px_rgba(249,216,74,.55)]" :style="sunStyle"></div>
                                </div>
                                <div x-show="isNight" class="absolute inset-0">
                                    <div class="absolute right-8 top-5 h-7 w-7 rounded-full bg-slate-100 shadow-[inset_-7px_-3px_0_0_rgba(100,116,139,.6)]"></div>
                                    <span class="absolute left-[15%] top-[20%] h-1 w-1 rounded-full bg-white/80"></span><span class="absolute left-[40%] top-[12%] h-0.5 w-0.5 rounded-full bg-white"></span>
                                    <span class="absolute left-[62%] top-[30%] h-1 w-1 rounded-full bg-white/70"></span><span class="absolute left-[28%] top-[42%] h-0.5 w-0.5 rounded-full bg-white"></span>
                                </div>
                                <div class="absolute bottom-0 left-1/2 -translate-x-1/2">
                                    <svg width="150" height="62" viewBox="0 0 150 62" aria-hidden="true">
                                        <polygon points="15,30 75,4 135,30" fill="#334155"/>
                                        <polygon points="38,24 75,8 112,24" :fill="now.prod > 0.01 ? '#1d4ed8' : '#1e293b'" stroke="#93c5fd" stroke-width=".8"/>
                                        <line x1="56" y1="16" x2="56" y2="24" stroke="#93c5fd" stroke-width=".6"/><line x1="75" y1="8" x2="75" y2="24" stroke="#93c5fd" stroke-width=".6"/><line x1="94" y1="16" x2="94" y2="24" stroke="#93c5fd" stroke-width=".6"/>
                                        <rect x="25" y="30" width="100" height="32" fill="#f8fafc"/>
                                        <rect x="40" y="38" width="16" height="12" :fill="isNight ? '#fde68a' : '#cbd5e1'"/>
                                        <rect x="94" y="38" width="16" height="12" :fill="isNight ? '#fde68a' : '#cbd5e1'"/>
                                        <rect x="67" y="42" width="16" height="20" fill="#0b7a3a"/>
                                    </svg>
                                </div>
                                <div class="absolute left-3 top-3 rounded-full bg-black/35 px-2.5 py-1 text-xs font-semibold tabular-nums text-white backdrop-blur" x-text="'Pukul ' + hourLabel"></div>
                            </div>

                            <div class="mt-3 flex items-center gap-3">
                                <button type="button" @click="togglePlay()" class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-600 text-white hover:bg-brand-700" :aria-label="playing ? 'Jeda simulasi' : 'Putar simulasi'">
                                    <x-heroicon-s-play x-show="!playing" class="h-4 w-4"/><x-heroicon-s-pause x-show="playing" x-cloak class="h-4 w-4"/>
                                </button>
                                <input type="range" min="0" max="24" step="0.25" x-model.number="hour" @input="interact()" :aria-valuetext="'Pukul ' + hourLabel"
                                       class="h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-200 accent-accent-600" aria-label="Jam simulasi">
                            </div>

                            <svg viewBox="0 0 300 70" class="mt-3 h-14 w-full" preserveAspectRatio="none" role="img" aria-label="Grafik produksi panel dan pemakaian listrik sepanjang hari">
                                <polygon :points="'0,70 ' + chart(h => prodAt(h), 300, 70) + ' 300,70'" fill="#f9d84a" fill-opacity=".45"/>
                                <polyline :points="chart(h => prodAt(h), 300, 70)" fill="none" stroke="#e07a0b" stroke-width="1.5"/>
                                <polyline :points="chart(h => loadAt(h), 300, 70)" fill="none" stroke="#0b7a3a" stroke-width="1.5" stroke-dasharray="4 2"/>
                                <line :x1="hour / 24 * 300" :x2="hour / 24 * 300" y1="0" y2="70" stroke="#0f172a" stroke-width="1" stroke-opacity=".5"/>
                            </svg>
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <div class="flex gap-3">
                                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-4 rounded-sm bg-sun-400/70 ring-1 ring-accent-600"></span>Produksi panel</span>
                                    <span class="flex items-center gap-1.5"><span class="w-4 border-t-2 border-dashed border-brand-700"></span>Pemakaian</span>
                                </div>
                                <span class="tabular-nums">00 — 24</span>
                            </div>

                            <dl class="mt-3 grid grid-cols-3 divide-x divide-slate-200 rounded-lg bg-slate-50 py-2 text-center">
                                <div><dt class="text-xs text-slate-600">Produksi</dt><dd class="font-bold tabular-nums text-accent-800"><span x-text="fmt(now.prod, 1)"></span> kW</dd></div>
                                <div><dt class="text-xs text-slate-600">Pemakaian</dt><dd class="font-bold tabular-nums text-brand-800"><span x-text="fmt(now.load, 1)"></span> kW</dd></div>
                                <div><dt class="text-xs text-slate-600">Dari PLN</dt><dd class="font-bold tabular-nums text-slate-900"><span x-text="fmt(now.grid, 1)"></span> kW</dd></div>
                            </dl>
                            <p class="mt-3 flex gap-2 text-xs leading-relaxed text-slate-600">
                                <x-heroicon-s-sun x-show="simStatus !== 'night'" class="h-4 w-4 shrink-0 text-accent-600"/>
                                <x-heroicon-s-moon x-show="simStatus === 'night'" class="h-4 w-4 shrink-0 text-indigo-700"/>
                                <span>
                                    <span x-show="simStatus === 'solar'">Kebutuhan listrik saat ini sepenuhnya dari panel surya.</span>
                                    <span x-show="simStatus === 'mixed'">Panel surya menanggung sebagian, sisanya dari PLN.</span>
                                    <span x-show="simStatus === 'night'">Malam hari: listrik dari PLN, atau baterai pada sistem hybrid.</span>
                                    PLTS <b x-text="fmt(result.kwp, 1)"></b> kWp memenuhi ±<b x-text="dayCoverage + '%'"></b> kebutuhan harian.
                                </span>
                            </p>
                        </div>
                    </div>

                    <a :href="waLink()" target="_blank" rel="noopener" class="btn-primary mt-5 w-full py-3">{!! $waIcon !!} Minta penawaran via WhatsApp</a>
                    <details class="group mt-3 text-xs text-slate-600">
                        <summary class="flex cursor-pointer list-none items-center justify-center gap-1 font-medium text-slate-600 hover:text-slate-900">
                            Bagaimana angka ini dihitung? <x-heroicon-m-chevron-down class="h-4 w-4 transition group-open:rotate-180"/>
                        </summary>
                        <ul class="mt-2 space-y-1 rounded-lg bg-slate-50 p-3 leading-relaxed">
                            <li>Tarif listrik <span class="tabular-nums" x-text="rp(tariff)"></span>/kWh, matahari efektif <span x-text="fmt(cfg.psh, 1)"></span> jam/hari, efisiensi sistem <span x-text="fmt(cfg.performance_ratio * 100)"></span>%.</li>
                            <li>Hemat hanya dihitung dari listrik yang dipakai sendiri. Kelebihan ke PLN tidak dikompensasi (Permen ESDM No. 2/2024).</li>
                            <li>Estimasi rata-rata, bukan penawaran. Angka final setelah survei lokasi.</li>
                        </ul>
                    </details>
                </div>
            </div>
        </div>
    </div>
</section>
