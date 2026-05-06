<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | RuangCerita</title>
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
    <main class="container py-5">
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-soft reading-toggle-btn me-2" data-reading-toggle>Mode Baca</button>
            <div class="reading-tint-control me-2" aria-label="Intensitas mode baca">
                <span class="reading-tint-control__mark">-</span>
                <input type="range" min="0" max="100" value="35" data-reading-level>
                <span class="reading-tint-control__mark">+</span>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <section class="app-card page-section editor-card">
                    <h1 class="section-title text-center mb-3">Kamu belum login</h1>

                    <?php
                    $tab = $initialTab ?? 'login';
                    if (session('errors') || session('error')) {
                        $oldLogin = old('login');
                        $oldEmail = old('email');
                        $tab = ($oldEmail || (!$oldLogin && $tab === 'register')) ? 'register' : $tab;
                    }
                    ?>

                    <div class="auth-tabs">
                        <button type="button" class="auth-tab-btn <?= $tab === 'login' ? 'active' : '' ?>" data-auth-tab-trigger="login">Login</button>
                        <button type="button" class="auth-tab-btn <?= $tab === 'register' ? 'active' : '' ?>" data-auth-tab-trigger="register">Daftar</button>
                    </div>

                    <?php if (session()->has('message')) : ?>
                        <div class="alert-soft mb-3"><?= session('message') ?></div>
                    <?php endif; ?>
                    <?php if (session()->has('error')) : ?>
                        <div class="alert alert-danger rounded-3 mb-3"><?= session('error') ?></div>
                    <?php endif; ?>

                    <section data-auth-tab="login" class="<?= $tab === 'login' ? '' : 'd-none' ?>">
                        <form action="<?= url_to('login') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="input-label" for="login">Email atau Username</label>
                                <input id="login" type="text" name="login" class="form-control custom-field <?= session('errors.login') ? 'is-invalid' : '' ?>" value="<?= old('login') ?>" placeholder="Email atau username">
                                <?php if (session('errors.login')) : ?>
                                    <div class="invalid-feedback"><?= session('errors.login') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="input-label" for="password">Password</label>
                                <input id="password" type="password" name="password" class="form-control custom-field <?= session('errors.password') ? 'is-invalid' : '' ?>" placeholder="Password">
                                <?php if (session('errors.password')) : ?>
                                    <div class="invalid-feedback"><?= session('errors.password') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <button type="submit" class="btn btn-main">Login</button>
                            </div>

                            <?php if ($config->activeResetter) : ?>
                                <p class="muted-note mb-0">Lupa password? <a href="<?= url_to('forgot') ?>">Reset di sini</a>.</p>
                            <?php endif; ?>
                        </form>
                    </section>

                    <section data-auth-tab="register" class="<?= $tab === 'register' ? '' : 'd-none' ?>">
                        <form id="register-form-combined" action="<?= url_to('register') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="input-label" for="username">Nama Lengkap</label>
                                <input id="username" type="text" name="username" class="form-control custom-field <?= session('errors.username') ? 'is-invalid' : '' ?>" value="<?= old('username') ?>" placeholder="Nama Lengkap">
                                <?php if (session('errors.username')) : ?>
                                    <div class="invalid-feedback"><?= session('errors.username') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="input-label" for="phone_display">Nomor HP</label>
                                <input id="phone_display" type="text" name="phone_display" class="form-control custom-field" value="<?= old('phone_display') ?>" placeholder="Nomor HP">
                            </div>

                            <div class="mb-3">
                                <label class="input-label" for="email">Email address</label>
                                <input id="email" type="email" name="email" class="form-control custom-field <?= session('errors.email') ? 'is-invalid' : '' ?>" value="<?= old('email') ?>" placeholder="Email">
                                <?php if (session('errors.email')) : ?>
                                    <div class="invalid-feedback"><?= session('errors.email') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="input-label" for="register_password">Password</label>
                                <input id="register_password" type="password" name="password" class="form-control custom-field <?= session('errors.password') ? 'is-invalid' : '' ?>" placeholder="Password">
                                <input id="pass_confirm" type="hidden" name="pass_confirm" value="">
                                <?php if (session('errors.password')) : ?>
                                    <div class="invalid-feedback"><?= session('errors.password') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-main">Daftar</button>
                            </div>
                        </form>
                    </section>
                </section>
            </div>
        </div>
    </main>

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

            function activateTab(tab) {
                document.querySelectorAll('[data-auth-tab-trigger]').forEach(function (btn) {
                    btn.classList.toggle('active', btn.getAttribute('data-auth-tab-trigger') === tab);
                });
                document.querySelectorAll('[data-auth-tab]').forEach(function (panel) {
                    panel.classList.toggle('d-none', panel.getAttribute('data-auth-tab') !== tab);
                });
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

            document.querySelectorAll('[data-auth-tab-trigger]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    activateTab(btn.getAttribute('data-auth-tab-trigger'));
                });
            });

            var registerForm = document.getElementById('register-form-combined');
            if (registerForm) {
                registerForm.addEventListener('submit', function () {
                    var pass = document.getElementById('register_password');
                    var confirm = document.getElementById('pass_confirm');
                    if (pass && confirm) {
                        confirm.value = pass.value;
                    }
                });
            }
        })();
    </script>
</body>
</html>
