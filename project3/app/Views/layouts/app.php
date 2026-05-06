<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'RuangCerita') ?></title>
    <script>
        (function () {
            try {
                var savedMode = localStorage.getItem('ruangcerita_reading_mode');
                var legacyTheme = localStorage.getItem('ruangcerita_theme');
                var isReading = savedMode === 'on' || legacyTheme === 'reading';
                document.documentElement.setAttribute('data-theme', isReading ? 'reading' : 'light');
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    <link rel="stylesheet" href="<?= base_url('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
</head>
<body>
    <?= $this->include('layouts/navbar') ?>

    <header class="hero-wrap">
        <div class="container">
            <div class="hero-card">
                <h1 class="hero-title"><?= esc($heroTitle ?? 'Selamat datang di RuangCerita') ?></h1>
                <?php if (!empty($heroSubtitle)) : ?>
                    <p class="hero-subtitle"><?= esc($heroSubtitle) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container content-wrap">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="container pb-4 pt-5">
        <div class="footer-shell">
            <span>&copy; <?= date('Y') ?> RuangCerita</span>
            <span>Tulis yang penting, publish pas siap.</span>
        </div>
    </footer>

    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        (function () {
            function setReadingMode(enabled) {
                var mode = enabled ? 'reading' : 'light';
                document.documentElement.setAttribute('data-theme', mode);
                try {
                    localStorage.setItem('ruangcerita_reading_mode', enabled ? 'on' : 'off');
                    localStorage.setItem('ruangcerita_theme', mode);
                } catch (e) {}

                document.querySelectorAll('[data-reading-toggle]').forEach(function (btn) {
                    btn.classList.toggle('btn-main', enabled);
                    btn.classList.toggle('btn-soft', !enabled);
                });
            }

            function setReadingLevel(value) {
                var numeric = Number(value);
                if (Number.isNaN(numeric)) numeric = 35;
                if (numeric < 0) numeric = 0;
                if (numeric > 100) numeric = 100;

                var tint = (numeric / 100) * 0.55;
                document.documentElement.style.setProperty('--reading-tint', tint.toFixed(2));

                document.querySelectorAll('[data-reading-level]').forEach(function (slider) {
                    slider.value = String(numeric);
                });

                try {
                    localStorage.setItem('ruangcerita_reading_level', String(numeric));
                } catch (e) {}
            }

            var currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            setReadingMode(currentTheme === 'reading');
            var savedLevel = 35;
            try {
                savedLevel = Number(localStorage.getItem('ruangcerita_reading_level') || 35);
            } catch (e) {}
            setReadingLevel(savedLevel);

            document.querySelectorAll('[data-reading-toggle]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var nextEnabled = (document.documentElement.getAttribute('data-theme') || 'light') !== 'reading';
                    setReadingMode(nextEnabled);
                });
            });

            document.querySelectorAll('[data-reading-level]').forEach(function (slider) {
                slider.addEventListener('input', function () {
                    if ((document.documentElement.getAttribute('data-theme') || 'light') !== 'reading') {
                        setReadingMode(true);
                    }
                    setReadingLevel(slider.value);
                });
            });
        })();
    </script>
</body>
</html>
