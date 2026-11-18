# Neira Energy — Website & Admin Panel

Website company profile + sales tool PT Neira Bumi Energi. Dibangun dengan **Laravel 12**, **Filament 3** (admin panel), Blade + Tailwind CSS 4 + Alpine.js.

## Fase 1 (selesai)

| Modul | Admin (`/admin`) | Halaman publik |
|---|---|---|
| Identitas perusahaan (profil, kontak, sosmed, visi-misi, unit bisnis, angka, SEO & tracking) | Pengaturan → Identitas Perusahaan | header, footer, `/tentang-kami`, `/kontak` |
| Banner halaman utama (jadwal tayang, gambar desktop/mobile, 2 tombol) | Konten → Banner | slider beranda |
| Kategori produk + **brosur HTML** (section builder) | Produk → Kategori & Brosur | `/produk/{kategori}` & `/produk/{kategori}/brosur` (cetak/PDF) |
| Paket produk per kapasitas (gambar, data teknis, BOM, lingkup kerja, harga, datasheet) | Produk → Paket Produk | `/produk/{kategori}/{paket}` |
| Promo (% / nominal, maks potongan, semua/kategori/paket, periode) | Produk → Promo | `/promo`, harga coret otomatis |
| Segmen pasar (5 segmen) | Produk → Segmen Pasar | `/solusi/{segmen}` |
| Blog / artikel + kategori | Konten → Blog | `/blog` |
| Portfolio (filter tipe & segmen, peta sebaran) | Konten → Portfolio | `/portfolio` |
| FAQ (per kelompok, bisa disisipkan ke brosur) | Konten → FAQ | `/faq` |
| Galeri (foto & video YouTube) | Konten → Galeri | `/galeri` |
| Klien & partner, testimoni, legalitas & sertifikasi | Konten → ... | beranda, tentang kami |
| Halaman statis berbasis blok (mis. Perizinan PLTS) | Konten → Halaman Statis | `/halaman/{slug}` |
| Pengguna & role (Super Admin, Manajer, BD/Sales, Marketing) | Pengaturan → Pengguna | — |

Sitemap otomatis di `/sitemap.xml`.

### Role & hak akses
Diatur di `App\Models\User::PERMISSIONS`:
- **Super Admin** – semua modul.
- **Manajer** – konten, produk, promo, pengaturan (+ CRM & penjualan di fase berikutnya).
- **BD / Sales** – lihat produk & promo (tidak bisa ubah), CRM & penjualan.
- **Marketing / Konten** – konten website, produk, promo.

### Brosur HTML
Setiap kategori punya tab **Brosur (HTML)** berisi section: Hero, Teks + Gambar, Keunggulan, Angka, **Tabel Paket (otomatis)**, **Sub-kategori (otomatis)**, Spesifikasi, Alur, Galeri, FAQ, CTA, HTML kustom. Versi cetak di `/produk/{slug}/brosur` → tombol *Cetak / Simpan PDF*.

### Harga & promo
`ProductPackage::finalPrice()` memilih promo aktif dengan potongan terbesar. Logika ini akan dipakai ulang oleh kalkulator, quotation, dan proposal (fase 2–3).

## Instalasi

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
# atur DB_* di .env (MySQL untuk production)
php artisan migrate --seed
php artisan storage:link
```

Seeder (`database/seeders/NeiraSeeder.php`) mengisi data dari Company Profile 2026 (identitas, legalitas, unit bisnis, 16 portfolio dengan foto, kategori, 24 contoh paket, FAQ, banner, halaman Perizinan PLTS).

Login admin awal: `ADMIN_EMAIL` / `ADMIN_PASSWORD` dari `.env` (default `admin@neiraenergy.com` / `password` — **segera ganti**).

Buat atau reset akun Super Admin kapan saja:

```bash
php artisan neira:admin admin@neiraenergy.com PasswordBaru123
```

> ⚠️ Harga paket di seeder adalah **contoh**. Sesuaikan di admin sebelum go-live.

## Test

```bash
php artisan test
```

## Roadmap
- **Fase 2** – Kalkulator PLTS/PJU → rekomendasi paket; CRM tiket multi-source (form web, kalkulator, WhatsApp, Email); dashboard pipeline.
- **Fase 3** – Quotation generator (tarik data paket + promo, snapshot harga, approval diskon) & proposal generator (paket standar + generator custom: Helioscope, NGO, backup genset, dll — porting dari AppScript).
- **Fase 4** – Partnership / affiliate (referral link, atribusi tiket, komisi).
