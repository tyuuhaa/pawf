<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<section class="app-card page-section editor-card">
    <h2 class="section-title">Edit Postingan</h2>

    <?php if (isset($validation) && $validation->getErrors()) : ?>
        <div class="alert alert-danger rounded-3">
            <strong>Masih ada input yang belum pas:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($validation->getErrors() as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="" method="post" id="postEditorForm">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= esc((string) $post['id']) ?>">
        <div class="autosave-indicator mb-3" data-autosave-state>
            Autosave aktif. Revisi kamu bakal kesimpan otomatis.
        </div>

        <div class="mb-3">
            <label class="input-label" for="title">Judul</label>
            <input id="title" type="text" name="title" class="form-control custom-field" value="<?= esc($post['title']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="input-label">Akun Penulis</label>
            <input type="text" class="form-control custom-field" value="<?= esc($post['author'] ?? '-') ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="input-label" for="content">Isi Artikel</label>
            <textarea id="content" name="content" rows="9" class="form-control custom-field" required><?= esc($post['content']) ?></textarea>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="submit" name="status" value="published" class="btn btn-main">Update & Publish</button>
            <button type="submit" name="status" value="draft" class="btn btn-soft">Update & Draft</button>
            <a href="<?= base_url('admin/post') ?>" class="btn btn-muted">Batal</a>
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
    var postId = '<?= esc((string) ($post['id'] ?? '0'), 'js') ?>';
    var autosaveKey = 'ruangcerita_autosave_edit_' + postId;
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

        setState('Menyimpan revisi lokal...', 'is-saving');
        try {
            localStorage.setItem(autosaveKey, JSON.stringify(payload));
            setState('Revisi otomatis tersimpan jam ' + formatTime(new Date(payload.savedAt)) + '.', 'is-saved');
        } catch (e) {
            setState('Gagal simpan revisi lokal. Cek storage browser kamu.', '');
        }
    }

    function scheduleSave() {
        if (saveTimer) window.clearTimeout(saveTimer);
        saveTimer = window.setTimeout(saveDraft, 650);
    }

    try {
        var raw = localStorage.getItem(autosaveKey);
        if (raw) {
            var parsed = JSON.parse(raw);
            var savedTitle = String(parsed && parsed.title ? parsed.title : '');
            var savedContent = String(parsed && parsed.content ? parsed.content : '');
            var currentTitle = titleInput ? titleInput.value : '';
            var currentContent = contentInput ? contentInput.value : '';

            if ((savedTitle !== '' || savedContent !== '') && (savedTitle !== currentTitle || savedContent !== currentContent)) {
                if (titleInput) titleInput.value = savedTitle;
                if (contentInput) contentInput.value = savedContent;
                if (parsed && parsed.savedAt) {
                    setState('Revisi terakhir dipulihkan (jam ' + formatTime(new Date(parsed.savedAt)) + ').', 'is-saved');
                } else {
                    setState('Revisi terakhir dipulihkan.', 'is-saved');
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
            activeButton.textContent = activeButton.value === 'published' ? 'Mengupdate...' : 'Menyimpan...';
        }

        try { localStorage.removeItem(autosaveKey); } catch (e) {}
    });
})();
</script>
<?= $this->endSection() ?>
