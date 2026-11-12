<?php

// [Anggota 1] 3 artikel wajib (konten format Markdown)

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::where('email', 'staff@demo.test')->value('id');

        $articles = [
            [
                'title' => 'Tips Memilih Tenda Glamping untuk Pemula di Lereng Merapi',
                'slug' => 'tips-memilih-tenda-glamping-untuk-pemula-di-lereng-merapi',
                'category' => 'Tips',
                'excerpt' => 'Baru pertama kali glamping? Kenali dulu tipe tenda, kapasitas, dan fasilitas yang sesuai dengan gaya liburanmu di Cangkringan.',
                'content' => <<<'MD'
Glamping (*glamorous camping*) adalah cara paling nyaman menikmati alam tanpa repot mendirikan tenda sendiri. Untuk kamu yang baru pertama kali mencoba di lereng Merapi, berikut beberapa hal yang perlu diperhatikan.

## 1. Sesuaikan dengan jumlah rombongan
- **Berdua dengan pasangan**: pilih *Standard River View Tent* yang intim dengan kasur king.
- **Keluarga kecil atau 3–4 sahabat**: *Deluxe Merapi View* punya tambahan single bed dan balkon pribadi.
- **Rombongan hingga 6 orang**: *Family Glamping Suite* seluas 36 m² dengan ruang keluarga.

## 2. Perhatikan view yang diinginkan
Ingin mendengar gemericik sungai saat tidur? Pilih tenda tepi sungai. Ingin melihat siluet Merapi saat matahari terbit? Tenda dengan balkon menghadap utara adalah jawabannya.

## 3. Cek fasilitas penunjang
Pastikan tersedia AC (udara siang di Cangkringan bisa cukup hangat), sarapan, serta akses ke fasilitas bersama seperti kolam renang, area pemancingan, dan BBQ.

## 4. Pertimbangkan asuransi pembatalan
Cuaca di lereng gunung kadang sulit ditebak. Dengan tambahan Rp 50.000, kamu bisa mendapat refund hingga 100% biaya kamar bila rencana berubah.

Selamat merencanakan liburan pertamamu — *bangun pagi disapa Merapi, malam tidur di tengah sawah!*
MD,
            ],
            [
                'title' => 'Checklist Barang Bawaan Glamping Tengah Sawah agar Liburan Anti Ribet',
                'slug' => 'checklist-barang-bawaan-glamping-tengah-sawah-agar-liburan-anti-ribet',
                'category' => 'Persiapan',
                'excerpt' => 'Fasilitas sudah lengkap, tapi beberapa barang pribadi tetap wajib dibawa. Ini daftar lengkapnya supaya liburan di tengah sawah makin santai.',
                'content' => <<<'MD'
Walaupun unit glamping kami sudah dilengkapi kasur empuk, AC, dan sarapan, ada beberapa barang yang sebaiknya kamu siapkan sendiri.

## Pakaian
- Jaket atau sweater — suhu malam di lereng Merapi bisa turun cukup dingin.
- Pakaian renang untuk menikmati kolam.
- Sandal yang nyaman untuk berjalan di pematang sawah.

## Perlengkapan pribadi
- Obat-obatan pribadi dan *lotion* anti nyamuk.
- *Sunscreen* dan topi untuk aktivitas siang.
- *Power bank* dan kabel pengisi daya.

## Untuk aktivitas
- Alat pancing pribadi (opsional, tersedia juga di lokasi).
- Bahan makanan tambahan jika ingin BBQ — perlengkapan panggangan sudah tersedia di Family Suite.
- Kamera untuk mengabadikan *sunrise* Merapi.

## Dokumen
- Kartu identitas sesuai nama pemesan.
- **Kode booking** atau Kartu Check-in yang bisa kamu tampilkan dari menu *Cek Booking*.

Ingat, check-in mulai pukul **14:00 WIB** dan check-out paling lambat **12:00 WIB**.
MD,
            ],
            [
                'title' => 'Glamping Minimalis: Menikmati Tenangnya Pedesaan Cangkringan',
                'slug' => 'glamping-minimalis-menikmati-tenangnya-pedesaan-cangkringan',
                'category' => 'Inspirasi',
                'excerpt' => 'Liburan tidak harus padat jadwal. Coba gaya glamping minimalis: sedikit barang, lebih banyak waktu untuk menikmati suasana desa.',
                'content' => <<<'MD'
Cangkringan, Sleman, adalah desa yang tumbuh di kaki Gunung Merapi. Sawah berundak, udara sejuk, dan ritme hidup yang lambat menjadikannya tempat ideal untuk liburan minimalis.

## Apa itu glamping minimalis?
Konsepnya sederhana: bawa barang secukupnya, kurangi layar gawai, dan perbanyak waktu menikmati momen. Tidak perlu daftar destinasi yang panjang.

## Ide aktivitas sederhana
1. **Jalan pagi menyusuri pematang** sambil menunggu matahari muncul dari balik Merapi.
2. **Memancing santai** di kolam pemancingan tanpa target tangkapan.
3. **Membaca buku di teras** ditemani suara angin dan burung sawah.
4. **Makan malam BBQ** bersama keluarga, lalu memandangi bintang.

## Mendukung warga lokal
Belanjalah oleh-oleh atau makanan dari warung warga sekitar Argomulyo. Liburan minimalis juga berarti meninggalkan jejak sekecil mungkin dan memberi manfaat sebesar mungkin bagi desa.

Sesekali, liburan terbaik adalah yang paling sederhana.
MD,
            ],
        ];

        foreach ($articles as $i => $a) {
            Article::updateOrCreate(
                ['slug' => $a['slug']],
                $a + ['author_id' => $authorId, 'published_at' => now()->subDays(10 - $i * 3)]
            );
        }
    }
}
