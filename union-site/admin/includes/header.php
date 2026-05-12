<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($adminTitle ?? 'Администрирование') ?> — Профсоюз 69</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/admin.css">
</head>
<body>

<div class="admin-layout">
  <!-- Боковая панель -->
  <aside class="admin-sidebar" id="admin-sidebar">
    <div class="sidebar-header">
      <div class="sidebar-brand">
        <div class="sidebar-brand-mark">П69</div>
        <div>
          <div class="sidebar-brand-name">Профсоюз 69</div>
          <div class="sidebar-brand-sub">Панель управления</div>
        </div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-label">Контент</div>
      <a href="<?= BASE_URL ?>/admin/" class="nav-item<?= ($adminPage ?? '') === 'dashboard' ? ' active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Главная
      </a>
      <a href="<?= BASE_URL ?>/admin/news.php" class="nav-item<?= ($adminPage ?? '') === 'news' ? ' active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        Новости и статьи
      </a>
      <a href="<?= BASE_URL ?>/admin/gallery.php" class="nav-item<?= ($adminPage ?? '') === 'gallery' ? ' active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        Галерея
      </a>
      <a href="<?= BASE_URL ?>/admin/documents.php" class="nav-item<?= ($adminPage ?? '') === 'documents' ? ' active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
        Документы
      </a>

      <div class="nav-section-label" style="margin-top:8px">Организация</div>
      <a href="<?= BASE_URL ?>/admin/members.php" class="nav-item<?= ($adminPage ?? '') === 'members' ? ' active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        Руководство
      </a>
      <a href="<?= BASE_URL ?>/admin/organizations.php" class="nav-item<?= ($adminPage ?? '') === 'organizations' ? ' active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
        Организации
      </a>
      <a href="<?= BASE_URL ?>/admin/help.php" class="nav-item<?= ($adminPage ?? '') === 'help' ? ' active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        FAQ / Помощь
      </a>

      <div class="nav-section-label" style="margin-top:8px">Обращения</div>
      <a href="<?= BASE_URL ?>/admin/forms.php" class="nav-item<?= ($adminPage ?? '') === 'forms' ? ' active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        Обращения
        <?php
        $newForms = DB::fetchOne("SELECT COUNT(*) as c FROM contacts_form WHERE status = 'new'");
        if ($newForms && $newForms['c'] > 0):
        ?>
        <span class="nav-badge"><?= $newForms['c'] ?></span>
        <?php endif; ?>
      </a>

      <div class="nav-section-label" style="margin-top:8px">Система</div>
      <a href="<?= BASE_URL ?>/admin/settings.php" class="nav-item<?= ($adminPage ?? '') === 'settings' ? ' active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M5 5a10 10 0 000 14"/></svg>
        Настройки сайта
      </a>
      <a href="<?= BASE_URL ?>/admin/password.php" class="nav-item<?= ($adminPage ?? '') === 'password' ? ' active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        Сменить пароль
      </a>
    </nav>

    <div class="sidebar-footer">
      <a href="<?= BASE_URL ?>/" target="_blank" class="sidebar-footer-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        На сайт
      </a>
      <a href="<?= BASE_URL ?>/admin/logout.php" class="sidebar-footer-link sidebar-footer-link--danger">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Выйти
      </a>
    </div>
  </aside>

  <!-- Основная область -->
  <main class="admin-main">
    <div class="admin-topbar">
      <button class="sidebar-toggle" id="sidebar-toggle">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <h1 class="admin-page-title"><?= h($adminTitle ?? '') ?></h1>
      <div class="admin-topbar-right">
        <span class="admin-user">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <?= h($_SESSION['admin_name'] ?? 'Администратор') ?>
        </span>
      </div>
    </div>

    <div class="admin-content">
