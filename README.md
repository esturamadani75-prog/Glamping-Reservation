# Sistem Manajemen Reservasi — Merapi Glamping Tengah Sawah

> *"Bangun Pagi Disapa Merapi, Malam Tidur di Tengah Sawah"*
> Jl. Panggung No. 86, Jaranan, Argomulyo, Kec. Cangkringan, Sleman, DI Yogyakarta 55584

Aplikasi web full-stack UAS Pemrograman Web — **Laravel 12 + Blade + Tailwind CSS + Alpine.js + Chart.js** dengan 3 role: **User, Staff, Admin**.

Dikerjakan 4 anggota secara berurutan (Anggota 1 → 4). Setiap file diberi penanda `[Anggota N]` di baris awal.

---

## Kebutuhan

- PHP ≥ 8.2 (ekstensi: pdo_sqlite / pdo_mysql, mbstring, openssl, intl disarankan)
- Composer 2
- Koneksi internet saat membuka halaman (Tailwind, Alpine.js, Chart.js, QR Code dimuat dari CDN — **tidak perlu `npm install` / `npm run dev`**)

## Cara menjalankan

```bash
composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```

**Opsi A — SQLite (paling cepat, bawaan):**

```bash
# Linux/Mac
touch database/database.sqlite
# Windows (PowerShell)
New-Item database/database.sqlite
```

**Opsi B — MySQL (XAMPP / Laragon / Herd):** buat database `merapi_glamping`, lalu ubah `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=merapi_glamping
DB_USERNAME=root
DB_PASSWORD=
```

Lalu:

```bash
php artisan migrate:fresh --seed
php artisan serve
```

Buka http://127.0.0.1:8000

## Akun demo

| Role  | Email            | Password     |
|-------|------------------|--------------|
| Admin | admin@demo.test  | `Admin#2026` |
| Staff | staff@demo.test  | `Staff#2026` |
| User  | user@demo.test   | `User#2026`  |

Di halaman login ada tombol untuk mengisi akun demo otomatis.

**Kode booking demo** (cek di menu *Cek Booking* dengan email `user@demo.test`):

| Kode          | Kondisi                                                        |
|---------------|----------------------------------------------------------------|
| `MGTS-DEMO01` | Terkonfirmasi, berasuransi, check-in 10 hari lagi → refund 100% |
| `MGTS-DEMO02` | Terkonfirmasi, sudah ada pengajuan batal *pending* (refund 50%) |
| `MGTS-DEMO03` | Selesai (masa lalu)                                             |
| `MGTS-DEMO04` | Check-in **hari ini** (muncul di dashboard staff)               |
| `MGTS-DEMO05` | Menunggu pembayaran (tombol *Bayar Sekarang (Sandbox)*)          |

Seeder juga membuat ±220 booking dummy agar grafik statistik terisi.

## Ringkasan fitur

**Pengunjung & User:** landing page, katalog unit, artikel, form reservasi dengan cek stok + simulasi harga real-time, pembayaran sandbox, cek status booking → Kartu Check-in + QR dummy + checklist serah terima, register/login, edit profil, riwayat sewa, ajukan pembatalan dengan estimasi refund otomatis, statistik Chart.js.

**Staff:** dashboard (check-in hari ini, pembatalan pending, pendapatan dummy, tabel transaksi), approve/reject pembatalan + catatan refund, CRUD artikel, CRUD unit glamping, tandai booking selesai.

**Admin:** semua fitur staff + CRUD akun (admin/staff/user) + audit log aktivitas staff.

## Logika bisnis penting

| Aturan | Lokasi kode |
|---|---|
| Total kamar = `price_per_night × unit_qty × jumlah_malam`; asuransi Rp 50.000/booking | `app/Services/ReservationService.php` → `quote()` |
| Validasi stok per malam (booking `pending`/`confirmed`), dikunci `lockForUpdate` agar tidak overbooking | `ReservationService::remainingStock()` & `createBooking()` |
| Refund: asuransi & > 3 hari → 100%, asuransi & ≤ 3 hari → 50%, tanpa asuransi → 0% | `app/Services/RefundCalculator.php` |
| Approve pembatalan → booking `cancelled`, stok otomatis kembali, refund dicatat, audit log | `app/Http/Controllers/Staff/CancellationController.php` |
| Proteksi rute per role (`auth`, `role:admin`, `role:staff,admin`) | `app/Http/Middleware/RoleMiddleware.php`, `bootstrap/app.php` |

## Struktur route

| File | Isi | Anggota |
|---|---|---|
| `routes/web.php`, `routes/auth.php` | file induk, login/register/logout | 1 |
| `routes/publik.php` | landing, artikel, reservasi, cek booking | 2 |
| `routes/user.php` | dashboard user, profil, pembatalan, statistik | 3 |
| `routes/panel.php` | panel staff (`/staff`) & admin (`/admin`) | 4 |

## Catatan

- Palet warna: Paddy Emerald `#2D6A4F`, Golden Harvest `#D97706`, Warm Canvas `#FAF9F5`, Merapi Slate `#1E293B` (diatur di `resources/views/partials/head.blade.php`).
- Nomor telepon & email bisnis di `config/glamping.php` adalah **dummy** — ganti bila ada kontak resmi.
- Pembayaran hanya simulasi (sandbox), tidak terhubung ke payment gateway sungguhan.

## Skenario demo (±10 menit)

1. **Pengunjung** — buka landing → Reservasi Family Suite, centang asuransi, lihat harga & stok berubah → Bayar Sandbox → Kartu Check-in + QR.
2. **User** (`user@demo.test`) — riwayat sewa → Ajukan Batal `MGTS-DEMO01` (estimasi 100%) → buka Statistik.
3. **Staff** (`staff@demo.test`) — dashboard → Pembatalan → Terima `MGTS-DEMO01` (stok Deluxe bertambah 1) → ubah tarif unit → tulis artikel.
4. **Admin** (`admin@demo.test`) — tambah akun staff baru → Audit Log menampilkan semua aktivitas langkah 3.
