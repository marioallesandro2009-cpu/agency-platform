# Aturan Blog Mingguan (dibaca agen tiap Senin)

Tujuan: 5 artikel baru tiap Senin agar blog terlihat aktif dan peringkat naik bertahap. Calon pembaca: pemilik, direktur, dan manajer perusahaan yang mempertimbangkan website atau software custom.

## Batas yang tidak boleh dilanggar

1. Tidak provokatif, tidak menakut-nakuti, tidak mengejek, tidak membandingkan dengan pesaing.
2. Tidak menyinggung SARA, politik, atau memuat pelecehan, kata kasar, dan konten dewasa.
3. Tidak membahas atau mengutip undang-undang, pasal, atau peraturan.
4. Tidak ada klaim tanpa dasar: angka dan fakta wajib bersumber dari **sumber primer** (lembaga yang menerbitkan data itu sendiri) dan ditautkan. Kalau tidak ada sumbernya, tulis sebagai pendapat atau pengalaman umum ("biasanya", "sering terjadi"), bukan sebagai fakta.
5. Tidak menjanjikan hasil ("pasti", "dijamin", "terbaik", "100%").
6. Tidak mengarang cerita klien, testimoni, atau studi kasus. Pembuka memakai pengandaian ("bayangkan...").
7. **Tidak ada topik toko online, e-commerce, marketplace, dropship**, dan tidak menautkan ke halaman toko online.
8. Tidak menyebut nama merek atau lembaga di teks (termasuk nama situs kita). Sumber ditautkan di balik teks tautan yang umum, misalnya "laporan resmi", "publikasi lembaga statistik".
9. Setiap artikel wajib punya gambar yang bebas hak cipta dan boleh dipakai komersial, dengan catatan lisensi.

Pemeriksa otomatis (`node scripts/cek-artikel.mjs`) menegakkan sebagian besar aturan ini. Artikel yang gagal **tidak boleh diterbitkan**. Pemeriksa tidak menggantikan penilaianmu: baca ulang tiap artikel dengan kritis sebelum menyerahkannya.

## Gaya tulisan

- Bercerita yang mengalir, bukan daftar. Pembuka berupa adegan yang dikenali pembaca, lalu fakta bersumber di tengah, lalu adegan yang sama diputar ulang dengan sistem yang tepat, ditutup dengan pertanyaan yang bisa diuji pembaca di kantornya sendiri.
- Persuasif dan mengajak, bukan menekan. Boleh mengajak konsultasi gratis di akhir.
- Jangan memakai label "masalah", "solusi", "fakta", "rangkuman", "kesimpulan" di subjudul. Poin singkat boleh sedikit saja.
- Tidak ada subjudul bernama "Daftar isi" yang dibuat dari h2; boleh memakai daftar isi bernomor di awal seperti artikel lain.

## SEO

- Judul **memuat angka** dan kata kunci utama, 30 sampai 75 karakter. Contoh: "7 Alasan Perusahaan Membutuhkan Sistem Informasi Gudang".
- `focus_keyword`: 2 sampai 5 kata, huruf kecil. Muncul di judul, seo_title, seo_description, paragraf pertama, dan minimal 2 subjudul h2.
- Kepadatan kata kunci wajar: 0,6 sampai 2,2 persen. Pengulangan alami, jangan dipaksakan. Pakai juga sinonim dan variasi.
- Panjang isi minimal 1000 kata, minimal 5 subjudul h2, minimal 2 tautan internal (satu ke `/konsultasi` atau `/jasa-*`) dan minimal 1 tautan eksternal ke sumber primer.
- Tautan internal yang boleh: `/jasa-software-custom`, `/jasa-web-development`, `/jasa-website-company-profile`, `/jasa-website-umkm`, `/konsultasi`, `/portfolio`, `/kontak`, `/faq`, `/tentang-kami`, dan `/blog/<slug>` artikel yang sudah ada.
- Judul tidak boleh mirip artikel yang sudah ada. Cek daftar slug dan judul di `database/seeders/data/`.

## Topik

1. Ambil dulu dari `docs/blog-mingguan/antrian-judul.txt` (judul dari pemilik). Satu judul per baris. Setelah dipakai, pindahkan ke bagian "Sudah dipakai" di berkas itu.
2. Kalau antrian kurang dari 5, buat sendiri topik yang belum ada di blog. Bidang: website perusahaan, software custom, SEO untuk bisnis, digitalisasi proses kantor, keamanan dan performa website, UMKM yang butuh website (bukan toko online).
3. Kategori yang tersedia: `tips-website`, `seo`, `umkm`, `studi-kasus`, `custom-software`, `website-korporat`.

## Format berkas

Satu berkas per minggu: `database/seeders/data/blog-mingguan/YYYY-MM-DD.json` (tanggal Senin penerbitan). Isinya array 5 objek:

