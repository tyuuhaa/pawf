<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<section class="app-card reading-card page-section mb-4">
    <?php $detailDate = !empty($post['created_at']) ? date('d M Y H:i', strtotime((string) $post['created_at'])) : 'Tanggal belum tersedia'; ?>
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <span class="badge-status <?= ($post['status'] ?? '') === 'published' ? 'badge-published' : 'badge-draft' ?>">
            <?= esc(strtoupper((string) ($post['status'] ?? 'draft'))) ?>
        </span>
        <span class="muted-note">By <?= esc($post['author'] ?? 'Anonim') ?></span>
        <span class="muted-note">| <?= esc($detailDate) ?></span>
        <span class="muted-note">| <?= esc((string) ($reading_time ?? 1)) ?> menit baca</span>
    </div>
    <article class="lh-lg">
        <?= nl2br(esc((string) ($post['content'] ?? ''))) ?>
    </article>
    <div class="mt-4 d-flex flex-wrap gap-2">
        <a href="<?= base_url('post') ?>" class="btn btn-soft">Kembali ke daftar post</a>
        <?php if (logged_in()) : ?>
            <a href="<?= base_url('admin/post') ?>" class="btn btn-main">Kelola di Dashboard</a>
        <?php endif; ?>
    </div>
</section>

<section class="app-card page-section">
    <h2 class="section-title">Kamu mungkin suka ini juga</h2>

    <?php if (!empty($relatedPosts)) : ?>
        <div class="post-grid mt-3">
            <?php foreach ($relatedPosts as $item) : ?>
                <article class="post-card">
                    <h3 class="post-title"><a href="<?= base_url('post/' . $item['slug']) ?>"><?= esc($item['title']) ?></a></h3>
                    <p class="mb-0"><?= esc($item['excerpt']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="empty-state mt-3">
            <h3>Belum ada related post</h3>
            <p>Nanti kalau artikel makin banyak, rekomendasi otomatis ikut nambah.</p>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
