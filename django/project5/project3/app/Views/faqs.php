<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<section class="app-card page-section">
    <h2 class="section-title">FAQ Singkat</h2>

    <div class="post-grid">
        <article class="post-card">
            <h3 class="post-title">Gimana cara mulai nulis?</h3>
            <p class="mb-0">Login dulu, masuk dashboard, terus klik tombol "Tulis Post Baru".</p>
        </article>
        <article class="post-card">
            <h3 class="post-title">Kalau belum siap publish?</h3>
            <p class="mb-0">Simpan aja sebagai draft. Nanti bisa lanjut edit kapan pun.</p>
        </article>
        <article class="post-card">
            <h3 class="post-title">Bisa cari artikel tertentu?</h3>
            <p class="mb-0">Bisa. Tinggal pakai kolom pencarian di halaman blog atau dashboard admin.</p>
        </article>
        <article class="post-card">
            <h3 class="post-title">Kalau lupa password gimana?</h3>
            <p class="mb-0">Di halaman login ada link reset password, tinggal ikuti langkahnya.</p>
        </article>
    </div>
</section>
<?= $this->endSection() ?>
