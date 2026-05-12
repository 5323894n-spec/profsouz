<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$adminTitle = 'Документы';
$adminPage  = 'documents';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $row = DB::fetchOne("SELECT file FROM documents WHERE id = ?", [$id]);
        if ($row) { deleteUpload($row['file']); DB::execute("DELETE FROM documents WHERE id = ?", [$id]); }
        flash('success', 'Документ удалён.');
        redirect(BASE_URL . '/admin/documents.php');
    }

    if ($action === 'toggle') {
        DB::execute("UPDATE documents SET published = 1 - published WHERE id = ?", [(int)$_POST['id']]);
        redirect(BASE_URL . '/admin/documents.php');
    }
}

$docs = DB::fetchAll("SELECT * FROM documents ORDER BY category, sort_order, created_at DESC");

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($msg = flash('success')): ?><div class="alert alert-success">✅ <?= h($msg) ?></div><?php endif; ?>

<div class="page-header">
  <h2>Документы (<?= count($docs) ?>)</h2>
  <a href="<?= BASE_URL ?>/admin/documents-edit.php" class="btn btn-primary">+ Загрузить</a>
</div>

<div class="card" style="padding:0">
  <table class="admin-table">
    <thead><tr><th>Название</th><th>Категория</th><th>Файл</th><th>Дата</th><th>Статус</th><th class="col-actions">Действия</th></tr></thead>
    <tbody>
      <?php foreach ($docs as $d): ?>
      <tr>
        <td style="font-weight:600"><?= h($d['title']) ?></td>
        <td style="font-size:.8125rem;color:var(--text-muted)"><?= h($d['category']) ?></td>
        <td style="font-size:.8125rem;color:var(--text-muted)"><?= strtoupper(pathinfo($d['file'], PATHINFO_EXTENSION)) ?></td>
        <td style="font-size:.8125rem;color:var(--text-muted)"><?= formatDate($d['created_at']) ?></td>
        <td>
          <form method="POST" style="display:inline">
            <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" value="<?= $d['id'] ?>">
            <button type="submit" class="badge <?= $d['published'] ? 'badge-green' : 'badge-gray' ?>" style="cursor:pointer;border:none">
              <?= $d['published'] ? 'Опублик.' : 'Скрыт' ?>
            </button>
          </form>
        </td>
        <td class="col-actions">
          <a href="<?= BASE_URL ?>/uploads/<?= h($d['file']) ?>" target="_blank" class="btn btn-sm btn-outline btn-icon" title="Скачать">📥</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Удалить?')">
            <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $d['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline btn-icon" style="color:#ef4444">🗑️</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$docs): ?><tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">Документов нет. <a href="<?= BASE_URL ?>/admin/documents-edit.php" style="color:var(--accent)">Загрузить первый</a></td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
