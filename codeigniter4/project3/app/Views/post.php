<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<section class="app-card page-section mb-4">
    <form method="get" action="<?= base_url('post') ?>" class="search-bar">
        <input type="text" class="form-control" name="q" placeholder="Cari judul, isi, atau author..." value="<?= esc($query ?? '') ?>">
        <button type="submit" class="btn btn-main">Cari</button>
        <?php if (!empty($query)) : ?>
            <a class="btn btn-muted" href="<?= base_url('post') ?>">Reset</a>
        <?php endif; ?>
    </form>
</section>

<section class="app-card page-section blog-feed">
    <h2 class="section-title">Semua Postingan Publish</h2>

    <?php if (!empty($posts)) : ?>
        <div class="post-grid mt-3">
            <?php foreach ($posts as $post) : ?>
                <?php $dateLabel = !empty($post['created_at']) ? date('d M Y', strtotime((string) $post['created_at'])) : 'Tanggal belum tersedia'; ?>
                <article class="post-card">
                    <div class="post-meta">
                        <?= esc($post['author'] ?? 'Anonim') ?> | <?= esc($dateLabel) ?> | <?= esc((string) ($post['reading_time'] ?? 1)) ?> menit baca
                    </div>
                    <h3 class="post-title">
                        <a href="<?= base_url('post/' . $post['slug']) ?>"><?= esc($post['title']) ?></a>
                    </h3>
                    <p class="mb-0"><?= esc($post['excerpt']) ?></p>
                    <div class="mt-3">
                        <a href="<?= base_url('post/' . $post['slug']) ?>" class="btn btn-soft">Baca Sekarang</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="empty-state mt-3">
            <h3>Belum ketemu postingan yang kamu cari</h3>
            <p>Coba ganti kata kunci lain, atau reset pencarian dulu biar semua post muncul lagi.</p>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
