<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$activeNav = 'help';
$pageTitle = 'Помощь членам';

$faqItems = DB::fetchAll("SELECT * FROM help_items WHERE published = 1 ORDER BY category, sort_order");

// Группируем по категориям
$faqGrouped = [];
foreach ($faqItems as $item) {
    $faqGrouped[$item['category']][] = $item;
}

$faqCatNames = [
    'membership' => 'Вступление и членство',
    'legal'      => 'Юридическая помощь',
    'support'    => 'Материальная поддержка',
    'rights'     => 'Трудовые права',
    'general'    => 'Общие вопросы',
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumbs">
      <a href="<?= url() ?>">Главная</a>
      <span>›</span>
      <span>Помощь членам</span>
    </div>
    <h1>Помощь членам профсоюза</h1>
    <p>Правовая защита, социальная поддержка, консультации — для каждого члена профсоюза.</p>
  </div>
</div>

<!-- Виды помощи -->
<section class="section section--alt">
  <div class="container">
    <div class="section-label">Что мы предлагаем</div>
    <h2 class="section-title" style="margin-bottom:40px">Виды помощи</h2>
    <div class="advantages-grid">
      <div class="advantage-card">
        <div class="advantage-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3>Юридическая помощь</h3>
        <p>Бесплатные консультации правового инспектора, помощь в составлении исковых заявлений, представление интересов в суде и трудовой инспекции.</p>
        <p style="margin-top:10px;font-size:.875rem;color:var(--accent);font-weight:600">Бесплатно для членов профсоюза</p>
      </div>
      <div class="advantage-card">
        <div class="advantage-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
        </div>
        <h3>Материальная помощь</h3>
        <p>Единовременная материальная помощь членам профсоюза при трудной жизненной ситуации, тяжёлой болезни, потере кормильца.</p>
      </div>
      <div class="advantage-card">
        <div class="advantage-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <h3>Санаторно-курортное</h3>
        <p>Льготные путёвки в санатории и дома отдыха по сниженным ценам. Ежегодный приём заявлений в I полугодии.</p>
      </div>
    </div>
  </div>
</section>

<!-- Как вступить -->
<section class="section" id="join">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 460px;gap:60px;align-items:start">
      <div>
        <div class="section-label">Вступление</div>
        <h2 style="margin-bottom:20px">Как вступить в профсоюз</h2>
        <p style="color:var(--text-muted);margin-bottom:28px">Членом профсоюза может стать любой работник организаций жилищно-коммунального хозяйства, дорожного и лесного хозяйства Тверской области.</p>

        <div style="display:flex;flex-direction:column;gap:16px">
          <?php
          $steps = [
            ['1', 'Подайте заявление', 'Напишите заявление на имя председателя первичной профсоюзной организации на вашем предприятии.'],
            ['2', 'Уплатите взнос', 'Членский взнос — 1% от ежемесячного заработка. Начисляется с момента вступления.'],
            ['3', 'Получите билет', 'После принятия решения профсоюзным комитетом вам выдадут профсоюзный билет.'],
            ['4', 'Пользуйтесь льготами', 'Юридическая помощь, материальная поддержка и путёвки — всё доступно сразу после вступления.'],
          ];
          foreach ($steps as [$num, $title, $desc]):
          ?>
          <div style="display:flex;gap:16px;align-items:flex-start">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.875rem;font-weight:800;flex-shrink:0"><?= $num ?></div>
            <div>
              <div style="font-weight:700;margin-bottom:4px"><?= $title ?></div>
              <p style="font-size:.9375rem;color:var(--text-muted)"><?= $desc ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <div style="margin-top:32px">
          <a href="<?= url('contacts.php') ?>" class="btn btn-primary btn-lg">Задать вопрос о вступлении</a>
        </div>
      </div>

      <div style="background:var(--bg-card);border-radius:var(--radius-lg);padding:36px;border:1px solid var(--border)">
        <h3 style="margin-bottom:20px">Преимущества членства</h3>
        <?php
        $perks = [
          ['✅', 'Бесплатная юридическая помощь'],
          ['✅', 'Представление в суде'],
          ['✅', 'Льготные путёвки'],
          ['✅', 'Материальная помощь'],
          ['✅', 'Скидки у партнёров'],
          ['✅', 'Участие в культурных мероприятиях'],
          ['✅', 'Защита при увольнении'],
          ['✅', 'Консультации по охране труда'],
        ];
        foreach ($perks as [$icon, $text]):
        ?>
        <div style="display:flex;gap:10px;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);font-size:.9375rem">
          <span><?= $icon ?></span>
          <span><?= $text ?></span>
        </div>
        <?php endforeach; ?>

        <div style="margin-top:24px;padding:16px;background:rgba(201,100,66,.08);border-radius:8px;text-align:center">
          <div style="font-size:.8125rem;color:var(--text-muted)">Членский взнос</div>
          <div style="font-size:1.75rem;font-weight:800;color:var(--accent)">1%</div>
          <div style="font-size:.875rem;color:var(--text-muted)">от ежемесячного заработка</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="section section--alt">
  <div class="container">
    <div class="section-label">Вопросы и ответы</div>
    <h2 class="section-title" style="margin-bottom:40px">Частые вопросы</h2>

    <?php if ($faqItems): ?>
      <?php foreach ($faqGrouped as $cat => $items): ?>
      <h3 style="font-size:1.125rem;margin-bottom:16px;margin-top:32px;color:var(--text-muted)"><?= h($faqCatNames[$cat] ?? ucfirst($cat)) ?></h3>
      <div class="faq-list">
        <?php foreach ($items as $item): ?>
        <div class="faq-item">
          <div class="faq-question">
            <span><?= h($item['question']) ?></span>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6l4 4 4-4"/></svg>
          </div>
          <div class="faq-answer"><?= nl2br(h($item['answer'])) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
    <div class="empty-state">
      <div class="empty-state__icon">❓</div>
      <h3>Раздел в разработке</h3>
      <p>Если у вас есть вопросы, обратитесь к нам напрямую.</p>
      <a href="<?= url('contacts.php') ?>" class="btn btn-dark" style="margin-top:20px">Контакты</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
