<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Certification;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\Portfolio;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\ProductCategory;
use App\Models\ProductPackage;
use App\Models\Promo;
use App\Models\Segment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Data awal dari Company Profile Neira Energy 2026.
 * Harga paket adalah CONTOH — wajib disesuaikan di panel admin sebelum go-live.
 */
class NeiraSeeder extends Seeder
{
    protected function asset(string $name): ?string
    {
        $src = database_path("seeders/assets/{$name}.jpg");
        if (! is_file($src)) {
            return null;
        }
        $path = "seed/{$name}.jpg";
        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, file_get_contents($src));
        }

        return $path;
    }

    public function run(): void
    {
        User::updateOrCreate(['email' => env('ADMIN_EMAIL') ?: 'admin@neiraenergy.com'], [
            'name' => 'Super Admin',
            'password' => env('ADMIN_PASSWORD') ?: 'password',
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->settings();
        $segments = $this->segments();
        $cats = $this->categories();
        $this->packages($cats);
        $this->portfolios($segments, $cats);
        $this->content();
    }

    protected function settings(): void
    {
        Setting::put('company', [
            'name' => 'Neira Energy',
            'legal_name' => 'PT Neira Bumi Energi',
            'tagline' => 'Building Indonesia’s Integrated Renewable Energy Ecosystem',
            'slogan' => 'More than solar — we brighten your future',
            'short_about' => 'Neira Energy adalah perusahaan energi terbarukan terintegrasi — EPC, manpower, energy trading, dan platform digital — yang mendukung percepatan transisi energi Indonesia.',
            'logo' => null, 'logo_white' => null, 'favicon' => null,
            'badges' => ['Member of AESI', 'IUJPTL Certified', 'SBU PLTS', 'Top 10 EPC – Solar Academy Indonesia 2025'],
        ]);
        Setting::put('contact', [
            'phone' => '0811 1380 5499',
            'whatsapp' => '6281113805499',
            'email' => 'bd@neirasolar.com',
            'hours' => 'Senin – Jumat, 08.00 – 17.00 WIB',
            'wa_message' => 'Halo Neira Energy, saya ingin konsultasi mengenai energi surya.',
            'addresses' => [
                ['label' => 'Kantor', 'address' => 'Jl. Sapphire Commercial, Jl. Bulevar Summarecon Emerald Blok BB-22, Kondangjaya, Kec. Karawang Timur, Karawang, Jawa Barat 41314', 'maps_url' => null],
                ['label' => 'Workshop', 'address' => 'Jl. Melati No. 64 RT 01/09, Ds. Wanaherang, Kec. Gunung Putri, Bogor, Jawa Barat', 'maps_url' => null],
            ],
            'maps_embed' => null,
        ]);
        Setting::put('social', ['instagram' => null, 'facebook' => null, 'linkedin' => null, 'youtube' => null, 'tiktok' => null, 'energy_mart' => null]);
        Setting::put('about', [
            'heading' => 'Membangun Ekosistem Energi Bersih Indonesia',
            'image' => $this->asset('about'),
            'team_image' => $this->asset('team'),
            'body' => '<p><strong>Neira Energy</strong> adalah perusahaan energi terbarukan terintegrasi yang membangun ekosistem energi nasional untuk mendukung percepatan transisi energi Indonesia menuju masa depan yang bersih, mandiri, dan berkelanjutan.</p><p>Melalui integrasi infrastruktur pembangkit, layanan engineering, tenaga profesional, rantai pasok energi, serta platform digital, Neira Energy hadir sebagai mitra strategis pemerintah dan industri dalam mewujudkan kemandirian energi nasional.</p><p>Neira Energy menjalankan seluruh aktivitas bisnis berdasarkan prinsip kepatuhan regulasi, tata kelola perusahaan yang baik, serta standar keselamatan dan mutu internasional.</p>',
            'vision' => 'Menjadi penggerak utama ekosistem energi terbarukan terintegrasi di Indonesia.',
            'missions' => [
                'Membangun infrastruktur energi terbarukan skala nasional',
                'Mengembangkan solusi energi berbasis teknologi dan integrasi ekosistem',
                'Mendukung percepatan transisi energi nasional',
                'Mengembangkan talenta energi bersih Indonesia',
                'Membangun kemitraan strategis jangka panjang',
            ],
            'why' => [
                ['icon' => 'heroicon-o-arrow-path-rounded-square', 'title' => 'End-to-End Energy Ecosystem', 'text' => 'Pengelolaan proyek dari perencanaan hingga operasional dalam satu ekosistem terintegrasi.'],
                ['icon' => 'heroicon-o-user-group', 'title' => 'Tim Profesional Berpengalaman', 'text' => 'Didukung tenaga ahli dengan pengalaman proyek energi nasional.'],
                ['icon' => 'heroicon-o-shield-check', 'title' => 'Standar HSE Tinggi', 'text' => 'Keselamatan sebagai prioritas utama dalam setiap proyek.'],
                ['icon' => 'heroicon-o-globe-asia-australia', 'title' => 'Fokus Sustainability & ESG', 'text' => 'Komitmen terhadap transisi energi dan pembangunan berkelanjutan.'],
            ],
            'esg' => [
                ['title' => 'Environmental', 'text' => 'Mendukung pengurangan karbon melalui pemanfaatan energi bersih.'],
                ['title' => 'Social', 'text' => 'Mengembangkan tenaga kerja hijau yang terampil dan memberdayakan komunitas lokal.'],
                ['title' => 'Governance', 'text' => 'Menjalankan praktik bisnis yang bertanggung jawab, transparan, dan etis.'],
            ],
        ]);
        Setting::put('units', [
            ['name' => 'Neira Solar', 'label' => 'EPC & Engineering PLTS', 'logo' => null, 'url' => null,
                'description' => 'Layanan Engineering, Procurement, and Construction (EPC) PLTS untuk sektor komersial, industri, dan utilitas — dari perencanaan, desain, konstruksi, hingga commissioning.',
                'services' => ['EPC PLTS Rooftop & Ground Mounted', 'Desain dan engineering sistem PLTS', 'Pengadaan peralatan dan konstruksi', 'Testing, commissioning, dan handover']],
            ['name' => 'Adisurya', 'label' => 'Manpower Supply Specialist', 'logo' => null, 'url' => null,
                'description' => 'Pusat standarisasi, sertifikasi, dan pengelolaan tenaga profesional sektor energi — memastikan setiap personel memenuhi standar kompetensi, keselamatan, dan kinerja proyek.',
                'services' => ['Penyediaan tenaga teknis dan engineering', 'Manpower supply untuk proyek EPC dan O&M', 'Rekrutmen, seleksi, dan mobilisasi tenaga kerja', 'Training, standarisasi, dan sertifikasi tenaga energi', 'Manajemen HSE dan operasional lapangan']],
            ['name' => 'Energy Mart', 'label' => 'Energy Trading & Distribution', 'logo' => null, 'url' => null,
                'description' => 'Unit bisnis ritel dan distribusi yang menyediakan akses ke produk energi terbarukan berkualitas tinggi untuk bisnis dan pengguna akhir.',
                'services' => ['Distribusi solar panel dan inverter', 'Balance of System (BOS)', 'Battery Energy Storage System (BESS/ESS)', 'Logistik dan pergudangan', 'Workshop pabrikasi untuk custom specification']],
            ['name' => 'Neira Connect', 'label' => 'Digital Energy Platform', 'logo' => null, 'url' => null,
                'description' => 'Platform digital yang mengintegrasikan perencanaan proyek, monitoring konstruksi, manajemen aset, analitik performa, dan insight komersial dalam satu sistem. (Dalam pengembangan)',
                'services' => ['One Data – One Platform – One Decisions']],
        ]);
        Setting::put('stats', [
            ['value' => '10+', 'unit' => 'MWp', 'label' => 'Kapasitas terpasang dalam 2 tahun'],
            ['value' => '30+', 'unit' => 'Kota', 'label' => 'Lokasi proyek di Indonesia'],
            ['value' => '50+', 'unit' => '', 'label' => 'Tim profesional'],
            ['value' => '~10.000', 'unit' => 't', 'label' => 'Emisi CO₂ dihindari*'],
        ]);
        Setting::put('seo', [
            'meta_title' => 'Neira Energy — EPC PLTS, PJU Tenaga Surya & Solusi Energi Terbarukan',
            'meta_description' => 'Neira Energy (PT Neira Bumi Energi): EPC PLTS On Grid, Off Grid, Hybrid, PJU tenaga surya, fabrikasi, dan perizinan PLTS (NIDI & SLO) di seluruh Indonesia.',
            'og_image' => $this->asset('hero'),
            'keywords' => 'PLTS, EPC PLTS, panel surya, PJU tenaga surya, solar panel Indonesia',
        ]);
        Setting::put('hero', ['motion' => true, 'video_on_mobile' => false, 'overlay' => 'medium']);
        Setting::put('calculator', \App\Services\SolarCalculator::DEFAULTS);
        Setting::put('tracking', ['ga4' => null, 'gtm' => null, 'meta_pixel' => null]);
    }

    protected function segments(): array
    {
        $hero = [
            'Hunian' => ['Tagihan listrik rumah turun, nilai rumah naik', 'Paket PLTS rumah siap pasang lengkap dengan pengurusan izin PLN — tanpa repot.', ['Paket mulai 3 kWp', 'Termasuk NIDI & SLO', 'Monitoring via aplikasi', 'Garansi hingga 25 tahun'], 'residential', 'karawang-164'],
            'Bisnis & Industri' => ['Pangkas biaya listrik operasional & capai target ESG', 'PLTS atap dan ground-mounted untuk pabrik, gudang, gedung, dan kantor — dirancang untuk payback tercepat.', ['Desain & simulasi produksi', 'Skema EPC atau kemitraan', 'Laporan reduksi CO₂', 'O&M berkala'], 'business', 'cikarang-504'],
            'Proyek Pemerintah' => ['Mitra terpercaya untuk proyek energi pemerintah', 'Legalitas lengkap — SBU PLTS, IUJPTL, member AESI — untuk PLTS dan PJU tenaga surya instansi.', ['SBU PLTS & IUJPTL', 'PJU tenaga surya', 'Dokumen tender lengkap', 'Tim lapangan bersertifikat'], 'business', 'cibitung-2000'],
            'Komunitas' => ['Energi bersih untuk desa, sekolah & nelayan', 'Solusi surya untuk program pemberdayaan: listrik desa, sekolah, puskesmas, pompa air, hingga perahu nelayan.', ['Off grid & hybrid', 'PLTS kapal nelayan', 'Pompa air tenaga surya', 'Pelatihan operator lokal'], 'business', 'kapal-2'],
            'Kemitraan EPC' => ['Partner andal untuk developer & EPC', 'Supply material, fabrikasi, manpower bersertifikat, hingga engineering — skalakan proyek Anda bersama kami.', ['Material Tier-1 via Energy Mart', 'Fabrikasi custom', 'Manpower Adisurya', 'Dukungan engineering'], 'business', 'medan-4004'],
        ];
        $rows = [
            ['Hunian', 'heroicon-o-home-modern', 'Untuk rumah & perumahan', 'Paket PLTS rumah tangga siap pasang — hemat tagihan listrik, termasuk pengurusan perizinan PLN.', ['Rumah tinggal', 'Villa', 'Perumahan']],
            ['Bisnis & Industri', 'heroicon-o-building-office-2', 'Untuk pabrik, gedung, kantor', 'PLTS atap dan ground-mounted untuk menekan biaya listrik operasional dan mencapai target ESG perusahaan.', ['Pabrik', 'Gedung komersial', 'Kantor', 'Gudang', 'Hotel']],
            ['Proyek Pemerintah', 'heroicon-o-building-library', 'Untuk instansi & kontraktor pemerintah', 'PLTS dan PJU tenaga surya untuk instansi, BUMN/BUMD, dan kontraktor proyek pemerintah dengan legalitas lengkap.', ['Kementerian / Dinas', 'Pemda', 'BUMN / BUMD', 'Kontraktor pemerintah']],
            ['Komunitas', 'heroicon-o-users', 'Untuk NGO, desa, sekolah, kampus', 'Solusi energi surya untuk program pemberdayaan: listrik desa, sekolah, fasilitas kesehatan, perahu nelayan, dan pompa air.', ['NGO', 'Desa', 'Sekolah', 'Kampus', 'Pesantren']],
            ['Kemitraan EPC', 'heroicon-o-puzzle-piece', 'Untuk developer, EPC, dan kontraktor', 'Dukungan engineering, supply material, fabrikasi, dan manpower untuk proyek mitra EPC dan developer.', ['Developer', 'EPC', 'Kontraktor MEP', 'Installer']],
        ];
        $out = [];
        foreach ($rows as $i => [$name, $icon, $tagline, $desc, $examples]) {
            [$ht, $hx, $hp, $profile, $img] = $hero[$name];
            $out[$name] = Segment::updateOrCreate(['name' => $name], [
                'icon' => $icon, 'tagline' => $tagline, 'short_description' => $desc, 'examples' => $examples, 'sort' => $i, 'is_active' => true,
                'hero_title' => $ht, 'hero_text' => $hx, 'hero_points' => $hp, 'calc_profile' => $profile,
                'hero_image' => $this->asset($img), 'image' => $this->asset($img), 'show_in_hero' => true,
            ]);
        }

        return $out;
    }

    protected function categories(): array
    {
        $make = fn (array $attrs) => ProductCategory::updateOrCreate(['name' => $attrs['name'], 'parent_id' => $attrs['parent_id'] ?? null], $attrs + ['is_active' => true]);

        $plts = $make([
            'name' => 'PLTS', 'tagline' => 'Pembangkit Listrik Tenaga Surya', 'icon' => 'heroicon-o-sun', 'sort' => 1,
            'image' => $this->asset('cibitung-2000'),
            'short_description' => 'Sistem PLTS On Grid, Off Grid, dan Hybrid untuk rumah, bisnis, industri, hingga skala utilitas — lengkap dengan desain, instalasi, dan perizinan.',
            'brochure' => [
                ['type' => 'hero', 'data' => ['eyebrow' => 'Pembangkit Listrik Tenaga Surya', 'title' => 'Listrik hemat & bersih dari atap Anda', 'text' => 'Desain, pengadaan, instalasi, commissioning, hingga perizinan NIDI & SLO — semua dalam satu mitra.', 'image' => $this->asset('cikarang-504'), 'cta_label' => 'Konsultasi gratis', 'cta_url' => null]],
                ['type' => 'subcategories', 'data' => ['heading' => 'Pilih sistem yang tepat']],
                ['type' => 'features', 'data' => ['heading' => 'Keunggulan PLTS Neira', 'intro' => null, 'items' => [
                    ['icon' => 'heroicon-o-banknotes', 'title' => 'Hemat tagihan listrik', 'text' => 'Kurangi biaya listrik bulanan dengan energi gratis dari matahari.'],
                    ['icon' => 'heroicon-o-wrench-screwdriver', 'title' => 'Engineering terstandar', 'text' => 'Desain oleh tim engineer berpengalaman dengan simulasi produksi.'],
                    ['icon' => 'heroicon-o-document-check', 'title' => 'Perizinan lengkap', 'text' => 'Tim khusus perizinan PLTS: kuota IUPTLU, NIDI & SLO.'],
                    ['icon' => 'heroicon-o-shield-check', 'title' => 'Garansi & O&M', 'text' => 'Garansi produk & pekerjaan, serta layanan perawatan berkala.'],
                ]]],
                ['type' => 'packages', 'data' => ['heading' => 'Paket PLTS', 'intro' => 'Harga paket sudah termasuk material utama, instalasi, dan commissioning.', 'show_price' => '1']],
                ['type' => 'steps', 'data' => ['heading' => 'Alur pekerjaan', 'items' => [
                    ['title' => 'Konsultasi & survei', 'text' => 'Analisis tagihan listrik dan survei lokasi.'],
                    ['title' => 'Desain & penawaran', 'text' => 'Simulasi produksi, desain layout, dan proposal.'],
                    ['title' => 'Instalasi', 'text' => 'Pengadaan material dan pemasangan oleh tim bersertifikat.'],
                    ['title' => 'Perizinan & commissioning', 'text' => 'Pengujian, NIDI/SLO, dan serah terima.'],
                    ['title' => 'Monitoring & O&M', 'text' => 'Pemantauan performa dan perawatan berkala.'],
                ]]],
                ['type' => 'faq', 'data' => ['heading' => 'Pertanyaan seputar PLTS', 'group' => 'PLTS']],
                ['type' => 'cta', 'data' => ['title' => 'Hitung potensi penghematan Anda', 'text' => 'Kirimkan tagihan listrik terakhir Anda, tim kami akan menyiapkan simulasi gratis.', 'button_label' => 'Kirim via WhatsApp', 'button_url' => null]],
            ],
        ]);
        $ongrid = $make(['name' => 'PLTS On Grid', 'parent_id' => $plts->id, 'tagline' => 'Terhubung jaringan PLN', 'icon' => 'heroicon-o-bolt', 'sort' => 1, 'has_packages' => true, 'capacity_unit' => 'kWp', 'image' => $this->asset('mojokerto-207'),
            'short_description' => 'Sistem PLTS yang terhubung dengan jaringan PLN untuk menekan tagihan listrik siang hari. Pilihan paling ekonomis dengan payback tercepat.']);
        $offgrid = $make(['name' => 'PLTS Off Grid', 'parent_id' => $plts->id, 'tagline' => 'Mandiri dengan baterai', 'icon' => 'heroicon-o-battery-100', 'sort' => 2, 'has_packages' => true, 'capacity_unit' => 'kWp', 'image' => $this->asset('labuanbajo-17'),
            'short_description' => 'Sistem PLTS mandiri dengan baterai untuk lokasi tanpa jaringan PLN — pulau, desa terpencil, tambak, dan site remote.']);
        $hybrid = $make(['name' => 'PLTS Hybrid', 'parent_id' => $plts->id, 'tagline' => 'PLN + baterai cadangan', 'icon' => 'heroicon-o-arrows-right-left', 'sort' => 3, 'has_packages' => true, 'capacity_unit' => 'kWp', 'image' => $this->asset('karawang-45'),
            'short_description' => 'Kombinasi on grid dan baterai: hemat tagihan sekaligus tetap menyala saat listrik PLN padam.']);

        $pju = $make([
            'name' => 'PJU Tenaga Surya', 'tagline' => 'Penerangan Jalan Umum', 'icon' => 'heroicon-o-light-bulb', 'sort' => 2,
            'image' => $this->asset('sunset'),
            'short_description' => 'PJU tenaga surya All-in-One, 2in1, 3in1, dan konvensional untuk jalan desa, kawasan industri, perumahan, dan proyek pemerintah.',
            'brochure' => [
                ['type' => 'subcategories', 'data' => ['heading' => 'Tipe PJU Tenaga Surya']],
                ['type' => 'features', 'data' => ['heading' => 'Kenapa PJU Tenaga Surya?', 'items' => [
                    ['icon' => 'heroicon-o-bolt-slash', 'title' => 'Tanpa biaya listrik', 'text' => 'Tidak membutuhkan jaringan PLN dan tagihan bulanan.'],
                    ['icon' => 'heroicon-o-clock', 'title' => 'Otomatis', 'text' => 'Menyala otomatis saat gelap dengan sensor & timer.'],
                    ['icon' => 'heroicon-o-wrench', 'title' => 'Instalasi cepat', 'text' => 'Tanpa galian kabel, cocok untuk lokasi terpencil.'],
                    ['icon' => 'heroicon-o-cog-6-tooth', 'title' => 'Tiang fabrikasi sendiri', 'text' => 'Tiang PJU diproduksi di workshop Neira sesuai spesifikasi.'],
                ]]],
                ['type' => 'packages', 'data' => ['heading' => 'Paket PJU', 'intro' => 'Harga per titik, termasuk tiang dan instalasi standar.', 'show_price' => '1']],
                ['type' => 'faq', 'data' => ['heading' => 'Pertanyaan seputar PJU', 'group' => 'PJU']],
                ['type' => 'cta', 'data' => ['title' => 'Butuh PJU untuk proyek Anda?', 'text' => 'Kirimkan panjang jalan & jarak antar tiang, kami hitungkan kebutuhannya.', 'button_label' => 'Minta penawaran']],
            ],
        ]);
        $pjuChildren = [];
        foreach ([
            ['PJU All-in-One (AIO)', 'Panel, baterai, dan lampu dalam satu unit kompak.'],
            ['PJU 2in1', 'Panel surya terpisah, baterai terintegrasi pada lampu.'],
            ['PJU 3in1', 'Panel, baterai, dan lampu terpisah untuk kapasitas lebih besar.'],
            ['PJU Konvensional', 'Sistem split dengan box baterai dan controller — andal untuk jalan utama.'],
        ] as $i => [$n, $d]) {
            $pjuChildren[$n] = $make(['name' => $n, 'parent_id' => $pju->id, 'icon' => 'heroicon-o-light-bulb', 'sort' => $i, 'has_packages' => true, 'capacity_unit' => 'Watt', 'short_description' => $d]);
        }

        $terapan = $make([
            'name' => 'Solusi Surya Terapan', 'tagline' => 'Aplikasi produktif tenaga surya', 'icon' => 'heroicon-o-sparkles', 'sort' => 3,
            'image' => $this->asset('kapal-2'),
            'short_description' => 'Pompa air, cold storage, perahu, gerobak, dan aplikasi produktif lain bertenaga surya untuk UMKM, nelayan, petani, dan komunitas.',
            'brochure' => [
                ['type' => 'subcategories', 'data' => ['heading' => 'Pilihan solusi']],
                ['type' => 'gallery', 'data' => ['heading' => 'PLTS Kapal Nelayan — Labuan Bajo (19 kapal)', 'images' => array_filter([$this->asset('kapal-1'), $this->asset('kapal-2'), $this->asset('kapal-3')])]],
                ['type' => 'cta', 'data' => ['title' => 'Punya kebutuhan khusus?', 'text' => 'Kami merancang solusi surya sesuai kebutuhan program Anda.', 'button_label' => 'Diskusikan kebutuhan']],
            ],
        ]);
        foreach (['Pompa Air Tenaga Surya' => 'heroicon-o-beaker', 'Cold Storage Tenaga Surya' => 'heroicon-o-cube-transparent', 'Perahu Tenaga Surya' => 'heroicon-o-lifebuoy', 'Gerobak Tenaga Surya' => 'heroicon-o-shopping-cart', 'Solusi Lainnya' => 'heroicon-o-ellipsis-horizontal-circle'] as $n => $icon) {
            $make(['name' => $n, 'parent_id' => $terapan->id, 'icon' => $icon, 'sort' => 0, 'short_description' => 'Solusi '.strtolower($n).' — dirancang sesuai kebutuhan lapangan.']);
        }

        $fab = $make([
            'name' => 'Fabrikasi Custom', 'tagline' => 'Workshop fabrikasi', 'icon' => 'heroicon-o-wrench-screwdriver', 'sort' => 4,
            'image' => $this->asset('install-1'),
            'short_description' => 'Fabrikasi struktur pendukung PLTS dan PJU di workshop sendiri: access ladder, shelter inverter, walkway, power house, tiang PJU, dan lainnya.',
            'brochure' => [
                ['type' => 'subcategories', 'data' => ['heading' => 'Produk fabrikasi']],
                ['type' => 'cta', 'data' => ['title' => 'Kirim gambar kerja Anda', 'text' => 'Kami produksi sesuai spesifikasi proyek.', 'button_label' => 'Minta penawaran fabrikasi']],
            ],
        ]);
        foreach (['Access Ladder', 'Shelter Inverter', 'Walkway', 'Power House', 'Tiang PJU', 'Fabrikasi Lainnya'] as $i => $n) {
            $make(['name' => $n, 'parent_id' => $fab->id, 'icon' => 'heroicon-o-cog-6-tooth', 'sort' => $i, 'short_description' => $n.' custom sesuai spesifikasi proyek.']);
        }

        $make(['name' => 'Retail – Energy Mart', 'tagline' => 'Belanja produk energi', 'icon' => 'heroicon-o-shopping-bag', 'sort' => 5,
            'short_description' => 'Panel surya, inverter, baterai, dan Balance of System — tersedia di Energy Mart.',
            'external_url' => null]);

        return ['ongrid' => $ongrid, 'offgrid' => $offgrid, 'hybrid' => $hybrid, 'pju' => $pjuChildren, 'plts' => $plts];
    }

    protected function packages(array $cats): void
    {
        $pkg = function (ProductCategory $cat, string $name, float $cap, string $unit, ?float $price, array $extra = []) {
            return ProductPackage::updateOrCreate(['name' => $name], array_merge([
                'product_category_id' => $cat->id, 'capacity' => $cap, 'capacity_unit' => $unit, 'base_price' => $price,
                'price_note' => 'Harga contoh, belum termasuk PPN 11%', 'is_active' => true,
            ], $extra));
        };

        $scope = ['Survei lokasi', 'Desain & engineering', 'Material utama', 'Instalasi', 'Testing & commissioning', 'Training pengguna'];
        foreach ([[3, 1, 42e6], [5, 1, 65e6], [10, 3, 120e6], [20, 3, 225e6], [50, 3, 520e6]] as $i => [$kwp, $ph, $price]) {
            $prod = round($kwp * 4.0 * 0.8 * 30);
            $pkg($cats['ongrid'], "PLTS On Grid {$kwp} kWp", $kwp, 'kWp', $price, [
                'sku' => 'ONG-'.str_pad($kwp, 3, '0', STR_PAD_LEFT), 'phase' => "{$ph} Phase", 'image' => $this->asset(['karawang-164', 'demak-64', 'boyolali-200', 'karawang-45', 'cikarang-508'][$i]),
                'short_description' => "Sistem PLTS on grid {$kwp} kWp untuk ".($kwp <= 5 ? 'rumah tinggal' : 'bisnis & komersial').'.',
                'est_production_kwh_month' => $prod, 'area_required_m2' => $kwp * 6,
                'min_monthly_bill' => null, 'max_monthly_bill' => null, // kosong = rekomendasi otomatis berdasarkan kapasitas
                'warranty' => 'Panel 12 th produk / 25 th performa, Inverter 5 th',
                'is_featured' => in_array($kwp, [5, 20]),
                'specifications' => [
                    ['group' => 'Panel Surya', 'label' => 'Tipe', 'value' => 'Monocrystalline N-Type Bifacial'],
                    ['group' => 'Panel Surya', 'label' => 'Daya per modul', 'value' => '580 – 620 Wp'],
                    ['group' => 'Panel Surya', 'label' => 'Jumlah modul', 'value' => (string) ceil($kwp * 1000 / 600).' pcs'],
                    ['group' => 'Inverter', 'label' => 'Tipe', 'value' => 'On Grid String Inverter'],
                    ['group' => 'Inverter', 'label' => 'Kapasitas', 'value' => $kwp.' kW, '.$ph.' Phase'],
                    ['group' => 'Sistem', 'label' => 'Monitoring', 'value' => 'Aplikasi WiFi / 4G'],
                ],
                'components' => [
                    ['name' => 'Panel surya', 'brand' => 'Tier-1', 'qty' => ceil($kwp * 1000 / 600), 'unit' => 'pcs'],
                    ['name' => 'Inverter on grid', 'brand' => 'Tier-1', 'qty' => 1, 'unit' => 'unit'],
                    ['name' => 'Mounting aluminium', 'brand' => '-', 'qty' => 1, 'unit' => 'set'],
                    ['name' => 'Kabel DC/AC, MC4, proteksi (MCB, SPD)', 'brand' => '-', 'qty' => 1, 'unit' => 'lot'],
                ],
                'scope_of_work' => $scope,
            ]);
        }

        foreach ([[1, 5, 38e6], [3, 10, 95e6], [5, 15, 150e6]] as [$kwp, $bat, $price]) {
            $pkg($cats['offgrid'], "PLTS Off Grid {$kwp} kWp", $kwp, 'kWp', $price, [
                'sku' => 'OFG-'.str_pad($kwp, 3, '0', STR_PAD_LEFT), 'image' => $this->asset('labuanbajo-17'), 'phase' => '1 Phase', 'battery_kwh' => $bat,
                'short_description' => "Sistem mandiri {$kwp} kWp dengan baterai LiFePO4 {$bat} kWh untuk lokasi tanpa PLN.",
                'est_production_kwh_month' => round($kwp * 4.0 * 0.75 * 30), 'area_required_m2' => $kwp * 6,
                'warranty' => 'Panel 12 th, Inverter 3–5 th, Baterai 5 th', 'is_featured' => $kwp === 3,
                'specifications' => [
                    ['group' => 'Panel Surya', 'label' => 'Tipe', 'value' => 'Monocrystalline'],
                    ['group' => 'Baterai', 'label' => 'Tipe', 'value' => 'LiFePO4'],
                    ['group' => 'Baterai', 'label' => 'Kapasitas', 'value' => $bat.' kWh'],
                    ['group' => 'Inverter', 'label' => 'Tipe', 'value' => 'Off Grid Inverter + MPPT'],
                ],
                'scope_of_work' => $scope,
            ]);
        }

        foreach ([[3, 5, 75e6], [5, 10, 125e6], [10, 15, 215e6]] as [$kwp, $bat, $price]) {
            $pkg($cats['hybrid'], "PLTS Hybrid {$kwp} kWp", $kwp, 'kWp', $price, [
                'sku' => 'HYB-'.str_pad($kwp, 3, '0', STR_PAD_LEFT), 'image' => $this->asset('rooftop-1'), 'phase' => $kwp >= 10 ? '3 Phase' : '1 Phase', 'battery_kwh' => $bat,
                'short_description' => "PLTS hybrid {$kwp} kWp + baterai {$bat} kWh: hemat tagihan & backup saat listrik padam.",
                'est_production_kwh_month' => round($kwp * 4.0 * 0.8 * 30), 'area_required_m2' => $kwp * 6,
                'warranty' => 'Panel 12 th, Inverter 5 th, Baterai 5 th',
                'specifications' => [
                    ['group' => 'Inverter', 'label' => 'Tipe', 'value' => 'Hybrid Inverter'],
                    ['group' => 'Baterai', 'label' => 'Tipe', 'value' => 'LiFePO4'],
                    ['group' => 'Baterai', 'label' => 'Kapasitas', 'value' => $bat.' kWh'],
                ],
                'scope_of_work' => $scope,
            ]);
        }

        $pjuData = [
            'PJU All-in-One (AIO)' => [[40, 4.5e6], [60, 6e6], [100, 8.5e6]],
            'PJU 2in1' => [[60, 9e6], [80, 11e6]],
            'PJU 3in1' => [[60, 12e6], [100, 15.5e6]],
            'PJU Konvensional' => [[100, 18e6], [150, 23e6]],
        ];
        foreach ($pjuData as $catName => $rows) {
            foreach ($rows as [$w, $price]) {
                $pkg($cats['pju'][$catName], "{$catName} {$w}W", $w, 'Watt', $price, [
                    'sku' => 'PJU-'.strtoupper(substr(preg_replace('/\W/', '', $catName), 3, 3)).'-'.$w,
                    'short_description' => "Lampu LED {$w}W tenaga surya, nyala 12 jam/malam, termasuk tiang 7 m.",
                    'price_note' => 'Harga contoh per titik, belum termasuk PPN 11%',
                    'warranty' => 'Lampu 2 th, Baterai 2 th',
                    'specifications' => [
                        ['group' => 'Lampu', 'label' => 'Daya LED', 'value' => $w.' W'],
                        ['group' => 'Lampu', 'label' => 'Durasi nyala', 'value' => '12 jam / malam'],
                        ['group' => 'Baterai', 'label' => 'Tipe', 'value' => 'LiFePO4'],
                        ['group' => 'Tiang', 'label' => 'Tinggi', 'value' => '7 m (opsi 6–9 m)'],
                    ],
                ]);
            }
        }
    }

    protected function portfolios(array $segments, array $cats): void
    {
        $rows = [
            ['PLTS Off Grid Labuan Bajo', 'Labuan Bajo', 'Nusa Tenggara Timur', 17, 'Off Grid', 'labuanbajo-17', 'Komunitas', 'offgrid', -8.4964, 119.8877],
            ['PLTS On Grid Mojokerto', 'Mojokerto', 'Jawa Timur', 207, 'On Grid', 'mojokerto-207', 'Bisnis & Industri', 'ongrid', -7.4722, 112.4338],
            ['PLTS On Grid Berau', 'Berau', 'Kalimantan Timur', 64, 'On Grid', 'berau-64', 'Bisnis & Industri', 'ongrid', 2.1527, 117.4965],
            ['PLTS On Grid Karawang 164 kWp', 'Karawang', 'Jawa Barat', 164, 'On Grid', 'karawang-164', 'Bisnis & Industri', 'ongrid', -6.3227, 107.3376],
            ['PLTS On Grid Karawang 45 kWp', 'Karawang', 'Jawa Barat', 45, 'On Grid', 'karawang-45', 'Bisnis & Industri', 'ongrid', -6.3050, 107.3000],
            ['PLTS On Grid Cikarang 504 kWp', 'Cikarang', 'Jawa Barat', 504, 'On Grid', 'cikarang-504', 'Bisnis & Industri', 'ongrid', -6.2615, 107.1528],
            ['PLTS On Grid Medan 4 MWp', 'Medan', 'Sumatera Utara', 4004, 'On Grid', 'medan-4004', 'Kemitraan EPC', 'ongrid', 3.5952, 98.6722],
            ['PLTS Tasikmalaya', 'Tasikmalaya', 'Jawa Barat', null, 'On Grid', 'tasikmalaya', 'Proyek Pemerintah', 'ongrid', -7.3274, 108.2207],
            ['PLTS Semarang', 'Semarang', 'Jawa Tengah', null, 'On Grid', 'semarang', 'Bisnis & Industri', 'ongrid', -6.9667, 110.4167],
            ['PLTS Makassar', 'Makassar', 'Sulawesi Selatan', null, 'Off Grid', 'makassar', 'Komunitas', 'offgrid', -5.1477, 119.4327],
            ['PLTS On Grid Cibitung 2 MWp', 'Cibitung', 'Jawa Barat', 2000, 'On Grid', 'cibitung-2000', 'Kemitraan EPC', 'ongrid', -6.2564, 107.0836],
            ['PLTS On Grid Boyolali', 'Boyolali', 'Jawa Tengah', 200, 'On Grid', 'boyolali-200', 'Bisnis & Industri', 'ongrid', -7.5306, 110.5958],
            ['PLTS On Grid Demak', 'Demak', 'Jawa Tengah', 64, 'On Grid', 'demak-64', 'Bisnis & Industri', 'ongrid', -6.8909, 110.6396],
            ['PLTS On Grid Pulogadung', 'Pulogadung', 'DKI Jakarta', 615, 'On Grid', 'pulogadung-615', 'Bisnis & Industri', 'ongrid', -6.1900, 106.9050],
            ['PLTS On Grid Cikarang 508 kWp', 'Cikarang', 'Jawa Barat', 508, 'On Grid', 'cikarang-508', 'Bisnis & Industri', 'ongrid', -6.3000, 107.1700],
            ['PLTS Kapal Nelayan Labuan Bajo (19 Kapal)', 'Labuan Bajo', 'Nusa Tenggara Timur', 19, 'Solusi Terapan', 'kapal-2', 'Komunitas', null, -8.5100, 119.8700],
        ];
        $terapan = ProductCategory::where('name', 'Perahu Tenaga Surya')->first();
        foreach ($rows as $i => [$title, $loc, $prov, $cap, $type, $img, $seg, $cat, $lat, $lng]) {
            $isBoat = str_contains($title, 'Kapal');
            Portfolio::updateOrCreate(['title' => $title], [
                'location' => $loc, 'province' => $prov, 'capacity' => $cap, 'capacity_unit' => $isBoat ? 'kapal' : 'kWp', 'system_type' => $type,
                'cover' => $this->asset($img), 'segment_id' => $segments[$seg]->id ?? null,
                'product_category_id' => $cat ? $cats[$cat]->id : $terapan?->id,
                'gallery' => $isBoat ? array_values(array_filter([$this->asset('kapal-1'), $this->asset('kapal-3')])) : ($img === 'medan-4004' ? [$this->asset('medan-4004b')] : null),
                'summary' => $isBoat ? 'Elektrifikasi 19 perahu nelayan dengan sistem tenaga surya.' : 'Sistem PLTS '.$type.($cap ? ' '.$cap.' kWp' : '').' di '.$loc.'.',
                'lat' => $lat, 'lng' => $lng, 'is_featured' => in_array($img, ['medan-4004', 'cibitung-2000', 'pulogadung-615', 'cikarang-504', 'labuanbajo-17', 'kapal-2']),
                'sort' => $i, 'is_active' => true,
            ]);
        }
    }

    protected function content(): void
    {
        $banners = [
            ['Building Indonesia’s Integrated Renewable Energy Ecosystem', 'Neira Energy', 'EPC • Manpower • Energy Trading • Digital Platform — mitra strategis transisi energi nasional.', 'hero', 'Konsultasi Gratis', null, 'Lihat Produk', '/produk'],
            ['10+ MWp terpasang di 30+ kota', 'Portfolio', 'Dari rooftop pabrik hingga PLTS kapal nelayan — proyek energi bersih di seluruh Indonesia.', 'medan-4004', 'Lihat Portfolio', '/portfolio', null, null],
            ['Tim khusus perizinan PLTS', 'Perizinan', 'Kuota IUPTLU dibuka hanya Januari & Juli. Kami bantu hingga NIDI & SLO terbit.', 'cikarang-504', 'Pelajari', '/halaman/perizinan-plts', 'Konsultasi', null],
        ];
        foreach ($banners as $i => [$t, $s, $d, $img, $c1, $u1, $c2, $u2]) {
            Banner::updateOrCreate(['title' => $t], ['subtitle' => $s, 'description' => $d, 'image' => $this->asset($img), 'cta_label' => $c1, 'cta_url' => $u1, 'cta2_label' => $c2, 'cta2_url' => $u2, 'sort' => $i, 'is_active' => true]);
        }

        foreach ([
            ['Nomor Induk Berusaha (NIB)', 'active'], ['Sertifikat Badan Usaha (SBU) PLTS', 'active'],
            ['Izin Usaha Jasa Penunjang Tenaga Listrik (IUJPTL)', 'active'], ['Member of AESI', 'active'],
            ['Penunjukan resmi LIT – layanan SLO PLTS', 'active'],
            ['ISO 9001 · ISO 14001 · ISO 45001', 'in_process'], ['TD-BUPPJ bidang Alat Penerangan Jalan (APJ)', 'in_process'],
        ] as $i => [$n, $st]) {
            Certification::updateOrCreate(['name' => $n], ['status' => $st, 'sort' => $i, 'is_active' => true]);
        }

        $faqs = [
            ['PLTS', 'Apa perbedaan PLTS On Grid, Off Grid, dan Hybrid?', '<p><strong>On Grid</strong> terhubung ke PLN dan paling ekonomis untuk menekan tagihan. <strong>Off Grid</strong> berdiri sendiri dengan baterai untuk lokasi tanpa PLN. <strong>Hybrid</strong> menggabungkan keduanya sehingga tetap menyala saat PLN padam.</p>'],
            ['PLTS', 'Berapa luas atap yang dibutuhkan?', '<p>Rata-rata sekitar 5–6 m² per kWp, tergantung tipe panel dan kondisi atap. Tim kami akan melakukan survei untuk memastikan.</p>'],
            ['PLTS', 'Berapa lama balik modal PLTS?', '<p>Umumnya 5–8 tahun untuk sistem on grid, tergantung tarif listrik, pola pemakaian, dan kapasitas sistem.</p>'],
            ['Perizinan', 'Kapan pengajuan izin PLTS atap dibuka?', '<p>Berdasarkan Permen ESDM No. 2 Tahun 2024, perizinan PLTS atap berbasis kuota dari pemegang IUPTLU dan dibuka dua kali setahun, yaitu bulan <strong>Januari</strong> dan <strong>Juli</strong>.</p>'],
            ['Perizinan', 'Apakah Neira membantu pengurusan NIDI dan SLO?', '<p>Ya. Neira memiliki tim khusus perizinan PLTS dan telah mendapat surat penunjukan resmi dari LIT untuk layanan Sertifikat Laik Operasi (SLO) PLTS.</p>'],
            ['PJU', 'Berapa lama PJU tenaga surya menyala?', '<p>PJU kami dirancang menyala sekitar 12 jam per malam dengan cadangan baterai untuk hari berawan.</p>'],
            ['Umum', 'Di mana saja area layanan Neira Energy?', '<p>Kami telah menangani proyek di 30+ kota di Indonesia, dari Sumatera hingga Nusa Tenggara.</p>'],
        ];
        foreach ($faqs as $i => [$g, $q, $a]) {
            Faq::updateOrCreate(['question' => $q], ['group' => $g, 'answer' => $a, 'sort' => $i, 'is_active' => true]);
        }

        $cat = PostCategory::firstOrCreate(['name' => 'Regulasi'], ['slug' => 'regulasi']);
        PostCategory::firstOrCreate(['name' => 'Tips & Edukasi'], ['slug' => 'tips-edukasi']);
        Post::updateOrCreate(['title' => 'Perizinan PLTS Atap Kini Berbasis Kuota: Dibuka Januari & Juli'], [
            'post_category_id' => $cat->id, 'status' => 'published', 'published_at' => now()->subDays(3), 'cover' => $this->asset('cikarang-504'),
            'excerpt' => 'Permen ESDM No. 2 Tahun 2024 mengatur pemasangan PLTS atap berdasarkan kuota IUPTLU yang dibuka dua kali setahun.',
            'content' => '<p>Berdasarkan <strong>Peraturan Menteri ESDM No. 2 Tahun 2024</strong>, perizinan pemasangan PLTS atap saat ini berdasarkan <strong>kuota</strong> dari pemegang IUPTLU dan periode pengajuannya hanya dibuka <strong>dua kali dalam setahun</strong>, yaitu pada bulan <strong>Januari</strong> dan <strong>Juli</strong>.</p><h2>Apa artinya bagi Anda?</h2><p>Perencanaan proyek PLTS perlu disiapkan lebih awal agar dokumen siap saat periode kuota dibuka.</p><h2>Bagaimana Neira membantu?</h2><p>Neira memiliki tim khusus perizinan PLTS, termasuk pengurusan NIDI dan SLO. Neira juga telah mendapat surat penunjukan resmi dari LIT untuk melayani kebutuhan Sertifikat Laik Operasi (SLO) PLTS.</p>',
            'tags' => ['PLTS atap', 'regulasi', 'SLO'],
        ]);

        Page::updateOrCreate(['slug' => 'perizinan-plts'], [
            'title' => 'Perizinan PLTS', 'subtitle' => 'Tim khusus perizinan PLTS — termasuk NIDI & SLO', 'show_in_menu' => true, 'is_active' => true,
            'hero_image' => $this->asset('cikarang-504'),
            'blocks' => [
                ['type' => 'text', 'data' => ['heading' => 'Kuota PLTS dibuka 2x setahun', 'body' => '<p>Berdasarkan Permen ESDM No. 2 Tahun 2024, perizinan pemasangan PLTS saat ini berdasarkan <strong>kuota dari IUPTLU</strong> dan dibuka hanya 2 kali dalam setahun: <strong>Januari</strong> dan <strong>Juli</strong>.</p><p>Neira telah mendapatkan surat penunjukan resmi dari LIT untuk melayani kebutuhan Sertifikat Laik Operasi PLTS (SLO PLTS).</p>', 'image' => $this->asset('install-2'), 'image_position' => 'right']],
                ['type' => 'steps', 'data' => ['heading' => 'Layanan perizinan kami', 'items' => [
                    ['title' => 'Persiapan dokumen', 'text' => 'Pengumpulan data teknis dan administrasi.'],
                    ['title' => 'Pengajuan kuota', 'text' => 'Pengajuan pada periode Januari / Juli.'],
                    ['title' => 'NIDI', 'text' => 'Nomor Identitas Instalasi.'],
                    ['title' => 'SLO', 'text' => 'Sertifikat Laik Operasi melalui LIT.'],
                ]]],
                ['type' => 'faq', 'data' => ['heading' => 'Pertanyaan seputar perizinan', 'group' => 'Perizinan']],
                ['type' => 'cta', 'data' => ['title' => 'Jangan lewatkan periode kuota berikutnya', 'text' => 'Siapkan dokumen PLTS Anda bersama tim kami.', 'button_label' => 'Konsultasi perizinan']],
            ],
        ]);

        Gallery::updateOrCreate(['title' => 'PLTS Kapal Nelayan Labuan Bajo'], [
            'description' => 'Instalasi sistem tenaga surya pada 19 kapal nelayan di Labuan Bajo.', 'is_active' => true,
            'items' => collect(['kapal-1', 'kapal-2', 'kapal-3'])->map(fn ($k) => ['type' => 'image', 'image' => $this->asset($k), 'caption' => null])->all(),
        ]);
        Gallery::updateOrCreate(['title' => 'Instalasi di Lapangan'], [
            'description' => 'Dokumentasi pekerjaan tim instalasi Neira.', 'is_active' => true,
            'items' => collect(['install-1', 'install-2', 'ground-1', 'medan-4004b', 'tasikmalaya-b', 'rooftop-1'])->map(fn ($k) => ['type' => 'image', 'image' => $this->asset($k), 'caption' => null])->all(),
        ]);

        Promo::updateOrCreate(['title' => 'Contoh Promo – Diskon 5% PLTS On Grid'], [
            'code' => 'CONTOH5', 'short_description' => 'Contoh promo. Aktifkan & sesuaikan di admin.',
            'discount_type' => 'percent', 'discount_value' => 5, 'applies_to' => 'all', 'is_active' => false, 'show_on_site' => true,
        ]);
    }
}
