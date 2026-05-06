<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<section class="app-card page-section">
    <h2 class="section-title">Kontak yang bisa dihubungi</h2>

    <div class="post-grid">
        <article class="post-card">
            <h3 class="post-title">Email</h3>
            <p class="mb-0">unusia@student.ac.id</p>
        </article>
        <article class="post-card">
            <h3 class="post-title">WhatsApp</h3>
            <p class="mb-0">+62 887-2047-385</p>
        </article>
        <article class="post-card">
            <h3 class="post-title">Lokasi Tim</h3>
            <p class="mb-0">Kampus Unusia.</p>
        </article>
        <article class="post-card">
            <h3 class="post-title">Jam Respons</h3>
            <p class="mb-0">Senin - Jumat, 09.00 - 16.00 WIB.</p>
        </article>
    </div>

</section>
<?= $this->endSection() ?>
