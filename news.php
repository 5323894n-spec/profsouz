<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$activeNav = 'news';
$pageTitle = 'Новости';

$perPage  = 8;
$page     = max(1, (int)($_GET['page'] ?? 1));
$category = $_GET['cat'] ?? '';

$where = "WHERE published = 1";
$params = [];
if ($category) {
    $where .= " AND category = ?";
    $params[] = $category;
}

$total = (int)DB::fetchOne("SELECT COUNT(*) as c FROM news $where", $params)['c'];
$pag   = paginate($total, $perPage, $page);

$news = DB::fetchAll(
    "SELECT * FROM news $where ORDER BY created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$perPage, $pag['offset']])
);

$categories = DB::fetchAll("SELECT * FROM news_categories ORDER BY sort_order");

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumbs">
      <a href="<?= url() ?>">Главная</a>
      <span>›</span>
      <span>Новости</span>
    </div>
    <h1>Новости профсоюза</h1>
    <p>Актуальные события, достижения и объявления.</p>
  </div>
</div>

<section class="section">
  <div class="container">
    <!-- Фильтры -->
    <div class="filter-bar">
      <a href="<?= url('news.php') ?>" class="filter-btn<?= !$category ? ' active' : '' ?>">Все</a>
      <?php foreach ($categories as $cat): ?>
      <a href="<?= url('news.php?cat=' . $cat['slug']) ?>" class="filter-btn<?= $category === $cat['slug'] ? ' active' : '' ?>">
        <?= h($cat['name']) ?>
      </a>
      <?php endforeach; ?>
    </div>

    <?php if ($news): ?>
    <div class="news-list">
      <?php foreach ($news as $item): ?>
      <a href="<?= url('news-single.php?slug=' . $item['slug']) ?>" class="news-list-item" style="text-decoration:none;color:inherit">
        <div class="news-list-item__img">
          <?php if ($item['image']): ?>
            <img src="<?= uploadUrl($item['image']) ?>" alt="<?= h($item['title']) ?>">
          <?php else: ?>
            <div class="news-list-item__img-placeholder">📰</div>
          <?php endif; ?>
        </div>
        <div class="news-list-item__body">
          <div class="news-card__meta" style="margin-bottom:10px">
            <span class="news-card__cat"><?= h(match($item['category']) {
              'events' => 'Мероприятие',
              'achievements' => 'Достижение',
              'announcements' => 'Объявление',
              default => 'Новость'
            }) ?></span>
            <span class="news-card__date"><?= formatDateRu($item['created_at']) ?></span>
          </div>
          <div class="news-list-item__title"><?= h($item['title']) ?></div>
          <?php if ($item['excerpt']): ?>
          <div class="news-list-item__excerpt"><?= h(truncate($item['excerpt'], 200)) ?></div>
          <?php endif; ?>
          <div class="news-card__link" style="margin-top:12px">Читать далее →</div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- Пагинация -->
    <?php if ($pag['pages'] > 1): ?>
    <div class="pagination">
      <a href="?page=<?= $page-1 ?><?= $category ? '&cat='.$category : '' ?>" class="page-link<?= $pag['prev'] ? '' : ' disabled' ?>">←</a>
      <?php for ($p = 1; $p <= $pag['pages']; $p++): ?>
      <a href="?page=<?= $p ?><?= $category ? '&cat='.$category : '' ?>" class="page-link<?= $p === $page ? ' active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
      <a href="?page=<?= $page+1 ?><?= $category ? '&cat='.$category : '' ?>" class="page-link<?= $pag['next'] ? '' : ' disabled' ?>">→</a>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="empty-state">
      <div class="empty-state__icon">📰</div>
      <h3>Новостей пока нет</h3>
      <p>Скоро здесь появятся актуальные новости профсоюза.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
