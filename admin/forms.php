<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$adminTitle = 'Обращения';
$adminPage  = 'forms';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'status') {
        DB::execute("UPDATE contacts_form SET status = ? WHERE id = ?", [$_POST['status'], $id]);
        flash('success', 'Статус обновлён.');
    }
    if ($action === 'note') {
        DB::execute("UPDATE contacts_form SET admin_note = ? WHERE id = ?", [trim($_POST['note'] ?? ''), $id]);
        flash('success', 'Заметка сохранена.');
    }
    if ($action === 'delete') {
        DB::execute("DELETE FROM contacts_form WHERE id = ?", [$id]);
        flash('success', 'Обращение удалено.');
    }
    redirect(BASE_URL . '/admin/forms.php');
}

// Просмотр одного обращения
$viewId = (int)($_GET['id'] ?? 0);
$viewing = $viewId ? DB::fetchOne("SELECT * FROM contacts_form WHERE id = ?", [$viewId]) : null;
if ($viewing && $viewing['status'] === 'new') {
    DB::execute("UPDATE contacts_form SET status = 'read' WHERE id = ?", [$viewId]);
    $viewing['status'] = 'read';
}

$perPage = 15;
$page    = max(1, (int)($_GET['page'] ?? 1));
$filter  = $_GET['status'] ?? '';
$where   = $filter ? "WHERE status = ?" : "";
$params  = $filter ? [$filter] : [];

$total = (int)DB::fetchOne("SELECT COUNT(*) as c FROM contacts_form $where", $params)['c'];
$pag   = paginate($total, $perPage, $page);
$forms = DB::fetchAll("SELECT * FROM contacts_form $where ORDER BY created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$perPage, $pag['offset']]));

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($msg = flash('success')): ?><div class="alert alert-success">✅ <?= h($msg) ?></div><?php endif; ?>

<?php if ($viewing): ?>
<!-- Просмотр обращения -->
<div style="margin-bottom:16px">
  <a href="<?= BASE_URL ?>/admin/forms.php" style="color:var(--text-muted);font-size:.875rem">← Назад к списку</a>
</div>
<div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start">
  <div class="card">
    <h2 style="font-size:1.125rem;margin-bottom:8px"><?= h($viewing['subject'] ?: 'Обращение #' . $viewing['id']) ?></h2>
    <div style="display:flex;gap:16px;margin-bottom:20px;font-size:.875rem;color:var(--text-muted)">
      <span>📅 <?= formatDate($viewing['created_at'], 'd.m.Y H:i') ?></span>
      <span class="badge <?= ['new'=>'badge-blue','read'=>'badge-gray','done'=>'badge-green'][$viewing['status']] ?? 'badge-gray' ?>">
        <?= ['new'=>'Новое','read'=>'Прочитано','done'=>'Закрыто'][$viewing['status']] ?? $viewing['status'] ?>
      </span>
    </div>

    <div style="background:#f9fafb;border-radius:8px;padding:20px;line-height:1.7;margin-bottom:20px">
      <?= nl2br(h($viewing['message'])) ?>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:.9375rem">
      <div><span style="color:var(--text-muted)">Имя:</span> <strong><?= h($viewing['name']) ?></strong></div>
      <?php if ($viewing['phone']): ?>
      <div><span style="color:var(--text-muted)">Телефон:</span> <a href="tel:<?= h($viewing['phone']) ?>"><?= h($viewing['phone']) ?></a></div>
      <?php endif; ?>
      <?php if ($viewing['email']): ?>
      <div><span style="color:var(--text-muted)">Email:</span> <a href="mailto:<?= h($viewing['email']) ?>"><?= h($viewing['email']) ?></a></div>
      <?php endif; ?>
    </div>
  </div>

  <div>
    <div class="card" style="margin-bottom:16px">
      <h3 style="font-size:.9375rem;margin-bottom:12px">Изменить статус</h3>
      <form method="POST">
        <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
        <input type="hidden" name="action" value="status">
        <input type="hidden" name="id" value="<?= $viewing['id'] ?>">
        <select name="status" class="form-control" style="margin-bottom:10px">
          <option value="new" <?= $viewing['status'] === 'new' ? 'selected' : '' ?>>Новое</option>
          <option value="read" <?= $viewing['status'] === 'read' ? 'selected' : '' ?>>Прочитано</option>
          <option value="done" <?= $viewing['status'] === 'done' ? 'selected' : '' ?>>Закрыто</option>
        </select>
        <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center">Сохранить</button>
      </form>
    </div>

    <div class="card" style="margin-bottom:16px">
      <h3 style="font-size:.9375rem;margin-bottom:12px">Заметка</h3>
      <form method="POST">
        <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
        <input type="hidden" name="action" value="note">
        <input type="hidden" name="id" value="<?= $viewing['id'] ?>">
        <textarea name="note" class="form-control" rows="4" placeholder="Внутренняя заметка…"><?= h($viewing['admin_note'] ?? '') ?></textarea>
        <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center;margin-top:10px">Сохранить заметку</button>
      </form>
    </div>

    <form method="POST" onsubmit="return confirm('Удалить обращение?')">
      <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" value="<?= $viewing['id'] ?>">
      <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center">🗑️ Удалить</button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- Список обращений -->
<div class="page-header">
  <h2>Обращения (<?= $total ?>)</h2>
  <div style="display:flex;gap:6px">
    <a href="?" class="btn btn-sm <?= !$filter ? 'btn-dark' : 'btn-outline' ?>">Все</a>
    <a href="?status=new" class="btn btn-sm <?= $filter==='new' ? 'btn-dark' : 'btn-outline' ?>">Новые</a>
    <a href="?status=read" class="btn btn-sm <?= $filter==='read' ? 'btn-dark' : 'btn-outline' ?>">Прочитанные</a>
    <a href="?status=done" class="btn btn-sm <?= $filter==='done' ? 'btn-dark' : 'btn-outline' ?>">Закрытые</a>
  </div>
</div>

<div class="card" style="padding:0">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Имя</th>
        <th>Тема / сообщение</th>
        <th>Контакт</th>
        <th>Дата</th>
        <th>Статус</th>
        <th class="col-actions">Действие</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($forms as $f): ?>
      <tr <?= $f['status'] === 'new' ? 'style="font-weight:600"' : '' ?>>
        <td><?= h($f['name']) ?></td>
        <td style="max-width:250px">
          <div style="<?= $f['status']==='new' ? '' : 'color:var(--text-muted)' ?>;font-size:.9375rem">
            <?= h(truncate($f['subject'] ?: $f['message'], 55)) ?>
          </div>
        </td>
        <td style="font-size:.8125rem;color:var(--text-muted)">
          <?= h($f['phone'] ?: ($f['email'] ?: '—')) ?>
        </td>
        <td style="font-size:.8125rem;color:var(--text-muted)"><?= formatDate($f['created_at'], 'd.m H:i') ?></td>
        <td>
          <span class="badge <?= ['new'=>'badge-blue','read'=>'badge-gray','done'=>'badge-green'][$f['status']] ?? 'badge-gray' ?>">
            <?= ['new'=>'Новое','read'=>'Прочитано','done'=>'Закрыто'][$f['status']] ?? $f['status'] ?>
          </span>
        </td>
        <td class="col-actions">
          <a href="?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline btn-icon" title="Открыть">👁️</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Удалить?')">
            <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $f['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline btn-icon" style="color:#ef4444">🗑️</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$forms): ?>
      <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">Обращений нет</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
