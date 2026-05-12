<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$adminTitle = 'Добавить фото/видео';
$adminPage  = 'gallery';

$albums = DB::fetchAll("SELECT * FROM gallery_albums ORDER BY sort_order");

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($msg = flash('error')): ?><div class="alert alert-error">⚠️ <?= h($msg) ?></div><?php endif; ?>

<div style="margin-bottom:16px">
  <a href="<?= BASE_URL ?>/admin/gallery.php" style="color:var(--text-muted);font-size:.875rem">← Назад к галерее</a>
</div>

<div class="card" style="max-width:600px">
  <h2 style="margin-bottom:24px;font-size:1.125rem">Добавить в галерею</h2>

  <form method="POST" action="<?= BASE_URL ?>/admin/gallery.php" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
    <input type="hidden" name="action" value="add">

    <div class="form-group">
      <label class="form-label">Название <span style="color:var(--accent)">*</span></label>
      <input type="text" name="title" class="form-control" placeholder="Описание фото" required>
    </div>

    <div class="form-group">
      <label class="form-label">Описание</label>
      <textarea name="description" class="form-control" rows="3" placeholder="Дополнительное описание…"></textarea>
    </div>

    <div class="form-group">
      <label class="form-label">Тип</label>
      <select name="type" class="form-control" id="type-select" onchange="toggleVideoField()">
        <option value="photo">📷 Фотография</option>
        <option value="video">▶ Видео (ссылка)</option>
      </select>
    </div>

    <div id="photo-field" class="form-group">
      <label class="form-label">Файл изображения <span style="color:var(--accent)">*</span></label>
      <input type="file" name="file" class="form-control-file" accept="image/*" id="img-input" onchange="previewImg(this)">
      <p class="form-hint">JPG, PNG, WebP — до 5 МБ</p>
      <div id="img-preview" style="margin-top:10px;display:none">
        <img id="preview-img" src="" style="max-width:300px;border-radius:8px;border:1px solid var(--border)">
      </div>
    </div>

    <div id="video-field" class="form-group" style="display:none">
      <label class="form-label">Ссылка на видео <span style="color:var(--accent)">*</span></label>
      <input type="text" name="video_url" class="form-control" placeholder="https://youtube.com/watch?v=...">
      <p class="form-hint">Ссылка на YouTube или другой видеохостинг</p>
    </div>

    <div class="form-group">
      <label class="form-label">Альбом <small>(необязательно)</small></label>
      <select name="album" class="form-control">
        <option value="">— Без альбома —</option>
        <?php foreach ($albums as $a): ?>
        <option value="<?= h($a['slug']) ?>"><?= h($a['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">💾 Добавить</button>
      <a href="<?= BASE_URL ?>/admin/gallery.php" class="btn btn-outline">Отмена</a>
    </div>
  </form>
</div>

<script>
function toggleVideoField() {
  const type = document.getElementById('type-select').value;
  document.getElementById('photo-field').style.display = type === 'photo' ? '' : 'none';
  document.getElementById('video-field').style.display = type === 'video' ? '' : 'none';
}

function previewImg(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('preview-img').src = e.target.result;
      document.getElementById('img-preview').style.display = '';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
