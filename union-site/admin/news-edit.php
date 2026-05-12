<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$id      = (int)($_GET['id'] ?? 0);
$isNew   = $id === 0;
$article = $isNew ? null : DB::fetchOne("SELECT * FROM news WHERE id = ?", [$id]);

if (!$isNew && !$article) {
    redirect(BASE_URL . '/admin/news.php');
}

$adminTitle = $isNew ? 'Добавить новость' : 'Редактировать новость';
$adminPage  = 'news';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $error = 'Ошибка безопасности.';
    } else {
        $title     = trim($_POST['title'] ?? '');
        $content   = $_POST['content'] ?? '';
        $excerpt   = trim($_POST['excerpt'] ?? '');
        $category  = $_POST['category'] ?? 'news';
        $published = (int)isset($_POST['published']);

        if (!$title) {
            $error = 'Введите заголовок.';
        } else {
            $slugVal = uniqueSlug('news', $title, $id);
            $imgPath = $article['image'] ?? null;

            // Загрузка изображения
            if (!empty($_FILES['image']['name'])) {
                $uploaded = uploadFile($_FILES['image'], 'news', ['image/jpeg','image/png','image/gif','image/webp']);
                if ($uploaded) {
                    if ($imgPath) deleteUpload($imgPath);
                    $imgPath = $uploaded;
                } else {
                    $error = 'Ошибка загрузки изображения. Допустимые форматы: JPG, PNG, GIF, WebP.';
                }
            }

            // Удаление изображения
            if (isset($_POST['remove_image']) && $imgPath) {
                deleteUpload($imgPath);
                $imgPath = null;
            }

            if (!$error) {
                if ($isNew) {
                    $id = (int)DB::insert(
                        "INSERT INTO news (title, slug, content, excerpt, category, image, published) VALUES (?, ?, ?, ?, ?, ?, ?)",
                        [$title, $slugVal, $content, $excerpt, $category, $imgPath, $published]
                    );
                    $success = 'Новость добавлена.';
                } else {
                    DB::execute(
                        "UPDATE news SET title=?, slug=?, content=?, excerpt=?, category=?, image=?, published=?, updated_at=datetime('now','localtime') WHERE id=?",
                        [$title, $slugVal, $content, $excerpt, $category, $imgPath, $published, $id]
                    );
                    $success = 'Изменения сохранены.';
                }
                $article = DB::fetchOne("SELECT * FROM news WHERE id = ?", [$id]);
                $isNew = false;
                flash('success', $success);
                redirect(BASE_URL . '/admin/news-edit.php?id=' . $id);
            }
        }
    }
}

if ($msg = flash('success')) $success = $msg;

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($success): ?><div class="alert alert-success">✅ <?= h($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error">⚠️ <?= h($error) ?></div><?php endif; ?>

<div style="margin-bottom:20px">
  <a href="<?= BASE_URL ?>/admin/news.php" style="color:var(--text-muted);font-size:.875rem">← Назад к списку</a>
</div>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">

  <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">
    <!-- Основной контент -->
    <div>
      <div class="card">
        <div class="form-group">
          <label class="form-label">Заголовок <span style="color:var(--accent)">*</span></label>
          <input type="text" name="title" class="form-control"
                 value="<?= h($article['title'] ?? '') ?>" placeholder="Введите заголовок новости" required>
        </div>

        <div class="form-group">
          <label class="form-label">Краткое описание <small>(для карточки и SEO)</small></label>
          <textarea name="excerpt" class="form-control" rows="3" placeholder="1–2 предложения о содержании…"><?= h($article['excerpt'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Полный текст новости</label>
          <div class="editor-toolbar">
            <button type="button" onclick="fmt('bold')"><strong>Ж</strong></button>
            <button type="button" onclick="fmt('italic')"><em>К</em></button>
            <button type="button" onclick="fmt('underline')"><u>Ч</u></button>
            <button type="button" onclick="fmtBlock('h2')">Заголовок 2</button>
            <button type="button" onclick="fmtBlock('h3')">Заголовок 3</button>
            <button type="button" onclick="fmt('insertUnorderedList')">• Список</button>
            <button type="button" onclick="fmt('insertOrderedList')">1. Список</button>
            <button type="button" onclick="insertLink()">Ссылка</button>
            <button type="button" onclick="fmtBlock('blockquote')">Цитата</button>
          </div>
          <div class="editor-area" id="editor" contenteditable="true"><?= $article['content'] ?? '' ?></div>
          <input type="hidden" name="content" id="content-input">
        </div>
      </div>
    </div>

    <!-- Настройки -->
    <div>
      <div class="card" style="margin-bottom:16px">
        <h3 style="font-size:.9375rem;margin-bottom:16px">Публикация</h3>
        <div class="form-group" style="margin-bottom:12px">
          <label class="form-label">Категория</label>
          <select name="category" class="form-control">
            <option value="news" <?= ($article['category'] ?? 'news') === 'news' ? 'selected' : '' ?>>Новость</option>
            <option value="events" <?= ($article['category'] ?? '') === 'events' ? 'selected' : '' ?>>Мероприятие</option>
            <option value="achievements" <?= ($article['category'] ?? '') === 'achievements' ? 'selected' : '' ?>>Достижение</option>
            <option value="announcements" <?= ($article['category'] ?? '') === 'announcements' ? 'selected' : '' ?>>Объявление</option>
          </select>
        </div>
        <div class="form-check" style="margin-bottom:16px">
          <input type="checkbox" name="published" id="published" value="1"
                 <?= ($article['published'] ?? 1) ? 'checked' : '' ?>>
          <label for="published">Опубликовать</label>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
          💾 <?= $isNew ? 'Создать новость' : 'Сохранить' ?>
        </button>
        <?php if (!$isNew): ?>
        <a href="<?= BASE_URL ?>/news-single.php?slug=<?= $article['slug'] ?>" target="_blank"
           class="btn btn-outline" style="width:100%;justify-content:center;margin-top:8px">
          👁️ Просмотр на сайте
        </a>
        <?php endif; ?>
      </div>

      <div class="card">
        <h3 style="font-size:.9375rem;margin-bottom:14px">Изображение</h3>
        <?php if (!empty($article['image'])): ?>
        <div class="img-preview" style="margin-bottom:12px">
          <img src="<?= BASE_URL ?>/uploads/<?= h($article['image']) ?>" alt="">
        </div>
        <div class="form-check" style="margin-bottom:10px">
          <input type="checkbox" name="remove_image" id="remove_image" value="1">
          <label for="remove_image" style="color:#ef4444">Удалить изображение</label>
        </div>
        <?php endif; ?>
        <label class="form-label">Загрузить <?= !empty($article['image']) ? 'новое ' : '' ?>изображение</label>
        <input type="file" name="image" class="form-control-file" accept="image/*">
        <p class="form-hint">JPG, PNG, WebP — до 5 МБ. Рекомендуемый размер: 1200×675 пикс.</p>
      </div>
    </div>
  </div>
</form>

<script>
const editor = document.getElementById('editor');
const contentInput = document.getElementById('content-input');

// Синхронизация перед отправкой формы
document.querySelector('form').addEventListener('submit', () => {
  contentInput.value = editor.innerHTML;
});

function fmt(cmd) {
  document.execCommand(cmd, false, null);
  editor.focus();
}

function fmtBlock(tag) {
  document.execCommand('formatBlock', false, '<' + tag + '>');
  editor.focus();
}

function insertLink() {
  const url = prompt('Введите URL:');
  if (url) {
    document.execCommand('createLink', false, url);
    editor.focus();
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
