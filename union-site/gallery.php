<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$activeNav = 'gallery';
$pageTitle = 'Галерея';

$filter = $_GET['type'] ?? '';
$where  = "WHERE g.published = 1" . ($filter ? " AND g.type = ?" : "");
$params = $filter ? [$filter] : [];

$items = DB::fetchAll(
    "SELECT g.*, a.name AS album_name FROM gallery g
     LEFT JOIN gallery_albums a ON a.slug = g.album
     $where ORDER BY g.sort_order ASC, g.created_at DESC",
    $params
);

$albums = DB::fetchAll("SELECT * FROM gallery_albums ORDER BY sort_order");

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumbs">
      <a href="<?= url() ?>">Главная</a>
      <span>›</span>
      <span>Галерея</span>
    </div>
    <h1>Фотогалерея</h1>
    <p>Жизнь профсоюза в фотографиях и видео.</p>
  </div>
</div>

<section class="section">
  <div class="container">
    <!-- Фильтры -->
    <div class="filter-bar">
      <a href="<?= url('gallery.php') ?>" class="filter-btn<?= !$filter ? ' active' : '' ?>">Все</a>
      <a href="<?= url('gallery.php?type=photo') ?>" class="filter-btn<?= $filter === 'photo' ? ' active' : '' ?>">Фотографии</a>
      <a href="<?= url('gallery.php?type=video') ?>" class="filter-btn<?= $filter === 'video' ? ' active' : '' ?>">Видео</a>
    </div>

    <?php if ($items): ?>
    <div class="gallery-grid" style="grid-template-columns:repeat(auto-fill,minmax(240px,1fr))">
      <?php foreach ($items as $item): ?>
      <?php if ($item['type'] === 'video'): ?>
      <div class="gallery-item" style="aspect-ratio:16/9;cursor:pointer" onclick="window.open('<?= h($item['file']) ?>','_blank')">
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;background:#1c2530;color:#fff;gap:8px">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
          <span style="font-size:.875rem;max-width:90%;text-align:center"><?= h($item['title']) ?></span>
        </div>
      </div>
      <?php else: ?>
      <div class="gallery-item" data-lightbox="main"
           data-src="<?= uploadUrl($item['file']) ?>"
           data-title="<?= h($item['title']) ?>">
        <img src="<?= uploadUrl($item['file']) ?>" alt="<?= h($item['title']) ?>" loading="lazy">
        <div class="gallery-item__overlay">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
        </div>
      </div>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <div class="empty-state__icon">📷</div>
      <h3>Галерея пуста</h3>
      <p>Фотографии и видео появятся после добавления через панель администратора.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Лайтбокс -->
<div id="lightbox" class="lightbox-overlay">
  <button id="lb-close">✕</button>
  <button id="lb-prev">‹</button>
  <img id="lb-img" src="" alt="">
  <div id="lb-caption"></div>
  <button id="lb-next">›</button>
</div>

<style>
.lightbox-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.94);
  z-index: 1000;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.lightbox-overlay.active { display: flex; }
#lb-close {
  position: absolute; top: 16px; right: 20px;
  background: none; border: none; color: #fff; font-size: 28px; cursor: pointer; z-index: 2;
}
#lb-prev, #lb-next {
  position: absolute; background: rgba(255,255,255,.1); border: none; color: #fff;
  font-size: 32px; cursor: pointer; padding: 10px 14px; border-radius: 8px; z-index: 2;
  transition: background .15s;
}
#lb-prev { left: 16px; }
#lb-next { right: 16px; }
#lb-prev:hover, #lb-next:hover { background: rgba(255,255,255,.2); }
#lb-img { max-width: 92vw; max-height: 88vh; border-radius: 8px; object-fit: contain; }
#lb-caption {
  position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
  color: rgba(255,255,255,.8); font-size: .9375rem; text-align: center;
  background: rgba(0,0,0,.5); padding: 6px 16px; border-radius: 20px;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
