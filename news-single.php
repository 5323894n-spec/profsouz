<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$slug = trim($_GET['slug'] ?? '');
if (!$slug) {
    header('Location: ' . url('news.php'));
    exit;
}

$article = DB::fetchOne("SELECT * FROM news WHERE slug = ? AND published = 1", [$slug]);
if (!$article) {
    http_response_code(404);
    $pageTitle = 'Статья не найдена';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="container" style="padding:80px 24px;text-align:center"><h1>404</h1><p>Материал не найден.</p><a href="'.url('news.php').'" class="btn btn-dark" style="margin-top:20px">← Все новости</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Увеличиваем счётчик просмотров
DB::execute("UPDATE news SET views = views + 1 WHERE id = ?", [$article['id']]);

$activeNav = 'news';
$pageTitle = $article['title'];
$pageDesc  = $article['excerpt'] ? truncate($article['excerpt'], 160) : '';

// Последние новости для сайдбара
$relatedNews = DB::fetchAll(
    "SELECT id, title, slug, created_at FROM news WHERE published = 1 AND id != ? ORDER BY created_at DESC LIMIT 5",
    [$article['id']]
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumbs">
      <a href="<?= url() ?>">Главная</a>
      <span>›</span>
      <a href="<?= url('news.php') ?>">Новости</a>
      <span>›</span>
      <span><?= h(truncate($article['title'], 50)) ?></span>
    </div>
  </div>
</div>

<div class="container">
  <div class="article-layout">
    <!-- Основной контент -->
    <main class="article-body">
      <?php if ($article['image']): ?>
      <div class="article-cover">
        <img src="<?= uploadUrl($article['image']) ?>" alt="<?= h($article['title']) ?>">
      </div>
      <?php endif; ?>

      <div class="article-meta">
        <span class="news-card__cat"><?= h(match($article['category']) {
          'events' => 'Мероприятие',
          'achievements' => 'Достижение',
          'announcements' => 'Объявление',
          default => 'Новость'
        }) ?></span>
        <span class="news-card__date"><?= formatDateRu($article['created_at']) ?></span>
        <span class="text-muted" style="font-size:.8125rem">👁 <?= (int)$article['views'] ?> просмотров</span>
      </div>

      <h1 style="margin-bottom:24px;font-size:clamp(1.5rem,4vw,2.25rem)"><?= h($article['title']) ?></h1>

      <?php if ($article['excerpt']): ?>
      <p style="font-size:1.125rem;color:var(--text-muted);margin-bottom:28px;line-height:1.7;font-weight:500">
        <?= h($article['excerpt']) ?>
      </p>
      <?php endif; ?>

      <div class="article-content">
        <?= $article['content'] ?>
      </div>

      <div style="margin-top:40px;padding-top:24px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <a href="<?= url('news.php') ?>" class="btn btn-outline">← Все новости</a>
        <span class="text-muted" style="font-size:.875rem">Опубликовано: <?= formatDateRu($article['created_at']) ?></span>
      </div>
    </main>

    <!-- Сайдбар -->
    <aside class="article-sidebar">
      <div class="sidebar-widget">
        <h4>Последние новости</h4>
        <?php foreach ($relatedNews as $r): ?>
        <a href="<?= url('news-single.php?slug=' . $r['slug']) ?>" class="sidebar-news-item" style="text-decoration:none;color:inherit">
          <div style="flex:1">
            <div class="sidebar-news-item__title"><?= h($r['title']) ?></div>
            <div class="sidebar-news-item__date"><?= formatDateRu($r['created_at']) ?></div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>

      <div class="sidebar-widget">
        <h4>Профсоюз 69</h4>
        <p style="font-size:.875rem;color:var(--text-muted);margin-bottom:16px">Есть вопросы или предложения? Свяжитесь с нами.</p>
        <a href="<?= url('contacts.php') ?>" class="btn btn-primary" style="width:100%;justify-content:center">Обратиться</a>
      </div>
    </aside>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
