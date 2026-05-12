<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$adminTitle = 'Главная';
$adminPage  = 'dashboard';

// Статистика
$statsNews    = (int)DB::fetchOne("SELECT COUNT(*) as c FROM news WHERE published = 1")['c'];
$statsGallery = (int)DB::fetchOne("SELECT COUNT(*) as c FROM gallery WHERE published = 1")['c'];
$statsForms   = (int)DB::fetchOne("SELECT COUNT(*) as c FROM contacts_form WHERE status = 'new'")['c'];
$statsMembers = (int)DB::fetchOne("SELECT COUNT(*) as c FROM members WHERE published = 1")['c'];

$latestNews  = DB::fetchAll("SELECT * FROM news ORDER BY created_at DESC LIMIT 5");
$latestForms = DB::fetchAll("SELECT * FROM contacts_form ORDER BY created_at DESC LIMIT 5");

require_once __DIR__ . '/includes/header.php';
?>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-card__icon stat-card__icon--blue">📰</div>
    <div>
      <div class="stat-card__num"><?= $statsNews ?></div>
      <div class="stat-card__label">Новостей</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card__icon stat-card__icon--green">📷</div>
    <div>
      <div class="stat-card__num"><?= $statsGallery ?></div>
      <div class="stat-card__label">Фото/видео</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card__icon stat-card__icon--orange" style="position:relative">📩
      <?php if ($statsForms): ?>
      <span style="position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;font-size:.6rem;padding:1px 4px;border-radius:8px;font-weight:700"><?= $statsForms ?></span>
      <?php endif; ?>
    </div>
    <div>
      <div class="stat-card__num"><?= $statsForms ?></div>
      <div class="stat-card__label">Новых обращений</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card__icon stat-card__icon--purple">👥</div>
    <div>
      <div class="stat-card__num"><?= $statsMembers ?></div>
      <div class="stat-card__label">Руководителей</div>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
  <!-- Последние новости -->
  <div class="card">
    <div class="page-header">
      <h2>Последние новости</h2>
      <a href="<?= BASE_URL ?>/admin/news.php" class="btn btn-sm btn-outline">Все →</a>
    </div>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Заголовок</th>
          <th>Дата</th>
          <th>Статус</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($latestNews as $n): ?>
        <tr>
          <td>
            <a href="<?= BASE_URL ?>/admin/news-edit.php?id=<?= $n['id'] ?>" style="font-weight:600;color:var(--text)">
              <?= h(truncate($n['title'], 45)) ?>
            </a>
          </td>
          <td style="color:var(--text-muted);font-size:.8125rem"><?= formatDate($n['created_at']) ?></td>
          <td><span class="badge <?= $n['published'] ? 'badge-green' : 'badge-gray' ?>"><?= $n['published'] ? 'Опубл.' : 'Черн.' ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$latestNews): ?>
        <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:20px">Новостей нет</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Последние обращения -->
  <div class="card">
    <div class="page-header">
      <h2>Новые обращения</h2>
      <a href="<?= BASE_URL ?>/admin/forms.php" class="btn btn-sm btn-outline">Все →</a>
    </div>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Имя</th>
          <th>Тема</th>
          <th>Дата</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($latestForms as $f): ?>
        <tr>
          <td>
            <a href="<?= BASE_URL ?>/admin/forms.php?id=<?= $f['id'] ?>" style="font-weight:600;color:var(--text)">
              <?= h($f['name']) ?>
              <?php if ($f['status'] === 'new'): ?>
              <span class="badge badge-blue" style="margin-left:4px">Новое</span>
              <?php endif; ?>
            </a>
          </td>
          <td style="color:var(--text-muted);font-size:.875rem"><?= h(truncate($f['subject'] ?: $f['message'], 30)) ?></td>
          <td style="color:var(--text-muted);font-size:.8125rem"><?= formatDate($f['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$latestForms): ?>
        <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:20px">Обращений нет</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div style="margin-top:24px" class="card">
  <h2 style="margin-bottom:16px;font-size:1rem">Быстрые действия</h2>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <a href="<?= BASE_URL ?>/admin/news-edit.php" class="btn btn-primary">+ Добавить новость</a>
    <a href="<?= BASE_URL ?>/admin/gallery-edit.php" class="btn btn-outline">+ Добавить фото</a>
    <a href="<?= BASE_URL ?>/admin/documents-edit.php" class="btn btn-outline">+ Загрузить документ</a>
    <a href="<?= BASE_URL ?>/admin/settings.php" class="btn btn-outline">Настройки сайта</a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
