<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$adminTitle = 'Первичные организации';
$adminPage  = 'organizations';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'delete') {
        DB::execute("DELETE FROM organizations WHERE id = ?", [$id]);
        flash('success', 'Организация удалена.');
        redirect(BASE_URL . '/admin/organizations.php');
    }
    if ($action === 'save') {
        $name    = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $chairman= trim($_POST['chairman'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $members = (int)($_POST['members_count'] ?? 0);
        $sort    = (int)($_POST['sort_order'] ?? 0);
        $pub     = (int)isset($_POST['published']);
        if ($name) {
            if ($id) {
                DB::execute("UPDATE organizations SET name=?,address=?,chairman=?,phone=?,members_count=?,sort_order=?,published=? WHERE id=?",
                    [$name,$address,$chairman,$phone,$members,$sort,$pub,$id]);
            } else {
                DB::insert("INSERT INTO organizations (name,address,chairman,phone,members_count,sort_order,published) VALUES (?,?,?,?,?,?,?)",
                    [$name,$address,$chairman,$phone,$members,$sort,$pub]);
            }
            flash('success', 'Сохранено.');
        }
        redirect(BASE_URL . '/admin/organizations.php');
    }
}

$editing = isset($_GET['edit']) ? DB::fetchOne("SELECT * FROM organizations WHERE id=?", [(int)$_GET['edit']]) : null;
$adding = isset($_GET['add']);
$orgs = DB::fetchAll("SELECT * FROM organizations ORDER BY sort_order, name");

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($msg = flash('success')): ?><div class="alert alert-success">✅ <?= h($msg) ?></div><?php endif; ?>

<?php if ($editing || $adding): ?>
<div style="margin-bottom:16px"><a href="?" style="color:var(--text-muted);font-size:.875rem">← Назад</a></div>
<div class="card" style="max-width:600px">
  <h2 style="font-size:1.125rem;margin-bottom:20px"><?= $editing ? 'Редактировать' : 'Добавить' ?> организацию</h2>
  <form method="POST">
    <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
    <input type="hidden" name="action" value="save">
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"><?php endif; ?>
    <div class="form-group"><label class="form-label">Название <span style="color:var(--accent)">*</span></label><input type="text" name="name" class="form-control" value="<?= h($editing['name'] ?? '') ?>" required></div>
    <div class="form-group"><label class="form-label">Адрес</label><input type="text" name="address" class="form-control" value="<?= h($editing['address'] ?? '') ?>"></div>
    <div class="form-row form-row-2">
      <div class="form-group" style="margin:0"><label class="form-label">Председатель</label><input type="text" name="chairman" class="form-control" value="<?= h($editing['chairman'] ?? '') ?>"></div>
      <div class="form-group" style="margin:0"><label class="form-label">Телефон</label><input type="text" name="phone" class="form-control" value="<?= h($editing['phone'] ?? '') ?>"></div>
    </div>
    <div class="form-row form-row-2">
      <div class="form-group" style="margin:0"><label class="form-label">Число членов</label><input type="number" name="members_count" class="form-control" value="<?= (int)($editing['members_count'] ?? 0) ?>"></div>
      <div class="form-group" style="margin:0"><label class="form-label">Порядок</label><input type="number" name="sort_order" class="form-control" value="<?= (int)($editing['sort_order'] ?? 0) ?>"></div>
    </div>
    <div class="form-check" style="margin-bottom:20px"><input type="checkbox" name="published" id="pub" value="1" <?= ($editing['published'] ?? 1) ? 'checked' : '' ?>><label for="pub">Отображать на сайте</label></div>
    <div style="display:flex;gap:10px"><button type="submit" class="btn btn-primary">💾 Сохранить</button><a href="?" class="btn btn-outline">Отмена</a></div>
  </form>
</div>

<?php else: ?>
<div class="page-header">
  <h2>Первичные организации (<?= count($orgs) ?>)</h2>
  <a href="?add=1" class="btn btn-primary">+ Добавить</a>
</div>
<div class="card" style="padding:0">
  <table class="admin-table">
    <thead><tr><th>Название</th><th>Председатель</th><th>Членов</th><th>Статус</th><th class="col-actions">Действия</th></tr></thead>
    <tbody>
      <?php foreach ($orgs as $org): ?>
      <tr>
        <td style="font-weight:600"><?= h($org['name']) ?><div style="font-size:.8125rem;color:var(--text-muted)"><?= h($org['address'] ?? '') ?></div></td>
        <td style="font-size:.875rem"><?= h($org['chairman'] ?? '—') ?></td>
        <td><span class="badge badge-orange"><?= (int)$org['members_count'] ?></span></td>
        <td><span class="badge <?= $org['published'] ? 'badge-green' : 'badge-gray' ?>"><?= $org['published'] ? 'Показан' : 'Скрыт' ?></span></td>
        <td class="col-actions">
          <a href="?edit=<?= $org['id'] ?>" class="btn btn-sm btn-outline btn-icon">✏️</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Удалить?')">
            <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $org['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline btn-icon" style="color:#ef4444">🗑️</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$orgs): ?><tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted)">Организаций нет</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
