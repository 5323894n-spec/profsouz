<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$activeNav = 'docs';
$pageTitle = 'Документы';

$docs = DB::fetchAll("SELECT * FROM documents WHERE published = 1 ORDER BY category, sort_order, created_at DESC");

// Группируем по категориям
$grouped = [];
foreach ($docs as $doc) {
    $grouped[$doc['category']][] = $doc;
}

$catNames = [
    'general'     => 'Общие документы',
    'agreements'  => 'Соглашения',
    'collective'  => 'Коллективные договоры',
    'regulations' => 'Положения и регламенты',
    'reports'     => 'Отчёты',
    'templates'   => 'Шаблоны документов',
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumbs">
      <a href="<?= url() ?>">Главная</a>
      <span>›</span>
      <span>Документы</span>
    </div>
    <h1>Документы</h1>
    <p>Нормативные акты, соглашения, образцы заявлений и другие материалы профсоюза.</p>
  </div>
</div>

<section class="section">
  <div class="container">
    <?php if ($docs): ?>
      <?php foreach ($grouped as $cat => $items): ?>
      <div style="margin-bottom:48px">
        <h2 style="font-size:1.25rem;margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid var(--border)">
          <?= h($catNames[$cat] ?? ucfirst($cat)) ?>
        </h2>
        <div class="docs-list">
          <?php foreach ($items as $doc): ?>
          <div class="doc-item">
            <div class="doc-icon">
              <?php
              $ext = strtolower(pathinfo($doc['file'], PATHINFO_EXTENSION));
              if ($ext === 'pdf'): ?>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
              <?php elseif (in_array($ext, ['doc','docx'])): ?>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              <?php else: ?>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
              <?php endif; ?>
            </div>
            <div class="doc-info">
              <div class="doc-title"><?= h($doc['title']) ?></div>
              <?php if ($doc['description']): ?>
              <div class="doc-desc"><?= h($doc['description']) ?></div>
              <?php endif; ?>
              <div class="doc-desc" style="margin-top:4px"><?= date('d.m.Y', strtotime($doc['created_at'])) ?> · <?= strtoupper($ext) ?></div>
            </div>
            <a href="<?= uploadUrl($doc['file']) ?>" download class="doc-download" target="_blank">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 2v9M4 8l4 4 4-4M2 14h12"/></svg>
              Скачать
            </a>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
    <div class="empty-state">
      <div class="empty-state__icon">📋</div>
      <h3>Документов пока нет</h3>
      <p>Документы будут размещены в ближайшее время.<br>По вопросам обращайтесь в офис профсоюза.</p>
      <a href="<?= url('contacts.php') ?>" class="btn btn-dark" style="margin-top:20px">Контакты</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
