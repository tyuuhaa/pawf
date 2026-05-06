<?php
$path = trim(service('uri')->getPath(), '/');
$isAdmin = str_starts_with($path, 'admin');

if (!function_exists('is_active_menu')) {
    function is_active_menu(string $currentPath, array $aliases): bool
    {
        foreach ($aliases as $alias) {
            if ($alias === '') {
                if ($currentPath === '') {
                    return true;
                }
                continue;
            }
            if ($currentPath === $alias || str_starts_with($currentPath, $alias . '/')) {
                return true;
            }
        }
        return false;
    }
}
?>

<nav class="navbar navbar-expand-lg sticky-top app-navbar">
  <div class="container">
    <a class="navbar-brand brand-mark" href="<?= base_url('/') ?>" aria-label="RuangCerita">
      <img src="<?= base_url('asset/ruangcerita.png') ?>" alt="RuangCerita">
    </a>

    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mb-3 mb-lg-0 nav-center-menu">
        <li class="nav-item"><a class="nav-link <?= is_active_menu($path, ['']) ? 'active' : '' ?>" href="<?= base_url('/') ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link <?= is_active_menu($path, ['post']) ? 'active' : '' ?>" href="<?= base_url('post') ?>">Blog</a></li>
        <li class="nav-item"><a class="nav-link <?= is_active_menu($path, ['about']) ? 'active' : '' ?>" href="<?= base_url('about') ?>">About</a></li>
        <li class="nav-item"><a class="nav-link <?= is_active_menu($path, ['contact']) ? 'active' : '' ?>" href="<?= base_url('contact') ?>">Contact</a></li>
        <li class="nav-item"><a class="nav-link <?= is_active_menu($path, ['faqs']) ? 'active' : '' ?>" href="<?= base_url('faqs') ?>">FAQ</a></li>
      </ul>

      <div class="d-flex align-items-center gap-2 nav-action-wrap">
        <button type="button" class="btn btn-soft reading-toggle-btn" data-reading-toggle>Mode Baca</button>
        <div class="reading-tint-control" aria-label="Intensitas mode baca">
          <span class="reading-tint-control__mark">-</span>
          <input type="range" min="0" max="100" value="35" data-reading-level>
          <span class="reading-tint-control__mark">+</span>
        </div>
        <?php if (logged_in()) : ?>
          <a class="btn btn-soft" href="<?= base_url($isAdmin ? 'post' : 'admin/post') ?>"><?= $isAdmin ? 'Lihat Publik' : 'Dashboard' ?></a>
          <a class="btn btn-main" href="<?= base_url('admin/post/new') ?>">Tulis Baru</a>
          <a class="btn btn-muted" href="<?= base_url('logout') ?>">Logout</a>
        <?php else : ?>
          <a class="btn btn-main" href="<?= base_url('login') ?>">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
