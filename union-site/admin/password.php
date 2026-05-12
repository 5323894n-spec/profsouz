<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$adminTitle = 'Сменить пароль';
$adminPage  = 'password';
$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $current = $_POST['current_password'] ?? '';
    $new1    = $_POST['new_password'] ?? '';
    $new2    = $_POST['confirm_password'] ?? '';

    $admin = DB::fetchOne("SELECT * FROM admins WHERE id = ?", [$_SESSION['admin_id']]);
    if (!password_verify($current, $admin['password'])) {
        $error = 'Текущий пароль введён неверно.';
    } elseif (strlen($new1) < 6) {
        $error = 'Новый пароль должен содержать не менее 6 символов.';
    } elseif ($new1 !== $new2) {
        $error = 'Пароли не совпадают.';
    } else {
        DB::execute("UPDATE admins SET password = ? WHERE id = ?",
            [password_hash($new1, PASSWORD_DEFAULT), $_SESSION['admin_id']]);
        flash('success', 'Пароль успешно изменён.');
        redirect(BASE_URL . '/admin/password.php');
    }
}
if ($msg = flash('success')) $success = $msg;

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($success): ?><div class="alert alert-success">✅ <?= h($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error">⚠️ <?= h($error) ?></div><?php endif; ?>

<div class="card" style="max-width:420px">
  <h2 style="font-size:1.125rem;margin-bottom:24px">Смена пароля</h2>
  <form method="POST">
    <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
    <div class="form-group">
      <label class="form-label">Текущий пароль</label>
      <input type="password" name="current_password" class="form-control" required>
    </div>
    <div class="form-group">
      <label class="form-label">Новый пароль</label>
      <input type="password" name="new_password" class="form-control" minlength="6" required>
    </div>
    <div class="form-group">
      <label class="form-label">Повторить новый пароль</label>
      <input type="password" name="confirm_password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">🔒 Сменить пароль</button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
