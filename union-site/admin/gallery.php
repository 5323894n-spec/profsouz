<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$adminTitle = 'Галерея';
$adminPage  = 'gallery';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) { redirect(BASE_URL . '/admin/gallery.php'); }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $row = DB::fetchOne("SELECT file FROM gallery WHERE id = ?", [$id]);
        if ($row) {
            deleteUpload($row['file']);
            DB::execute("DELETE FROM gallery WHERE id = ?", [$id]);
            flash('success', 'Элемент удалён.');
        }
        redirect(BASE_URL . '/admin/gallery.php');
    }

    if ($action === 'toggle') {
        DB::execute("UPDATE gallery SET published = 1 - published WHERE id = ?", [(int)$_POST['id']]);
        redirect(BASE_URL . '/admin/gallery.php');
    }

    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $type  = $_POST['type'] ?? 'photo';
        $album = trim($_POST['album'] ?? '');
        $videoUrl = trim($_POST['video_url'] ?? '');

        if (!$title) {
            flash('error', 'Введите название.');
            redirect(BASE_URL . '/admin/gallery-edit.php');
        }

        if ($type === 'video' && $videoUrl) {
            DB::insert("INSERT INTO gallery (title, description, file, type, album) VALUES (?, ?, ?, 'video', ?)",
                [$title, $desc, $videoUrl, $album]);
            flash('success', 'Видео добавлено.');
        } elseif (!empty($_FILES['file']['name'])) {
            $uploaded = uploadFile($_FILES['file'], 'gallery', ['image/jpeg','image/png','image/gif','image/webp']);
            if ($uploaded) {
                DB::insert("INSERT INTO gallery (title, description, file, type, album) VALUES (?, ?, ?, 'photo', ?)",
                    [$title, $desc, $uploaded, $album]);
                flash('success', 'Фото добавлено.');
            } else {
                flash('error', 'Ошибка загрузки. Допустимые форматы: JPG, PNG, GIF, WebP.');
            }
        } else {
            flash('error', 'Загрузите файл или введите URL видео.');
        }
        redirect(BASE_URL . '/admin/gallery.php');
    }
}

$perPage = 20;
$page    = max(1, (int)($_GET['page'] ?? 1));
$total   = (int)DB::fetchOne("SELECT COUNT(*) as c FROM gallery")['c'];
$pag     = paginate($total, $perPage, $page);
$items   = DB::fetchAll("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC LIMIT ? OFFSET ?",
    [$perPage, $pag['offset']]);

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($msg = flash('success')): ?><div class="alert alert-success">✅ <?= h($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert alert-error">⚠️ <?= h($msg) ?></div><?php endif; ?>

<div class="page-header">
  <h2>Галерея (<?= $total ?>)</h2>
  <a href="<?= BASE_URL ?>/admin/gallery-edit.php" class="btn btn-primary">+ Добавить</a>
</div>

<?php if ($items): ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px">
  <?php foreach ($items as $item): ?>
  <div class="card" style="padding:0;overflow:hidden">
    <?php if ($item['type'] === 'photo'): ?>
    <div style="aspect-ratio:4/3;overflow:hidden;background:#f3f4f6">
      <img src="<?= BASE_URL ?>/uploads/<?= h($item['file']) ?>" alt="<?= h($item['title']) ?>"
           style="width:100%;height:100%;object-fit:cover">
    </div>
    <?php else: ?>
    <div style="aspect-ratio:4/3;background:#1c2530;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.6);font-size:2rem">
      ▶
    </div>
    <?php endif; ?>
    <div style="padding:12px">
      <div style="font-size:.8125rem;font-weight:600;margin-bottom:4px"><?= h(truncate($item['title'], 30)) ?></div>
      <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:10px"><?= formatDate($item['created_at']) ?></div>
      <div style="display:flex;gap:6px">
        <form method="POST" style="display:inline">
          <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="id" value="<?= $item['id'] ?>">
          <button type="submit" class="btn btn-sm <?= $item['published'] ? 'btn-success' : 'btn-outline' ?>"
                  style="font-size:.75rem;padding:4px 8px">
            <?= $item['published'] ? '✓ Показан' : '○ Скрыт' ?>
          </button>
        </form>
        <form method="POST" style="display:inline" onsubmit="return confirm('Удалить?')">
          <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= $item['id'] ?>">
          <button type="submit" class="btn btn-sm btn-outline" style="color:#ef4444;padding:4px 8px;font-size:.75rem">🗑️</button>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div class="card" style="text-align:center;padding:60px">
  <div style="font-size:2.5rem;margin-bottom:12px">📷</div>
  <h3>Галерея пуста</h3>
  <p style="color:var(--text-muted);margin-bottom:20px">Добавьте первую фотографию или видео.</p>
  <a href="<?= BASE_URL ?>/admin/gallery-edit.php" class="btn btn-primary">+ Добавить</a>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
