/**
 * Hero interaktif beranda: pilihan segmen, mini kalkulator hemat, simulasi siang–malam.
 * Rumus kalkulator HARUS sama dengan app/Services/SolarCalculator.php
 */
const nf = new Intl.NumberFormat('id-ID');

// Bobot pemakaian listrik per jam (0–23)
const LOAD_PROFILES = {
    residential: [3, 3, 3, 3, 3, 4, 6, 7, 5, 4, 4, 4, 4, 4, 4, 4, 5, 7, 9, 10, 9, 8, 6, 4],
    business: [2, 2, 2, 2, 2, 2, 3, 5, 8, 9, 9, 9, 8, 9, 9, 9, 8, 6, 4, 3, 3, 2, 2, 2],
};

export default function solarHero({ segments = [], banners = [], calc = {}, waNumber = '', display = {} }) {
    const cfg = calc.config || {};
    const packages = calc.packages || [];
    const minLog = Math.log(cfg.min_bill || 500000);
    const maxLog = Math.log(cfg.max_bill || 100000000);
    // Mode proyek (pemerintah, komunitas, mitra EPC): input kapasitas, bukan tagihan
    const minKwpLog = Math.log(10), maxKwpLog = Math.log(5000);

    return {
        segments, banners, packages, cfg,
        seg: null,               // index segmen aktif, null = mode banner
        slide: 0, timer: null,
        tab: 'calc',
        profile: 'residential',
        billPos: 0,              // posisi slider 0..1000 (skala log)
        kwpPos: 0,               // posisi slider kapasitas proyek 0..1000 (skala log)
        billTouched: false,      // pengunjung sudah menggeser slider → jangan ditimpa
        hour: 12, playing: null,

        useVideo: true,
        isMobile: false,

        init() {
            const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            this.isMobile = window.matchMedia('(max-width: 640px)').matches;
            this.useVideo = !reduced && (!this.isMobile || !!display.video_on_mobile);
            this.setBill(3000000);
            this.setKwp(100);
            this.startSlides();
        },
        /** Interaksi pertama menghentikan rotasi banner agar konten tidak berganti di bawah jari pengunjung. */
        interact() { clearInterval(this.timer); this.timer = null; },

        // ---------- Banner & segmen ----------
        startSlides() {
            clearInterval(this.timer);
            if (this.banners.length > 1) this.timer = setInterval(() => { if (this.seg === null) this.slide = (this.slide + 1) % this.banners.length }, 6500);
        },
        pick(i) {
            this.interact();
            this.seg = this.seg === i ? null : i;
            if (this.seg === null && this.profile === 'project') this.profile = 'residential';
            if (this.seg !== null) {
                const s = this.segments[i];
                this.profile = s.profile;
                if (!this.billTouched && s.profile !== 'project') this.setBill(s.profile === 'residential' ? 3000000 : 30000000);
            }
        },
        setProfile(p) { this.interact(); this.profile = p; },
        get isProject() { return this.profile === 'project'; },
        get profileLabel() { return { residential: 'Rumah', business: 'Bisnis / Instansi', project: 'Proyek' }[this.profile]; },
        get current() { return this.seg === null ? null : this.segments[this.seg]; },
        /** Latar aktif: gambar (poster) + video opsional */
        get bg() {
            const src = this.current || this.banners[this.slide] || {};
            const image = (this.isMobile && src.image_mobile) || src.image || null;
            const video = this.useVideo ? (src.video || null) : null;
            return { key: (video || image || 'none') + (this.current ? ':s' : ':b'), image, video };
        },

        // ---------- Kalkulator ----------
        get bill() {
            const v = Math.exp(minLog + (maxLog - minLog) * this.billPos / 1000);
            const step = v < 2e6 ? 50000 : v < 2e7 ? 250000 : 1000000;
            return Math.round(v / step) * step;
        },
        setBill(v) { this.billPos = Math.round((Math.log(v) - minLog) / (maxLog - minLog) * 1000); },
        get projectKwp() {
            const v = Math.exp(minKwpLog + (maxKwpLog - minKwpLog) * this.kwpPos / 1000);
            const step = v < 100 ? 5 : v < 1000 ? 25 : 100;
            return Math.round(v / step) * step;
        },
        setKwp(v) { this.kwpPos = Math.round((Math.log(v) - minKwpLog) / (maxKwpLog - minKwpLog) * 1000); },
        get tariff() { return this.profile === 'residential' ? cfg.tariff_residential : cfg.tariff_business; },
        get yieldPerKwp() { return cfg.psh * cfg.performance_ratio * 30; },
        get result() {
            if (this.isProject) {
                const kwp = this.projectKwp;
                const production = kwp * this.yieldPerKwp;
                return {
                    kwhMonth: production / cfg.offset_business, kwp, production, saving: production * cfg.tariff_business,
                    cost: kwp * cfg.price_per_kwp, pkg: null, payback: null, co2: production * 12 * cfg.emission_factor / 1000,
                };
            }
            const bill = this.bill;
            const kwhMonth = bill / this.tariff;
            const offset = this.profile === 'residential' ? cfg.offset_residential : cfg.offset_business;
            let kwp = Math.max(1, Math.round(kwhMonth * offset / this.yieldPerKwp * 10) / 10);
            const pkg = this.recommend(bill, kwp);
            if (pkg) kwp = pkg.capacity;
            const production = kwp * this.yieldPerKwp;
            // Tanpa kompensasi ekspor (Permen ESDM 2/2024): hemat hanya dari energi yang dipakai sendiri
            const saving = Math.min(production, kwhMonth * offset) * this.tariff;
            const cost = pkg?.price ?? kwp * cfg.price_per_kwp;
            return {
                kwhMonth, kwp, production, saving, cost, pkg,
                payback: saving > 0 ? cost / (saving * 12) : null,
                co2: production * 12 * cfg.emission_factor / 1000,
            };
        },
        recommend(bill, kwp) {
            const byBill = this.packages.find(p => p.min != null && p.max != null && bill >= p.min && bill <= p.max);
            if (byBill) return byBill;
            if (!this.packages.length || kwp > Math.max(...this.packages.map(p => p.capacity)) * 1.5) return null;
            return [...this.packages].sort((a, b) => Math.abs(a.capacity - kwp) - Math.abs(b.capacity - kwp) || b.capacity - a.capacity)[0];
        },
        get paybackRange() {
            const p = this.result.payback;
            if (!p) return null;
            const lo = Math.max(1, Math.floor(p * 0.9)), hi = Math.ceil(p * 1.1);
            return lo === hi ? `${lo}` : `${lo}–${hi}`;
        },
        waLink(extra = '') {
            const r = this.result;
            const who = this.current ? ` (${this.current.name})` : '';
            const msg = this.isProject
                ? `Halo Neira Energy${who}, kami merencanakan proyek PLTS sekitar ${this.fmt(r.kwp)} kWp. Mohon diskusi kebutuhan & penawarannya.` + extra
                : `Halo Neira Energy${who}, tagihan listrik saya sekitar Rp ${nf.format(this.bill)}/bulan.`
                + (r.pkg ? ` Saya tertarik dengan ${r.pkg.name}.` : ` Estimasi kebutuhan ±${this.fmt(r.kwp, 1)} kWp.`) + ' Mohon penawarannya.' + extra;
            return `https://wa.me/${waNumber}?text=${encodeURIComponent(msg)}`;
        },

        // ---------- Simulasi ----------
        prodAt(h) {
            if (h <= 6 || h >= 18) return 0;
            return this.result.kwp * cfg.performance_ratio * cfg.psh * (Math.PI / 24) * Math.sin(Math.PI * (h - 6) / 12);
        },
        loadAt(h) {
            const w = LOAD_PROFILES[this.profile === 'residential' ? 'residential' : 'business'];
            const sum = w.reduce((a, b) => a + b, 0);
            const daily = this.result.kwhMonth / 30;
            const i = Math.floor(h) % 24, j = (i + 1) % 24, f = h - Math.floor(h);
            return daily * ((w[i] * (1 - f) + w[j] * f) / sum);
        },
        get now() {
            const p = this.prodAt(this.hour), l = this.loadAt(this.hour);
            return { prod: p, load: l, solar: Math.min(p, l), grid: Math.max(0, l - p), surplus: Math.max(0, p - l) };
        },
        get dayCoverage() {
            let solar = 0, load = 0;
            for (let h = 0; h < 24; h += 0.25) { solar += Math.min(this.prodAt(h), this.loadAt(h)); load += this.loadAt(h); }
            return load ? Math.round(solar / load * 100) : 0;
        },
        get isNight() { return this.hour < 5.5 || this.hour > 18.5; },
        get simStatus() {
            const n = this.now;
            if (n.prod <= 0.01) return 'night';
            return n.grid < 0.01 ? 'solar' : 'mixed';
        },
        get sunStyle() {
            const t = Math.min(1, Math.max(0, (this.hour - 6) / 12));
            return { left: `${(20 + t * 260) / 3}%`, top: `${(110 - Math.sin(Math.PI * t) * 90) / 1.3}%` };
        },
        get skyClass() {
            const h = this.hour;
            if (h < 5 || h >= 19.5) return 'from-slate-950 to-indigo-950';
            if (h < 7) return 'from-indigo-800 via-rose-400 to-amber-300';
            if (h < 16.5) return 'from-sky-500 to-sky-200';
            if (h < 18.5) return 'from-orange-500 via-rose-400 to-indigo-700';
            return 'from-indigo-900 to-slate-900';
        },
        chart(fn, w = 300, hgt = 90) {
            const max = Math.max(0.001, ...Array.from({ length: 97 }, (_, k) => Math.max(this.prodAt(k / 4), this.loadAt(k / 4))));
            const pts = Array.from({ length: 97 }, (_, k) => `${(k / 96 * w).toFixed(1)},${(hgt - fn(k / 4) / max * (hgt - 6)).toFixed(1)}`);
            return pts.join(' ');
        },
        togglePlay() {
            this.interact();
            if (this.playing) { clearInterval(this.playing); this.playing = null; return; }
            if (this.hour >= 23.5) this.hour = 0;
            this.playing = setInterval(() => {
                this.hour = Math.round((this.hour + 0.25) * 4) / 4;
                if (this.hour >= 24) { this.hour = 24; clearInterval(this.playing); this.playing = null; }
            }, 90);
        },
        get hourLabel() {
            const h = Math.floor(this.hour) % 24, m = Math.round((this.hour % 1) * 60);
            return `${String(h).padStart(2, '0')}.${String(m).padStart(2, '0')}`;
        },

        // ---------- Format ----------
        fmt(v, d = 0) { return new Intl.NumberFormat('id-ID', { maximumFractionDigits: d, minimumFractionDigits: 0 }).format(v || 0); },
        rp(v) { return 'Rp ' + nf.format(Math.round(v || 0)); },
        rpShort(v) {
            if (v >= 1e9) return 'Rp ' + this.fmt(v / 1e9, 2) + ' M';
            if (v >= 1e6) return 'Rp ' + this.fmt(v / 1e6, 1) + ' jt';
            return 'Rp ' + this.fmt(v / 1e3, 0) + ' rb';
        },
    };
}
