<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$adminTitle = 'Загрузить документ';
$adminPage  = 'documents';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $title    = trim($_POST['title'] ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? 'general';
    $sort     = (int)($_POST['sort_order'] ?? 0);
    $pub      = (int)isset($_POST['published']);

    if (!$title) {
        $error = 'Введите название документа.';
    } elseif (empty($_FILES['file']['name'])) {
        $error = 'Загрузите файл.';
    } else {
        $allowedTypes = ['application/pdf','application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip','image/jpeg','image/png'];
        $uploaded = uploadFile($_FILES['file'], 'docs', $allowedTypes);
        if (!$uploaded) {
            $error = 'Ошибка загрузки. Допустимые форматы: PDF, DOC, DOCX, XLS, XLSX, ZIP, JPG, PNG.';
        } else {
            DB::insert("INSERT INTO documents (title, description, file, category, sort_order, published) VALUES (?,?,?,?,?,?)",
                [$title, $desc, $uploaded, $category, $sort, $pub]);
            flash('success', 'Документ загружен.');
            redirect(BASE_URL . '/admin/documents.php');
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<?php if (!empty($error)): ?><div class="alert alert-error">⚠️ <?= h($error) ?></div><?php endif; ?>

<div style="margin-bottom:16px">
  <a href="<?= BASE_URL ?>/admin/documents.php" style="color:var(--text-muted);font-size:.875rem">← Назад к документам</a>
</div>

<div class="card" style="max-width:600px">
  <h2 style="font-size:1.125rem;margin-bottom:24px">Загрузить документ</h2>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
    <div class="form-group">
      <label class="form-label">Название документа <span style="color:var(--accent)">*</span></label>
      <input type="text" name="title" class="form-control" value="<?= h($_POST['title'] ?? '') ?>" required>
    </div>
    <div class="form-group">
      <label class="form-label">Описание</label>
      <textarea name="description" class="form-control" rows="2"><?= h($_POST['description'] ?? '') ?></textarea>
    </div>
    <div class="form-row form-row-2">
      <div class="form-group" style="margin:0">
        <label class="form-label">Категория</label>
        <select name="category" class="form-control">
          <option value="general">Общие</option>
          <option value="agreements">Соглашения</option>
          <option value="collective">Колл. договоры</option>
          <option value="regulations">Положения</option>
          <option value="reports">Отчёты</option>
          <option value="templates">Шаблоны</option>
        </select>
      </div>
      <div class="form-group" style="margin:0">
        <label class="form-label">Порядок</label>
        <input type="number" name="sort_order" class="form-control" value="0">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Файл <span style="color:var(--accent)">*</span></label>
      <input type="file" name="file" class="form-control-file" required>
      <p class="form-hint">PDF, DOC, DOCX, XLS, XLSX, ZIP — до 20 МБ</p>
    </div>
    <div class="form-check" style="margin-bottom:20px">
      <input type="checkbox" name="published" id="pub" value="1" checked>
      <label for="pub">Опубликовать сразу</label>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">📤 Загрузить</button>
      <a href="<?= BASE_URL ?>/admin/documents.php" class="btn btn-outline">Отмена</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
