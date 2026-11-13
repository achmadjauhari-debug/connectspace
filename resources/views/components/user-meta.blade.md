<span>Kode tersebut adalah sebuah </span>**Komponen Blade Anonim (*Anonymous Blade Component*)** di Laravel (biasanya disimpan di `resources/views/components/meta.blade.php` atau nama serupa) yang berfungsi untuk **menampilkan informasi metadata lokasi dan waktu pembuatan** secara rapi pada suatu postingan atau komentar.

1\. Deklarasi Parameter Komponen (`@props`)

```
@props(['user', 'time', 'location' => null, 'showProfileLocation' => true])
```

- <span>**Fungsi:** Mendefinisikan data (properti) apa saja yang bisa dikirimkan saat komponen ini dipanggil dari halaman lain.</span>
- `'user'` <span>**&**</span> `'time'`<span>: Wajib diisi (tanpa nilai bawaan), berisi objek pengguna dan objek waktu (</span>`Carbon`<span>).</span>
- `'location' => null`<span>: Opsional, berisi teks lokasi spesifik (misalnya lokasi khusus saat membuat postingan). Jika tidak dikirim, nilainya </span>`null`<span>.</span>
- `'showProfileLocation' => true`<span>: Sakelar logika dengan nilai bawaan </span>`true`<span> untuk menentukan sumber lokasi mana yang akan ditampilkan.</span>

2\. Logika Penentuan Lokasi (`@php(...)`)

```
@php($displayLocation = $showProfileLocation ? $user->location : $location)
```

- <span>Menggunakan operator *ternary* (</span>`kondisi ? jika_benar : jika_salah`<span>) untuk memilih teks lokasi yang akan tampil:</span>
  - <span>Jika </span>`$showProfileLocation`<span> bernilai </span>`true`<span>, sistem mengambil lokasi dari profil pengguna (</span>`$user->location`<span>).</span>
  - <span>Jika </span>`$showProfileLocation`<span> bernilai </span>`false`<span>, sistem mengambil lokasi yang dikirimkan lewat parameter </span>`$location`<span>.</span>

3\. Penggabungan Atribut HTML (`$attributes->merge`)

```
<span {{ $attributes->merge(['class' => 'meta']) }}>
```

- <span>Membungkus seluruh konten di dalam tag </span>`<span>`<span> dengan kelas CSS bawaan </span>`meta`<span>.</span>
- <span>**Kelebihan**</span> `merge()`<span>**:** Jika saat memanggil komponen Anda menambahkan kelas CSS tambahan (misalnya </span>`<x-meta class="text-muted" ... />`<span>), Laravel otomatis menggabungkannya menjadi </span>`class="meta text-muted"`<span> tanpa menimpa kelas </span>`meta`<span>.</span>

4\. Menampilkan Lokasi & Tanda Pemisah (`@if`)

```
@if ($displayLocation)
    <span>{{ $displayLocation }}</span>
    ·
@endif
```

- <span>Mengecek apakah </span>`$displayLocation`<span> memiliki isi (tidak kosong/null).</span>
- <span>Jika ada, teks lokasi dicetak diikuti tanda titik tengah (</span>`·`<span>) sebagai pemisah antara lokasi dan waktu. Jika pengguna tidak mengatur lokasi, bagian ini beserta titik pemisahnya tidak akan muncul sama sekali.</span>

5\. Format Waktu Ramah Mesin & Manusia (`<time>`)

```
<time datetime="{{ $time->toIso8601String() }}">{{ $time->format('d/M/y H:i') }}</time>
```

- `datetime="{{ $time->toIso8601String() }}"`<span>: Menghasilkan format waktu standar internasional (contoh: </span>`2026-11-13T14:24:00+07:00`<span>) di dalam atribut HTML agar mudah dibaca oleh mesin pencari (SEO) dan pembaca layar.</span>
- `{{ $time->format('d/M/y H:i') }}`<span>: Menampilkan waktu yang mudah dibaca manusia di layar (contoh: </span>`13/Nov/26 14:24`<span>).</span>

Contoh Cara Memanggil Komponen Ini di Blade

```
{{-- Menampilkan lokasi dari profil user --}}
<x-meta :user="$post->user" :time="$post->created_at" />

{{-- Menampilkan lokasi khusus postingan (bukan dari profil) --}}
<x-meta :user="$post->user" :time="$post->created_at" :show-profile-location="false" location="Jakarta" />
```

