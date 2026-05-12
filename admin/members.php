<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$adminTitle = 'Руководство';
$adminPage  = 'members';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'delete') {
        $row = DB::fetchOne("SELECT photo FROM members WHERE id = ?", [$id]);
        if ($row && $row['photo']) deleteUpload($row['photo']);
        DB::execute("DELETE FROM members WHERE id = ?", [$id]);
        flash('success', 'Запись удалена.');
        redirect(BASE_URL . '/admin/members.php');
    }

    if ($action === 'save') {
        $name     = trim($_POST['name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $desc     = trim($_POST['description'] ?? '');
        $sort     = (int)($_POST['sort_order'] ?? 0);
        $pub      = (int)isset($_POST['published']);

        if (!$name) {
            flash('error', 'Введите ФИО.');
            redirect(BASE_URL . '/admin/members.php' . ($id ? '?edit=' . $id : '?add=1'));
        }

        $photo = $id ? (DB::fetchOne("SELECT photo FROM members WHERE id = ?", [$id])['photo'] ?? null) : null;
        if (!empty($_FILES['photo']['name'])) {
            $up = uploadFile($_FILES['photo'], 'members', ['image/jpeg','image/png','image/webp']);
            if ($up) {
                if ($photo) deleteUpload($photo);
                $photo = $up;
            }
        }
        if (isset($_POST['remove_photo']) && $photo) {
            deleteUpload($photo);
            $photo = null;
        }

        if ($id) {
            DB::execute("UPDATE members SET name=?,position=?,phone=?,email=?,description=?,sort_order=?,published=?,photo=? WHERE id=?",
                [$name,$position,$phone,$email,$desc,$sort,$pub,$photo,$id]);
        } else {
            DB::insert("INSERT INTO members (name,position,phone,email,description,sort_order,published,photo) VALUES (?,?,?,?,?,?,?,?)",
                [$name,$position,$phone,$email,$desc,$sort,$pub,$photo]);
        }
        flash('success', $id ? 'Изменения сохранены.' : 'Запись добавлена.');
        redirect(BASE_URL . '/admin/members.php');
    }
}

$editing = null;
if (isset($_GET['edit'])) $editing = DB::fetchOne("SELECT * FROM members WHERE id = ?", [(int)$_GET['edit']]);
$adding  = isset($_GET['add']);

$members = DB::fetchAll("SELECT * FROM members ORDER BY sort_order, name");

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($msg = flash('success')): ?><div class="alert alert-success">✅ <?= h($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert alert-error">⚠️ <?= h($msg) ?></div><?php endif; ?>

<?php if ($editing || $adding): ?>
<!-- Форма редактирования/добавления -->
<div style="margin-bottom:16px">
  <a href="<?= BASE_URL ?>/admin/members.php" style="color:var(--text-muted);font-size:.875rem">← Назад</a>
</div>
<div class="card" style="max-width:600px">
  <h2 style="font-size:1.125rem;margin-bottom:24px"><?= $editing ? 'Редактировать' : 'Добавить' ?> сотрудника</h2>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
    <input type="hidden" name="action" value="save">
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"><?php endif; ?>

    <div class="form-group">
      <label class="form-label">ФИО <span style="color:var(--accent)">*</span></label>
      <input type="text" name="name" class="form-control" value="<?= h($editing['name'] ?? '') ?>" placeholder="Иванов Иван Иванович" required>
    </div>
    <div class="form-group">
      <label class="form-label">Должность</label>
      <input type="text" name="position" class="form-control" value="<?= h($editing['position'] ?? '') ?>" placeholder="Председатель профсоюза">
    </div>
    <div class="form-row form-row-2">
      <div class="form-group" style="margin:0">
        <label class="form-label">Телефон</label>
        <input type="text" name="phone" class="form-control" value="<?= h($editing['phone'] ?? '') ?>">
      </div>
      <div class="form-group" style="margin:0">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= h($editing['email'] ?? '') ?>">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Краткое описание</label>
      <textarea name="description" class="form-control" rows="3"><?= h($editing['description'] ?? '') ?></textarea>
    </div>
    <div class="form-row form-row-2">
      <div class="form-group" style="margin:0">
        <label class="form-label">Порядок сортировки</label>
        <input type="number" name="sort_order" class="form-control" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
      </div>
      <div class="form-group" style="margin:0;display:flex;align-items:flex-end">
        <div class="form-check">
          <input type="checkbox" name="published" id="pub" value="1" <?= ($editing['published'] ?? 1) ? 'checked' : '' ?>>
          <label for="pub">Отображать на сайте</label>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Фотография</label>
      <?php if (!empty($editing['photo'])): ?>
      <div class="img-preview" style="margin-bottom:10px">
        <img src="<?= BASE_URL ?>/uploads/<?= h($editing['photo']) ?>">
      </div>
      <div class="form-check" style="margin-bottom:8px">
        <input type="checkbox" name="remove_photo" id="rm_photo" value="1">
        <label for="rm_photo" style="color:#ef4444">Удалить фото</label>
      </div>
      <?php endif; ?>
      <input type="file" name="photo" class="form-control-file" accept="image/*">
      <p class="form-hint">Рекомендуется квадратное фото, JPG/PNG</p>
    </div>

    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">💾 Сохранить</button>
      <a href="<?= BASE_URL ?>/admin/members.php" class="btn btn-outline">Отмена</a>
    </div>
  </form>
</div>

<?php else: ?>
<!-- Список -->
<div class="page-header">
  <h2>Руководство профсоюза</h2>
  <a href="?add=1" class="btn btn-primary">+ Добавить</a>
</div>

<div class="card" style="padding:0">
  <table class="admin-table">
    <thead>
      <tr>
        <th style="width:50px">Фото</th>
        <th>ФИО</th>
        <th>Должность</th>
        <th>Контакт</th>
        <th>Статус</th>
        <th class="col-actions">Действия</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($members as $m): ?>
      <tr>
        <td>
          <?php if ($m['photo']): ?>
          <img src="<?= BASE_URL ?>/uploads/<?= h($m['photo']) ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover">
          <?php else: ?>
          <div style="width:36px;height:36px;border-radius:50%;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:.875rem">👤</div>
          <?php endif; ?>
        </td>
        <td style="font-weight:600"><?= h($m['name']) ?></td>
        <td style="color:var(--text-muted);font-size:.875rem"><?= h($m['position'] ?? '—') ?></td>
        <td style="font-size:.8125rem;color:var(--text-muted)"><?= h($m['phone'] ?: ($m['email'] ?: '—')) ?></td>
        <td><span class="badge <?= $m['published'] ? 'badge-green' : 'badge-gray' ?>"><?= $m['published'] ? 'Показан' : 'Скрыт' ?></span></td>
        <td class="col-actions">
          <a href="?edit=<?= $m['id'] ?>" class="btn btn-sm btn-outline btn-icon">✏️</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Удалить?')">
            <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $m['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline btn-icon" style="color:#ef4444">🗑️</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$members): ?>
      <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">Записей нет. <a href="?add=1" style="color:var(--accent)">Добавить первую</a></td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
