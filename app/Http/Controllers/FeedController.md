<span>Kode tersebut adalah </span>`FeedController` di Laravel yang bertugas mengatur **halaman utama beranda (*feed*), pembuatan status/postingan baru, serta penghapusan postingan** beserta seluruh komentar dan file gambar yang terkait dengannya.

1\. Metode `index()` — Menampilkan Daftar Postingan

```php
    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])
            ->withCount('likes')
            ->withExists(['likes as liked_by_me' => fn ($q) => $q->where('user_id', Auth::id())])
            ->orderByDesc('created_at')->orderByDesc('id') // terbaru di atas
            ->paginate(10);

        return view('feed.index', compact('posts'));
    }
```

- <span>**Optimasi Query (**</span>`with(['user', 'comments.user'])`<span>**).** Menggunakan *Eager Loading* untuk mengambil data pembuat postingan sekaligus semua komentar dan penulis komentarnya dalam sekali jalan. Ini mencegah masalah performa lambat (*N+1 Query Problem*).</span>
- <span>**Menghitung Jumlah Like (**</span>`withCount('likes')`<span>**).** Otomatis menambahkan kolom virtual </span>`likes_count`<span> pada setiap postingan sehingga Anda tinggal memanggil </span>`$post->likes_count`<span> di Blade.</span>
- <span>**Status Like Pengguna Aktif (**</span>`withExists(...)`<span>**).** Mengecek apakah pengguna yang sedang login (</span>`Auth::id()`<span>) sudah menyukai postingan tersebut. Hasilnya berupa nilai </span>`true`<span> atau </span>`false`<span> pada properti </span>`$post->liked_by_me`<span> (berguna untuk memberi warna merah pada tombol hati/like).</span>
- <span>**Urutan & Halaman (**</span>`orderByDesc(...)` <span>**&**</span> `paginate(10)`<span>**).** Mengurutkan postingan dari yang paling baru dan membaginya menjadi **10 postingan per halaman**.</span>

2\. Metode `store()` — Membuat Postingan Baru

```php
    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => ['nullable', 'string', 'max:2000', 'required_without_all:image,image_url'],
            'image' => Pesan::fileRule(),
            'image_url' => Pesan::urlRule(),
        ], Pesan::messages(), Pesan::attributes());

        Post::create([
            'user_id' => Auth::id(),
            'content' => $data['content'] ?? null,
            'image' => ImageStorage::store($request, 'image', 'image_url', 'posts'),
        ]);

        return redirect()->route('feed')->with('success', 'Status berhasil dikirim.');
    }
```

- <span>**Validasi Cerdas (**</span>`required_without_all:image,image_url`<span>**).** Kolom teks (</span>`content`<span>) boleh kosong, **asalkan** pengguna mengunggah gambar (</span>`image`<span>) atau mengisi tautan gambar (</span>`image_url`<span>). Jika ketiganya kosong, validasi akan menolak.</span>
- <span>**Aturan Gambar Terpusat (**</span>`Pesan::fileRule()` <span>**&**</span> `Pesan::urlRule()`<span>**).** Memanggil standar validasi ukuran/format file dan URL gambar dari kelas bantuan </span>`Pesan`<span>.</span>
- <span>**Penyimpanan Data & Gambar.** Menyimpan status dengan </span>`user_id`<span> milik pengguna yang sedang login, dan menyimpan gambar ke folder </span>`storage/app/public/posts`<span> melalui </span>`ImageStorage::store()`<span>.</span>

3\. Metode `destroy()` — Menghapus Postingan & Bersih-Bersih File

```php
    public function destroy(Post $post)
    {
        // Otorisasi: hanya pemilik postingan yang boleh menghapus.
        abort_unless($post->user_id === Auth::id(), 403, 'Anda tidak berhak menghapus postingan ini.');

        $files = $post->comments()->pluck('image')->push($post->image);

        $post->delete(); // likes & comments ikut terhapus (ON DELETE CASCADE)

        $files->each(fn ($f) => ImageStorage::delete($f));

        return back()->with('success', 'Status berhasil dihapus.');
    }
```

- <span>**Otorisasi Keamanan (**</span>`abort_unless(...)`<span>**).** Memastikan hanya pemilik asli postingan yang bisa menghapusnya. Jika pengguna lain mencoba menghapus lewat celah URL, server langsung memblokir dengan kode error **403 Forbidden**.</span>
- <span>**Mengumpulkan Semua Daftar Gambar (**</span>`pluck('image')->push($post->image)`<span>**).** Sebelum data dihapus dari database, kode mengumpulkan seluruh path gambar dari semua komentar di postingan tersebut, lalu menggabungkannya dengan path gambar milik postingan itu sendiri.</span>
- <span>**Menghapus Data & File Fisik.** Setelah baris postingan dihapus dari database (</span>`$post->delete()`<span>), perulangan </span>`$files->each(...)`<span> menghapus seluruh file gambar lokal terkait dari penyimpanan server agar tidak menyisakan file sampah (*orphan files*).</span>