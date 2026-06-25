<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<section class="app-card page-section mb-4">
    <h2 class="section-title">Tentang RuangCerita</h2>
    <div class="post-grid mt-3">
        <article class="post-card">
            <h3 class="post-title">Nulis Lebih Santai</h3>
            <p class="mb-0">Mulai dari ide pendek dulu, simpan kalau belum selesai, lalu lanjut saat waktu luang.</p>
        </article>
        <article class="post-card">
            <h3 class="post-title">Atur Sesuai Ritme</h3>
            <p class="mb-0">Mau langsung publish atau simpan dulu sebagai draft, semuanya fleksibel.</p>
        </article>
    </div>
</section>

<section class="app-card page-section">
    <h2 class="section-title">Yang sering dipakai</h2>
    <div class="post-grid mt-3">
        <article class="post-card">
            <h3 class="post-title">Dashboard Editor</h3>
            <p class="mb-0">Lihat progress konten, status post, dan aktivitas terbaru dalam satu layar.</p>
        </article>
        <article class="post-card">
            <h3 class="post-title">Pencarian Cepat</h3>
            <p class="mb-0">Cari artikel berdasarkan judul, isi, atau author tanpa perlu scroll panjang.</p>
        </article>
        <article class="post-card">
            <h3 class="post-title">Preview Instan</h3>
            <p class="mb-0">Cek tampilan artikel sebelum dibaca publik, jadi lebih aman sebelum publish.</p>
        </article>
    </div>
</section>
<?= $this->endSection() ?>
