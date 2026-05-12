<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$activeNav = 'home';
$pageTitle = setting('site_name');
$pageDesc  = setting('site_tagline');

// Свежие новости для главной (3 штуки)
$latestNews = DB::fetchAll(
    "SELECT * FROM news WHERE published = 1 ORDER BY created_at DESC LIMIT 3"
);

// Фотографии для галереи (8 штук)
$galleryItems = DB::fetchAll(
    "SELECT * FROM gallery WHERE published = 1 AND type = 'photo' ORDER BY sort_order ASC, created_at DESC LIMIT 8"
);

require_once __DIR__ . '/includes/header.php';
?>

<!-- ГЕРОЙ -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-bg-pattern"></div>
  <div class="hero-accent-bar"></div>
  <div class="container">
    <div class="hero-content">
      <div class="section-label">Профсоюз 69 · с <?= h(setting('site_since', '1957')) ?> года</div>
      <h1><?= h(setting('hero_title', 'Защищаем права каждого работника')) ?></h1>
      <p><?= h(setting('hero_text')) ?></p>
      <div class="hero-actions">
        <a href="<?= url('help.php#join') ?>" class="btn btn-primary btn-lg">Вступить в профсоюз</a>
        <a href="<?= url('about.php') ?>" class="btn btn-outline btn-lg">О нас</a>
      </div>
    </div>
  </div>
</section>

<!-- СТАТИСТИКА -->
<div class="hero-stats">
  <div class="container">
    <div class="stat-item">
      <span class="stat-num"><?= h(setting('members_count', '12 416')) ?></span>
      <div class="stat-label">членов профсоюза</div>
    </div>
    <div class="stat-item">
      <span class="stat-num"><?= h(setting('orgs_count', '68')) ?></span>
      <div class="stat-label">первичных организаций</div>
    </div>
    <div class="stat-item">
      <span class="stat-num"><?= h(setting('coverage', '94%')) ?></span>
      <div class="stat-label">охват предприятий</div>
    </div>
    <div class="stat-item">
      <span class="stat-num"><?= h(setting('years_active', '69 лет')) ?></span>
      <div class="stat-label">на стороне работников</div>
    </div>
  </div>
</div>

<!-- ЧЕМ МЫ ПОМОГАЕМ -->
<section class="section section--alt">
  <div class="container">
    <div class="section-header">
      <div class="section-header-left">
        <div class="section-label">Наша работа</div>
        <h2 class="section-title">Чем профсоюз помогает работникам</h2>
      </div>
      <a href="<?= url('help.php') ?>" class="section-more">Подробнее →</a>
    </div>
    <div class="advantages-grid">
      <div class="advantage-card">
        <div class="advantage-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3>Правовая защита</h3>
        <p>Бесплатные юридические консультации и представление интересов в суде для каждого члена профсоюза.</p>
      </div>
      <div class="advantage-card">
        <div class="advantage-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
        </div>
        <h3>Переговоры с работодателем</h3>
        <p>Заключение коллективных договоров и соглашений, добивающихся достойной заработной платы и условий труда.</p>
      </div>
      <div class="advantage-card">
        <div class="advantage-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <h3>Социальная поддержка</h3>
        <p>Материальная помощь в трудных ситуациях, льготные путёвки, культурные мероприятия для членов профсоюза.</p>
      </div>
    </div>
  </div>
</section>

<!-- НОВОСТИ -->
<section class="section" id="news">
  <div class="container">
    <div class="section-header">
      <div class="section-header-left">
        <div class="section-label">Актуально</div>
        <h2 class="section-title">Последние новости</h2>
        <p class="section-desc">Следите за деятельностью профсоюза, важными событиями и достижениями.</p>
      </div>
      <a href="<?= url('news.php') ?>" class="section-more">Все новости →</a>
    </div>

    <?php if ($latestNews): ?>
    <div class="news-grid">
      <?php foreach ($latestNews as $item): ?>
      <a href="<?= url('news-single.php?slug=' . $item['slug']) ?>" class="news-card" style="text-decoration:none;color:inherit">
        <div class="news-card__img">
          <?php if ($item['image']): ?>
            <img src="<?= uploadUrl($item['image']) ?>" alt="<?= h($item['title']) ?>">
          <?php else: ?>
            <div class="news-card__img-placeholder">📰</div>
          <?php endif; ?>
        </div>
        <div class="news-card__body">
          <div class="news-card__meta">
            <span class="news-card__cat"><?= h(match($item['category']) {
              'events' => 'Мероприятие',
              'achievements' => 'Достижение',
              'announcements' => 'Объявление',
              default => 'Новость'
            }) ?></span>
            <span class="news-card__date"><?= formatDateRu($item['created_at']) ?></span>
          </div>
          <div class="news-card__title"><?= h($item['title']) ?></div>
          <?php if ($item['excerpt']): ?>
          <div class="news-card__excerpt"><?= h(truncate($item['excerpt'], 120)) ?></div>
          <?php endif; ?>
          <div class="news-card__link">Читать <span>→</span></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <div class="empty-state__icon">📰</div>
      <h3>Новостей пока нет</h3>
      <p>Скоро здесь появятся актуальные новости профсоюза.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ГАЛЕРЕЯ -->