```json
[
  {
    "title": "7 Alasan Perusahaan Membutuhkan Sistem Informasi Gudang",
    "seo_title": "7 Alasan Perusahaan Butuh Sistem Informasi Gudang",
    "slug": "alasan-perusahaan-butuh-sistem-informasi-gudang",
    "category": "custom-software",
    "focus_keyword": "sistem informasi gudang",
    "excerpt": "80 sampai 200 karakter, ringkasan yang mengajak membaca.",
    "seo_description": "120 sampai 160 karakter, memuat focus_keyword.",
    "published_at": "2026-11-23 07:00:00",
    "image": "alasan-perusahaan-butuh-sistem-informasi-gudang",
    "image_alt": "Deskripsi gambar 40 sampai 160 karakter, bukan sama dengan judul",
    "content": "<p>...</p><h2 id=\"...\">...</h2>..."
  }
]
```

`content` adalah HTML: `<p>`, `<h2 id="...">`, `<h3>`, `<ul>`, `<ol>`, `<a>`, `<strong>`. Tanpa `<h1>`. Tautan eksternal: `<a href="https://..." target="_blank" rel="noopener">teks umum</a>`. Gunakan JSON yang valid (kutip ganda di dalam isi ditulis `\"`). Cara termudah: tulis lewat skrip Node yang membuat objek lalu `JSON.stringify`.

`published_at`: Senin penerbitan, pukul 07:00. Boleh disebar (mis. Senin sampai Jumat) hanya bila pemilik meminta; bawaannya semua hari Senin.

## Gambar

Satu gambar per artikel: `public/images/blog/<image>.jpg`, lebar minimal 1000 px (disarankan 1600 x 900), ukuran maksimal 600 KB, rasio 16:9.

Urutan sumber:
1. **Foto bebas hak cipta** lewat API Openverse tanpa kunci: `https://api.openverse.org/v1/images/?q=<kata>&license=cc0,pdm&mature=false&page_size=20`. Hanya lisensi **CC0** atau **PDM** (domain publik). Jangan pakai lisensi yang mewajibkan atribusi atau melarang komersial. Pilih foto yang relevan, tanpa wajah yang dikenali sebagai orang tertentu, tanpa logo, dan tanpa teks yang tampak. Unduh, ubah ke JPEG 1600 px bila perlu.
2. Kalau jaringan tidak bisa menjangkau sumber foto, atau tidak ada foto yang cocok: **buat gambar sendiri** (ilustrasi abstrak atau infografik sederhana dengan skrip). Lisensinya "Karya sendiri".

Catat tiap gambar di `public/images/blog/LISENSI.csv` dengan kolom `berkas,sumber,url,penulis,lisensi,tanggal`. Lisensi yang diizinkan: `CC0`, `PDM`, `Public Domain`, `Karya sendiri`, `Unsplash License`, `Pexels License`, `Pixabay License`. Untuk gambar dari luar, isi `url` halaman sumber (https). Jangan mengunduh gambar yang tidak jelas lisensinya.

## Pemeriksaan ulang (wajib, sebelum commit)

1. `node scripts/cek-artikel.mjs --file database/seeders/data/blog-mingguan/<tanggal>.json --expect 5`
2. Baca ulang setiap artikel dari awal sampai akhir dengan daftar ini: provokatif? menyinggung kelompok mana pun? klaim tanpa sumber? ada kata yang bisa dianggap pelecehan? ada hal yang bisa melanggar hukum atau norma? ada nama merek atau lembaga? gambar sudah tercatat lisensinya?
3. Artikel yang gagal diperbaiki sampai lolos, atau dibuang dari berkas. **Jangan menerbitkan artikel yang gagal.** Lebih baik 3 artikel yang bersih daripada 5 yang berisiko; catat alasannya di laporan.

## Setelah lolos: DRAF, BUKAN TERBIT

Artikel **tidak boleh** langsung masuk `main` dan tidak boleh diterbitkan. Pemilik meninjau dulu, lalu meminta penerbitan.

1. Buat branch baru `blog/draf-<tanggal>` dari `main`. Jangan pernah push ke `main`.
2. Selain berkas JSON, tulis versi baca untuk pemilik: `docs/blog-mingguan/draf/<tanggal>/<slug>.md` untuk tiap artikel. Isinya judul, kata kunci, kategori, ringkasan, gambar (tautan relatif ke `public/images/blog/<image>.jpg`), lalu isi artikel dalam Markdown biasa, dan di bagian bawah daftar sumber dan lisensi gambar. Pemilik membacanya langsung di GitHub.
3. Tulis laporan singkat di `docs/blog-mingguan/laporan/<tanggal>.md`: judul tiap artikel, kata kunci, sumber angka, sumber dan lisensi gambar, dan artikel yang dibuang beserta alasannya.
4. Commit dengan pesan `draf: N artikel blog mingguan <tanggal>`, push branch itu, dan buka **pull request** ke `main` dengan judul `Draf 5 artikel blog <tanggal>`. Isi deskripsi PR dengan daftar judul dan hasil pemeriksa.
5. Berhenti di situ. Penerbitan (merge dan deploy) hanya dilakukan setelah pemilik meminta, bukan oleh agen ini.
