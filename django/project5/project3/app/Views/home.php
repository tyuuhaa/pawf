<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<section class="app-card page-section home-landing">
    <div class="landing-copy">
        <h2>Tulis cerita kamu, publish saat siap.</h2>
        <p>RuangCerita bantu kamu fokus nulis tanpa ribet. Simpan draft pribadi, edit pelan-pelan, lalu publish pas udah yakin.</p>
        <div class="landing-actions">
            <a href="<?= base_url('login') ?>" class="btn btn-main">Login / Daftar</a>
            <a href="<?= base_url('post') ?>" class="btn btn-soft">Lihat Blog</a>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