**Rincian Arti Setiap Huruf Pola (**`'d/M/y H:i'`**)**

- `d`<span>: Tanggal dua digit dengan angka nol di depan (contoh: </span>`01`<span> sampai </span>`31`<span>).</span>
- `M`<span>: Singkatan nama bulan dalam tiga huruf (contoh: </span>`Jan`<span>, </span>`Feb`<span>, </span>`Nov`<span>). *(Catatan: huruf kecil*</span> `m` <span>*akan menghasilkan angka bulan seperti*</span> `11`<span>*, sedangkan huruf besar*</span> `M` <span>*menghasilkan singkatan teks).*</span>
- `y`<span>: Tahun dua digit terakhir (contoh: </span>`26`<span> untuk tahun 2026). *(Catatan: huruf besar*</span> `Y` <span>*akan menghasilkan empat digit penuh seperti*</span> `2026`<span>*).*</span>
- `H`<span>: Jam dalam format 24 jam dua digit (contoh: </span>`00`<span> sampai </span>`23`<span>).</span>
- `i`<span>: Menit dua digit dengan angka nol di depan (contoh: </span>`00`<span> sampai </span>`59`<span>).</span>
- <span>**Tanda**</span> `/` <span>**dan**</span> `:`<span>: Karakter pemisah biasa yang dicetak apa adanya.</span>

<span>Untuk mengubah tampilan waktu menjadi format relatif berbahasa Indonesia (seperti </span>**"baru saja"**, **"5 menit yang lalu"**, atau **"2 hari yang lalu"**), Anda cukup mengganti metode `format()` pada komponen Blade dengan `diffForHumans()` milik Carbon dan mengatur bahasa aplikasi ke bahasa Indonesia (`id`).

**1. Ubah Kode di Komponen Blade Anda**

Buka file komponen Blade Anda tadi, lalu ubah bagian isi di dalam tag `<time>` menjadi seperti ini:

```
<time datetime="{{ $time->toIso8601String() }}" title="{{ $time->format('d/M/y H:i') }}">
    {{ $time->locale('id')->diffForHumans() }}
</time>
```

- `->locale('id')`<span>: Memaksa penerjemahan waktu ke dalam **Bahasa Indonesia** langsung pada objek waktu tersebut (sehingga tidak tampil *"5 minutes ago"*).</span>
- `->diffForHumans()`<span>: Menghitung selisih waktu antara waktu postingan dibuat dengan waktu saat ini, lalu menerjemahkannya secara otomatis:</span>
  - <span>Kurang dari 1 menit: **"1 detik yang lalu"** atau **"baru saja"** (tergantung detik berjalan).</span>
  - <span>5 menit berselang: **"5 menit yang lalu"**.</span>
  - <span>3 jam berselang: **"3 jam yang lalu"**.</span>
  - <span>2 hari berselang: **"2 hari yang lalu"**.</span>
- `title="{{ $time->format('d/M/y H:i') }}"`<span> *(Opsional)*: Jika pengguna mengarahkan kursor mouse ke teks *"5 menit yang lalu"*, akan muncul kotak kecil berisi tanggal dan jam pastinya (</span>`13/Nov/26 14:38`<span>).</span>
- 

**2. Pengaturan Global di** `.env` **(Agar Tidak Perlu Menulis** `->locale('id')` **Terus-Menerus)**

Jika Anda ingin seluruh format waktu di aplikasi ConnectSpace otomatis berbahasa Indonesia tanpa harus mengetik `->locale('id')` di setiap file:

1. <span>Buka file </span>`.env`<span> di folder utama proyek Laravel Anda.</span>
2. <span>Cari baris </span>`APP_LOCALE`<span> (atau tambahkan jika belum ada), lalu ubah nilainya menjadi </span>`id`<span>:</span>

```
APP_LOCALE=id
```

3. Setelah diubah di `.env`, kode di Blade Anda cukup ditulis singkat menjadi:

```
<time datetime="{{ $time->toIso8601String() }}">
    {{ $time->diffForHumans() }}
</time>
```

(Catatan: Pastikan juga pengaturan zona waktu di `.env` adalah `APP_TIMEZONE=Asia/Jakarta` agar hitungan selisih menitnya akurat dengan waktu Indonesia).