<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<section class="app-card page-section editor-card">
    <h2 class="section-title">Form Post Baru</h2>

    <?php if (isset($validation) && $validation->getErrors()) : ?>
        <div class="alert alert-danger rounded-3">
            <strong>Masih ada yang perlu dibenerin:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($validation->getErrors() as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="" method="post" id="postEditorForm">
        <?= csrf_field() ?>
        <div class="autosave-indicator mb-3" data-autosave-state>
            Autosave aktif. Draft lokal bakal kesimpan otomatis.
        </div>

        <div class="mb-3">
            <label class="input-label" for="title">Judul</label>
            <input id="title" type="text" name="title" class="form-control custom-field" placeholder="Contoh: Cara bikin halaman blog yang enak dibaca" value="<?= esc($old['title'] ?? '') ?>" required>
            <small class="muted-note">Bikin judul yang singkat tapi kebayang isi artikelnya.</small>
        </div>

        <div class="mb-3">
            <label class="input-label">Akun Penulis</label>
            <input type="text" class="form-control custom-field" value="<?= esc($currentAuthor ?? '-') ?>" disabled>
            <small class="muted-note">Draft hanya akan terlihat di akun ini.</small>
        </div>

        <div class="mb-3">
            <label class="input-label" for="content">Isi Artikel</label>
            <textarea id="content" name="content" rows="9" class="form-control custom-field" placeholder="Tulis isi artikel yang detail dan natural..." required><?= esc($old['content'] ?? '') ?></textarea>
            <small class="muted-note">Tulis versi awal dulu aja, revisi bisa kapan pun.</small>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="submit" name="status" value="published" class="btn btn-main">Publish Sekarang</button>
            <button type="submit" name="status" value="draft" class="btn btn-soft">Simpan sebagai Draft</button>
            <a href="<?= base_url('admin/post') ?>" class="btn btn-muted">Kembali</a>
        </div>
    </form>
</section>

<script>
(function () {
    var form = document.getElementById('postEditorForm');
    if (!form) return;

    var titleInput = form.querySelector('#title');
    var contentInput = form.querySelector('#content');
    var statusNode = form.querySelector('[data-autosave-state]');
    var submitButtons = Array.prototype.slice.call(form.querySelectorAll('button[type="submit"]'));
    var submitter = null;
    var autosaveKey = 'ruangcerita_autosave_create_<?= esc((string) ($currentAuthor ?? 'anonim'), 'js') ?>';
    var hasServerOld = <?= (!empty($old['title']) || !empty($old['content'])) ? 'true' : 'false' ?>;
    var saveTimer = null;

    function setState(message, tone) {
        if (!statusNode) return;
        statusNode.textContent = message;
        statusNode.classList.remove('is-saving', 'is-saved');
        if (tone) statusNode.classList.add(tone);
    }

    function formatTime(date) {
        try {
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        } catch (e) {
            return date.toLocaleTimeString();
        }
    }

    function saveDraft() {
        if (!titleInput || !contentInput) return;
        var payload = {
            title: titleInput.value,
            content: contentInput.value,
            savedAt: Date.now()
        };

        if (payload.title.trim() === '' && payload.content.trim() === '') {
            try { localStorage.removeItem(autosaveKey); } catch (e) {}
            setState('Autosave aktif. Mulai nulis, nanti otomatis kesimpan.', '');
            return;
        }

        setState('Menyimpan draft lokal...', 'is-saving');
        try {
            localStorage.setItem(autosaveKey, JSON.stringify(payload));
            setState('Tersimpan otomatis jam ' + formatTime(new Date(payload.savedAt)) + '.', 'is-saved');
        } catch (e) {
            setState('Gagal simpan draft lokal. Cek storage browser kamu.', '');
        }
    }

    function scheduleSave() {
        if (saveTimer) window.clearTimeout(saveTimer);
        saveTimer = window.setTimeout(saveDraft, 650);
    }

    try {
        var raw = localStorage.getItem(autosaveKey);
        if (raw && !hasServerOld && titleInput && contentInput && titleInput.value.trim() === '' && contentInput.value.trim() === '') {
            var parsed = JSON.parse(raw);
            if (parsed && (parsed.title || parsed.content)) {
                titleInput.value = String(parsed.title || '');
                contentInput.value = String(parsed.content || '');
                if (parsed.savedAt) {
                    setState('Draft lama dipulihkan (jam ' + formatTime(new Date(parsed.savedAt)) + ').', 'is-saved');
                } else {
                    setState('Draft lama dipulihkan.', 'is-saved');
                }
            }
        }
    } catch (e) {}

    if (titleInput) titleInput.addEventListener('input', scheduleSave);
    if (contentInput) contentInput.addEventListener('input', scheduleSave);

    submitButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            submitter = button;
        });
    });

    form.addEventListener('submit', function (event) {
        var activeButton = event.submitter || submitter;

        submitButtons.forEach(function (button) {
            button.disabled = true;
        });

        if (activeButton) {
            activeButton.dataset.originalText = activeButton.textContent;
            activeButton.textContent = activeButton.value === 'published' ? 'Mempublish...' : 'Menyimpan...';
        }

        try { localStorage.removeItem(autosaveKey); } catch (e) {}
    });
})();
</script>
<?= $this->endSection() ?>