<section class="section section--alt" id="gallery">
  <div class="container">
    <div class="section-header">
      <div class="section-header-left">
        <div class="section-label">Жизнь профсоюза</div>
        <h2 class="section-title">Фотогалерея</h2>
      </div>
      <a href="<?= url('gallery.php') ?>" class="section-more">Все фото →</a>
    </div>

    <?php if ($galleryItems): ?>
    <div class="gallery-grid">
      <?php foreach ($galleryItems as $item): ?>
      <div class="gallery-item" data-lightbox="gallery" data-src="<?= uploadUrl($item['file']) ?>" data-title="<?= h($item['title']) ?>">
        <img src="<?= uploadUrl($item['file']) ?>" alt="<?= h($item['title']) ?>" loading="lazy">
        <div class="gallery-item__overlay">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="gallery-grid">
      <?php for ($i = 0; $i < 4; $i++): ?>
      <div class="gallery-item" style="background: linear-gradient(135deg, #e8e4dc, #d4cfc6); cursor:default">
        <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:2rem;color:#9ca3af">📷</div>
      </div>
      <?php endfor; ?>
    </div>
    <p style="text-align:center;color:var(--text-muted);margin-top:20px">Фотографии появятся после добавления через <a href="<?= url('admin/') ?>" style="color:var(--accent)">панель администратора</a>.</p>
    <?php endif; ?>
  </div>
</section>

<!-- О ПРОФСОЮЗЕ -->
<section class="section">
  <div class="container">
    <div class="about-intro">
      <div class="about-intro__text">
        <div class="section-label">О нас</div>
        <h2>Защищаем права с <?= h(setting('site_since', '1957')) ?> года</h2>
        <p><?= h(setting('about_short')) ?></p>
        <p>Сегодня в профсоюзе состоит более <strong><?= h(setting('members_count')) ?></strong> работников из <strong><?= h(setting('orgs_count')) ?></strong> первичных организаций по всей Тверской области.</p>
        <div style="margin-top:28px;display:flex;gap:12px;flex-wrap:wrap">
          <a href="<?= url('about.php') ?>" class="btn btn-dark">Подробнее о нас</a>
          <a href="<?= url('docs.php') ?>" class="btn btn-outline">Документы</a>
        </div>
      </div>
      <div class="about-intro__img">
        <div class="about-intro__img-placeholder">🏛️</div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <div class="cta-inner">
      <div class="section-label">Вступить в профсоюз</div>
      <h2>Ваши права под защитой</h2>
      <p>Членство в профсоюзе — это реальная юридическая и социальная защита.<br>Более <?= h(setting('members_count')) ?> работников уже с нами.</p>
      <div class="cta-actions">
        <a href="<?= url('help.php#join') ?>" class="btn btn-primary btn-lg">Как вступить</a>
        <a href="<?= url('contacts.php') ?>" class="btn btn-outline btn-lg" style="border-color:rgba(255,255,255,.35);color:#fff">Задать вопрос</a>
      </div>
    </div>
  </div>
</section>

<!-- ЛАЙТБОКС -->
<div id="lightbox" class="lightbox-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.9);z-index:1000;align-items:center;justify-content:center;padding:20px">
  <button id="lb-close" style="position:absolute;top:20px;right:20px;background:none;border:none;color:#fff;font-size:32px;cursor:pointer;line-height:1">✕</button>
  <button id="lb-prev" style="position:absolute;left:20px;background:none;border:none;color:#fff;font-size:36px;cursor:pointer">‹</button>
  <img id="lb-img" src="" alt="" style="max-width:100%;max-height:90vh;border-radius:8px;object-fit:contain">
  <button id="lb-next" style="position:absolute;right:20px;background:none;border:none;color:#fff;font-size:36px;cursor:pointer">›</button>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
