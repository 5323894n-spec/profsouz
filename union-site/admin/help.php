<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$adminTitle = 'FAQ / Помощь';
$adminPage  = 'help';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'delete') {
        DB::execute("DELETE FROM help_items WHERE id = ?", [$id]);
        flash('success', 'Вопрос удалён.');
        redirect(BASE_URL . '/admin/help.php');
    }
    if ($action === 'save') {
        $q = trim($_POST['question'] ?? '');
        $a = trim($_POST['answer'] ?? '');
        $cat = $_POST['category'] ?? 'general';
        $sort = (int)($_POST['sort_order'] ?? 0);
        $pub = (int)isset($_POST['published']);
        if ($q && $a) {
            if ($id) {
                DB::execute("UPDATE help_items SET question=?,answer=?,category=?,sort_order=?,published=? WHERE id=?",
                    [$q,$a,$cat,$sort,$pub,$id]);
            } else {
                DB::insert("INSERT INTO help_items (question,answer,category,sort_order,published) VALUES (?,?,?,?,?)",
                    [$q,$a,$cat,$sort,$pub]);
            }
            flash('success', 'Сохранено.');
        }
        redirect(BASE_URL . '/admin/help.php');
    }
}

$editing = isset($_GET['edit']) ? DB::fetchOne("SELECT * FROM help_items WHERE id=?", [(int)$_GET['edit']]) : null;
$adding = isset($_GET['add']);
$items = DB::fetchAll("SELECT * FROM help_items ORDER BY category, sort_order");

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($msg = flash('success')): ?><div class="alert alert-success">✅ <?= h($msg) ?></div><?php endif; ?>

<?php if ($editing || $adding): ?>
<div style="margin-bottom:16px"><a href="?" style="color:var(--text-muted);font-size:.875rem">← Назад</a></div>
<div class="card" style="max-width:600px">
  <h2 style="font-size:1.125rem;margin-bottom:20px"><?= $editing ? 'Редактировать вопрос' : 'Добавить вопрос' ?></h2>
  <form method="POST">
    <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
    <input type="hidden" name="action" value="save">
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"><?php endif; ?>
    <div class="form-group">
      <label class="form-label">Вопрос <span style="color:var(--accent)">*</span></label>
      <input type="text" name="question" class="form-control" value="<?= h($editing['question'] ?? '') ?>" required>
    </div>
    <div class="form-group">
      <label class="form-label">Ответ <span style="color:var(--accent)">*</span></label>
      <textarea name="answer" class="form-control" rows="5" required><?= h($editing['answer'] ?? '') ?></textarea>
    </div>
    <div class="form-row form-row-2">
      <div class="form-group" style="margin:0">
        <label class="form-label">Категория</label>
        <select name="category" class="form-control">
          <option value="general" <?= ($editing['category'] ?? '') === 'general' ? 'selected' : '' ?>>Общие</option>
          <option value="membership" <?= ($editing['category'] ?? '') === 'membership' ? 'selected' : '' ?>>Вступление</option>
          <option value="legal" <?= ($editing['category'] ?? '') === 'legal' ? 'selected' : '' ?>>Юрпомощь</option>
          <option value="support" <?= ($editing['category'] ?? '') === 'support' ? 'selected' : '' ?>>Поддержка</option>
          <option value="rights" <?= ($editing['category'] ?? '') === 'rights' ? 'selected' : '' ?>>Трудовые права</option>
        </select>
      </div>
      <div class="form-group" style="margin:0">
        <label class="form-label">Порядок</label>
        <input type="number" name="sort_order" class="form-control" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
      </div>
    </div>
    <div class="form-check" style="margin-bottom:20px">
      <input type="checkbox" name="published" id="pub" value="1" <?= ($editing['published'] ?? 1) ? 'checked' : '' ?>>
      <label for="pub">Опубликовать</label>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">💾 Сохранить</button>
      <a href="?" class="btn btn-outline">Отмена</a>
    </div>
  </form>
</div>

<?php else: ?>
<div class="page-header">
  <h2>FAQ (<?= count($items) ?>)</h2>
  <a href="?add=1" class="btn btn-primary">+ Добавить вопрос</a>
</div>
<div class="card" style="padding:0">
  <table class="admin-table">
    <thead><tr><th>Вопрос</th><th>Категория</th><th>Статус</th><th class="col-actions">Действия</th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td style="font-weight:600"><?= h(truncate($item['question'], 70)) ?></td>
        <td style="font-size:.8125rem;color:var(--text-muted)"><?= h($item['category']) ?></td>
        <td><span class="badge <?= $item['published'] ? 'badge-green' : 'badge-gray' ?>"><?= $item['published'] ? 'Опублик.' : 'Скрыт' ?></span></td>
        <td class="col-actions">
          <a href="?edit=<?= $item['id'] ?>" class="btn btn-sm btn-outline btn-icon">✏️</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Удалить?')">
            <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline btn-icon" style="color:#ef4444">🗑️</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$items): ?><tr><td colspan="4" style="text-align:center;padding:40px;color:var(--text-muted)">Вопросов нет. <a href="?add=1" style="color:var(--accent)">Добавить</a></td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
