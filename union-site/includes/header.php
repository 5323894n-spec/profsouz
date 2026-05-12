<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($pageTitle ?? setting('site_name')) ?><?= isset($pageTitle) ? ' — ' . h(setting('site_name')) : '' ?></title>
  <meta name="description" content="<?= h($pageDesc ?? setting('site_tagline')) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Onest:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

<header class="site-header" id="site-header">
  <div class="container">
    <a href="<?= url() ?>" class="brand">
      <div class="brand-mark">П69</div>
      <div class="brand-text">
        <div class="brand-name"><?= h(setting('site_name')) ?></div>
        <div class="brand-sub"><?= h(setting('site_tagline')) ?></div>
      </div>
    </a>

    <nav class="main-nav" id="main-nav">
      <a href="<?= url() ?>" class="nav-link<?= ($activeNav ?? '') === 'home' ? ' active' : '' ?>">Главная</a>
      <a href="<?= url('news.php') ?>" class="nav-link<?= ($activeNav ?? '') === 'news' ? ' active' : '' ?>">Новости</a>
      <a href="<?= url('about.php') ?>" class="nav-link<?= ($activeNav ?? '') === 'about' ? ' active' : '' ?>">О профсоюзе</a>
      <a href="<?= url('docs.php') ?>" class="nav-link<?= ($activeNav ?? '') === 'docs' ? ' active' : '' ?>">Документы</a>
      <a href="<?= url('help.php') ?>" class="nav-link<?= ($activeNav ?? '') === 'help' ? ' active' : '' ?>">Помощь членам</a>
      <a href="<?= url('contacts.php') ?>" class="nav-link<?= ($activeNav ?? '') === 'contacts' ? ' active' : '' ?>">Контакты</a>
    </nav>

    <div class="header-actions">
      <a href="<?= url('contacts.php') ?>" class="btn btn-primary btn-sm">Обратиться</a>
      <button class="burger" id="burger" aria-label="Меню">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<div class="mobile-menu" id="mobile-menu">
  <a href="<?= url() ?>">Главная</a>
  <a href="<?= url('news.php') ?>">Новости</a>
  <a href="<?= url('about.php') ?>">О профсоюзе</a>
  <a href="<?= url('docs.php') ?>">Документы</a>
  <a href="<?= url('help.php') ?>">Помощь членам</a>
  <a href="<?= url('contacts.php') ?>">Контакты</a>
</div>

<div class="page-wrap">
