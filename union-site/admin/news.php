<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$adminTitle = 'Новости и статьи';
$adminPage  = 'news';

// Удаление
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (verifyCsrf()) {
        $id = (int)($_POST['id'] ?? 0);
        $row = DB::fetchOne("SELECT image FROM news WHERE id = ?", [$id]);
        if ($row) {
            if ($row['image']) deleteUpload($row['image']);
            DB::execute("DELETE FROM news WHERE id = ?", [$id]);
            flash('success', 'Новость удалена.');
        }
    }
    redirect(BASE_URL . '/admin/news.php');
}

// Переключение публикации
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle') {
    if (verifyCsrf()) {
        $id = (int)($_POST['id'] ?? 0);
        DB::execute("UPDATE news SET published = 1 - published WHERE id = ?", [$id]);
        flash('success', 'Статус изменён.');
    }
    redirect(BASE_URL . '/admin/news.php');
}

$perPage = 15;
$page    = max(1, (int)($_GET['page'] ?? 1));
$search  = trim($_GET['q'] ?? '');
$where   = $search ? "WHERE title LIKE ?" : "";
$params  = $search ? ["%$search%"] : [];

$total = (int)DB::fetchOne("SELECT COUNT(*) as c FROM news $where", $params)['c'];
$pag   = paginate($total, $perPage, $page);
$news  = DB::fetchAll("SELECT * FROM news $where ORDER BY created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$perPage, $pag['offset']]));

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($msg = flash('success')): ?>
<div class="alert alert-success"><?= h($msg) ?></div>
<?php endif; ?>

<div class="page-header">
  <h2>Новости и статьи (<?= $total ?>)</h2>
  <div class="page-header-actions">
    <form method="GET" style="display:flex;gap:8px">
      <input type="text" name="q" class="form-control" placeholder="Поиск…" value="<?= h($search) ?>" style="width:220px">
      <button type="submit" class="btn btn-outline btn-sm">Найти</button>
      <?php if ($search): ?><a href="?" class="btn btn-sm btn-outline">✕</a><?php endif; ?>
    </form>
    <a href="<?= BASE_URL ?>/admin/news-edit.php" class="btn btn-primary">+ Добавить</a>
  </div>
</div>

<div class="card" style="padding:0">
  <table class="admin-table">
    <thead>
      <tr>
        <th style="width:50px">#</th>
        <th>Заголовок</th>
        <th>Категория</th>
        <th>Дата</th>
        <th>Статус</th>
        <th class="col-actions">Действия</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($news as $item): ?>
      <tr>
        <td style="color:var(--text-muted)"><?= $item['id'] ?></td>
        <td>
          <a href="<?= BASE_URL ?>/admin/news-edit.php?id=<?= $item['id'] ?>" style="font-weight:600">
            <?= h(truncate($item['title'], 60)) ?>
          </a>
          <?php if ($item['image']): ?>
          <span style="font-size:.75rem;color:var(--text-muted);margin-left:6px">📷</span>
          <?php endif; ?>
        </td>
        <td>
          <span class="badge badge-orange"><?= h(match($item['category']) {
            'events' => 'Мероприятие', 'achievements' => 'Достижение', 'announcements' => 'Объявление', default => 'Новость'
          }) ?></span>
        </td>
        <td style="color:var(--text-muted);font-size:.8125rem"><?= formatDate($item['created_at']) ?></td>
        <td>
          <form method="POST" style="display:inline">
            <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <button type="submit" class="badge <?= $item['published'] ? 'badge-green' : 'badge-gray' ?>" style="cursor:pointer;border:none">
              <?= $item['published'] ? 'Опублик.' : 'Черновик' ?>
            </button>
          </form>
        </td>
        <td class="col-actions">
          <a href="<?= BASE_URL ?>/admin/news-edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline btn-icon" title="Редактировать">✏️</a>
          <a href="<?= BASE_URL ?>/news-single.php?slug=<?= $item['slug'] ?>" target="_blank" class="btn btn-sm btn-outline btn-icon" title="На сайте">👁️</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Удалить новость?')">
            <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline btn-icon" title="Удалить" style="color:#ef4444">🗑️</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$news): ?>
      <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">Новостей не найдено</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <?php if ($pag['pages'] > 1): ?>
  <div class="admin-pag" style="padding:12px 16px">
    <a href="?page=<?= $page-1 ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="<?= $pag['prev'] ? '' : 'disabled' ?>">←</a>
    <?php for ($p = 1; $p <= $pag['pages']; $p++): ?>
    <a href="?page=<?= $p ?><?= $search ? '&q='.urlencode($search) : '' ?>" <?= $p === $page ? 'class="active"' : '' ?>><?= $p ?></a>
    <?php endfor; ?>
    <a href="?page=<?= $page+1 ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="<?= $pag['next'] ? '' : 'disabled' ?>">→</a>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
