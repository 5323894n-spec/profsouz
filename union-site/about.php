<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$activeNav = 'about';
$pageTitle = 'О профсоюзе';

$page    = DB::fetchOne("SELECT content FROM pages WHERE slug = 'about'");
$members = DB::fetchAll("SELECT * FROM members WHERE published = 1 ORDER BY sort_order");
$orgs    = DB::fetchAll("SELECT * FROM organizations WHERE published = 1 ORDER BY sort_order LIMIT 20");

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumbs">
      <a href="<?= url() ?>">Главная</a>
      <span>›</span>
      <span>О профсоюзе</span>
    </div>
    <h1>О профсоюзе</h1>
    <p>Тверская областная организация профсоюза работников жизнеобеспечения</p>
  </div>
</div>

<!-- Введение -->
<section class="section">
  <div class="container">
    <div class="about-intro" style="padding:0">
      <div class="about-intro__text">
        <div class="section-label">Кто мы</div>
        <h2>Защищаем работников с <?= h(setting('site_since', '1957')) ?> года</h2>
        <?php if ($page && $page['content']): ?>
          <div class="article-content"><?= $page['content'] ?></div>
        <?php else: ?>
        <p><?= h(setting('about_short')) ?></p>
        <p style="margin-top:12px">Профсоюз объединяет работников жилищно-коммунального хозяйства, дорожного хозяйства, лесного комплекса и смежных отраслей Тверской области.</p>
        <?php endif; ?>
      </div>
      <div class="about-intro__img">
        <div class="about-intro__img-placeholder">🏛️</div>
      </div>
    </div>
  </div>
</section>

<!-- Статистика -->
<div class="hero-stats" style="margin:0;background:var(--bg-dark)">
  <div class="container">
    <div class="stat-item">
      <span class="stat-num"><?= h(setting('members_count')) ?></span>
      <div class="stat-label">членов профсоюза</div>
    </div>
    <div class="stat-item">
      <span class="stat-num"><?= h(setting('orgs_count')) ?></span>
      <div class="stat-label">первичных организаций</div>
    </div>
    <div class="stat-item">
      <span class="stat-num"><?= h(setting('coverage')) ?></span>
      <div class="stat-label">охват предприятий</div>
    </div>
    <div class="stat-item">
      <span class="stat-num"><?= h(setting('years_active')) ?></span>
      <div class="stat-label">на стороне работников</div>
    </div>
  </div>
</div>

<!-- Руководство -->
<?php if ($members): ?>
<section class="section section--alt">
  <div class="container">
    <div class="section-header">
      <div class="section-header-left">
        <div class="section-label">Команда</div>
        <h2 class="section-title">Руководство профсоюза</h2>
      </div>
    </div>
    <div class="leaders-grid">
      <?php foreach ($members as $m): ?>
      <div class="leader-card">
        <div class="leader-card__photo">
          <?php if ($m['photo']): ?>
            <img src="<?= uploadUrl($m['photo']) ?>" alt="<?= h($m['name']) ?>">
          <?php else: ?>
            <div class="leader-card__photo-placeholder">👤</div>
          <?php endif; ?>
        </div>
        <div class="leader-card__body">
          <div class="leader-card__name"><?= h($m['name']) ?></div>
          <?php if ($m['position']): ?>
          <div class="leader-card__pos"><?= h($m['position']) ?></div>
          <?php endif; ?>
          <?php if ($m['phone'] || $m['email']): ?>
          <div class="leader-card__contacts" style="margin-top:8px">
            <?php if ($m['phone']): ?>
            <div style="font-size:.8125rem;color:var(--text-muted)"><?= h($m['phone']) ?></div>
            <?php endif; ?>
            <?php if ($m['email']): ?>
            <div style="margin-top:2px"><a href="mailto:<?= h($m['email']) ?>"><?= h($m['email']) ?></a></div>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Что мы делаем -->
<section class="section">
  <div class="container">
    <div class="section-label">Деятельность</div>
    <h2 class="section-title" style="margin-bottom:40px">Направления работы</h2>
    <div class="advantages-grid">
      <div class="advantage-card">
        <div class="advantage-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3>Правовая защита</h3>
        <p>Правовые инспекторы профсоюза оказывают бесплатную юридическую помощь: консультации, составление документов, представление в суде.</p>
      </div>
      <div class="advantage-card">
        <div class="advantage-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
        </div>
        <h3>Коллективные договоры</h3>
        <p>Ведём переговоры с работодателями, заключаем коллективные договоры, контролируем их исполнение.</p>
      </div>
      <div class="advantage-card">
        <div class="advantage-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
        </div>
        <h3>Социальная поддержка</h3>
        <p>Материальная помощь членам профсоюза в трудных ситуациях, льготные путёвки в санатории, культурные мероприятия.</p>
      </div>
      <div class="advantage-card">
        <div class="advantage-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3>Охрана труда</h3>
        <p>Технические инспекторы труда контролируют условия труда на предприятиях и добиваются устранения нарушений.</p>
      </div>
      <div class="advantage-card">
        <div class="advantage-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <h3>Молодёжная политика</h3>
        <p>Работа с молодыми работниками отрасли, обучение актива, молодёжные мероприятия и конкурсы.</p>
      </div>
      <div class="advantage-card">
        <div class="advantage-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.12 1.22 2 2 0 012.11 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.09a16 16 0 006 6l.45-.45a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
        </div>
        <h3>Горячая линия</h3>
        <p>Оперативные консультации по телефону и личный приём граждан по трудовым вопросам.</p>
      </div>
    </div>
  </div>
</section>

<!-- Первичные организации -->
<?php if ($orgs): ?>
<section class="section section--alt">
  <div class="container">
    <div class="section-label">Структура</div>
    <h2 class="section-title" style="margin-bottom:32px">Первичные организации</h2>
    <div style="overflow-x:auto">
      <table class="orgs-table">
        <thead>
          <tr>
            <th>Организация</th>
            <th>Председатель</th>
            <th>Адрес</th>
            <th style="text-align:center">Членов</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orgs as $org): ?>
          <tr>
            <td style="font-weight:600"><?= h($org['name']) ?></td>
            <td><?= h($org['chairman'] ?? '—') ?></td>
            <td style="color:var(--text-muted)"><?= h($org['address'] ?? '—') ?></td>
            <td style="text-align:center"><span class="orgs-badge"><?= (int)$org['members_count'] ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
