<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();

if (!empty($_SESSION['admin_id'])) {
    redirect(BASE_URL . '/admin/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$username || !$password) {
        $error = 'Введите логин и пароль.';
    } else {
        $admin = DB::fetchOne("SELECT * FROM admins WHERE username = ?", [$username]);
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $redirect = $_GET['r'] ?? (BASE_URL . '/admin/');
            redirect($redirect);
        } else {
            $error = 'Неверный логин или пароль.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Вход — Профсоюз 69</title>
  <link href="https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/admin.css">
</head>
<body>
<div class="login-page">
  <div class="login-box">
    <div class="login-logo">
      <div class="login-logo-mark">П69</div>
      <h2>Профсоюз 69</h2>
      <p>Панель администратора</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label class="form-label">Логин</label>
        <input type="text" name="username" class="form-control" autofocus
               value="<?= h($_POST['username'] ?? '') ?>" placeholder="Введите логин">
      </div>
      <div class="form-group">
        <label class="form-label">Пароль</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px">
        Войти
      </button>
    </form>

    <p style="text-align:center;margin-top:20px;font-size:.8125rem;color:var(--text-muted)">
      <a href="<?= BASE_URL ?>/" style="color:var(--accent)">← На сайт</a>
    </p>
  </div>
</div>
</body>
</html>
