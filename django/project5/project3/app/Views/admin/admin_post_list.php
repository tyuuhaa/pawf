<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php if (!empty($flash)) : ?>
    <div class="alert-soft mb-4"><?= esc($flash) ?></div>
<?php endif; ?>

<section class="mb-4">
    <div class="stats-grid">
        <article class="stats-card">
            <h4>Total Postingan</h4>
            <p><?= esc((string) ($stats['total'] ?? 0)) ?></p>
        </article>
        <article class="stats-card">
            <h4>Published</h4>
            <p><?= esc((string) ($stats['published'] ?? 0)) ?></p>
        </article>
        <article class="stats-card">
            <h4>Draft Kamu</h4>
            <p><?= esc((string) ($stats['draft'] ?? 0)) ?></p>
            <div class="progress-shell">
                <div class="progress-label">
                    <span>Progress publish</span>
                    <strong><?= esc((string) ($stats['completion'] ?? 0)) ?>%</strong>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width: <?= esc((string) ($stats['completion'] ?? 0)) ?>%;"></div>
                </div>
            </div>
        </article>
    </div>
</section>

<section class="app-card page-section mb-4">
    <h2 class="section-title mb-3">Data Pengguna</h2>
    <div class="user-summary-grid">
        <div class="user-summary-item">
            <span>Nama</span>
            <strong><?= esc((string) ($userProfile['name'] ?? '-')) ?></strong>
        </div>
        <div class="user-summary-item">
            <span>Email</span>
            <strong><?= esc((string) ($userProfile['emailMasked'] ?? '-')) ?></strong>
        </div>
    </div>
</section>

<section class="app-card page-section mb-4">
    <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-3">
        <div>
            <h2 class="section-title mb-1">Kelola Postingan</h2>
        </div>
        <a href="<?= base_url('admin/post/new') ?>" class="btn btn-main">+ Tulis Post Baru</a>
    </div>

    <form method="get" action="<?= base_url('admin/post') ?>" class="search-bar mb-3">
        <input type="text" class="form-control" name="q" placeholder="Cari title, author, atau isi..." value="<?= esc($query ?? '') ?>">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            <option value="published" <?= ($statusFilter ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
            <option value="draft" <?= ($statusFilter ?? '') === 'draft' ? 'selected' : '' ?>>Draft Kamu</option>
        </select>
        <select name="sort" class="form-select">
            <option value="newest" <?= ($sortFilter ?? 'newest') === 'newest' ? 'selected' : '' ?>>Terbaru dulu</option>
            <option value="oldest" <?= ($sortFilter ?? '') === 'oldest' ? 'selected' : '' ?>>Terlama dulu</option>
            <option value="title_asc" <?= ($sortFilter ?? '') === 'title_asc' ? 'selected' : '' ?>>Judul A-Z</option>
            <option value="title_desc" <?= ($sortFilter ?? '') === 'title_desc' ? 'selected' : '' ?>>Judul Z-A</option>
            <option value="status" <?= ($sortFilter ?? '') === 'status' ? 'selected' : '' ?>>Status dulu</option>
        </select>
        <button type="submit" class="btn btn-main">Terapkan</button>
        <a href="<?= base_url('admin/post') ?>" class="btn btn-muted">Reset</a>
    </form>

    <?php if (!empty($posts)) : ?>
        <div>
            <?php foreach ($posts as $post) : ?>
                <?php $dateLabel = !empty($post['created_at']) ? date('d M Y H:i', strtotime((string) $post['created_at'])) : 'Tanggal belum tersedia'; ?>
                <div class="admin-post-row">
                    <div>
                        <h3 class="post-title mb-1"><?= esc($post['title']) ?></h3>
                        <div class="post-meta mb-0">
                            by <?= esc($post['author'] ?? 'Anonim') ?> | <?= esc($dateLabel) ?>
                        </div>
                    </div>
                    <div class="admin-post-actions">
                        <span class="badge-status <?= $post['status'] === 'published' ? 'badge-published' : 'badge-draft' ?>">
                            <?= esc(strtoupper($post['status'])) ?>
                        </span>
                        <a href="<?= base_url('admin/post/' . $post['id'] . '/preview') ?>" class="btn btn-soft" target="_blank">Preview</a>
                        <a href="<?= base_url('admin/post/' . $post['id'] . '/edit') ?>" class="btn btn-soft">Edit</a>
                        <form method="post" action="<?= base_url('admin/post/' . $post['id'] . '/status') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-muted"><?= $post['status'] === 'published' ? 'Jadikan Draft' : 'Publish Sekarang' ?></button>
                        </form>
                        <form method="post" action="<?= base_url('admin/post/' . $post['id'] . '/delete') ?>" class="d-inline delete-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger-soft">Hapus</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="empty-state">
            <h3>Belum ada data sesuai filter</h3>
            <p>Yuk cek lagi kata kunci atau statusnya. Kalau masih kosong, saatnya bikin post baru.</p>
        </div>
    <?php endif; ?>
</section>

<section class="app-card page-section">
    <h2 class="section-title">Timeline Aktivitas Konten</h2>

    <?php if (!empty($recentTimeline)) : ?>
        <ul class="timeline">
            <?php foreach ($recentTimeline as $timeline) : ?>
                <?php $timelineDate = !empty($timeline['created_at']) ? date('d M Y H:i', strtotime((string) $timeline['created_at'])) : 'Tanggal belum tersedia'; ?>
                <li>
                    <div class="timeline-title"><?= esc($timeline['title']) ?></div>
                    <div class="timeline-meta">
                        by <?= esc($timeline['author']) ?> | <?= esc($timelineDate) ?> | <?= esc(strtoupper($timeline['status'])) ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <div class="empty-state">
            <h3>Timeline masih sepi</h3>
            <p>Nanti setiap post yang kamu buat akan otomatis muncul di sini.</p>
        </div>
    <?php endif; ?>
</section>

<script>
document.querySelectorAll('.delete-form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
        if (!confirm('Yakin mau hapus post ini? Aksi ini nggak bisa dibatalin.')) {
            event.preventDefault();
        }
    });
});
</script>
<?= $this->endSection() ?>
