<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$activeNav = 'contacts';
$pageTitle = 'Контакты';

$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $error = 'Ошибка безопасности. Попробуйте ещё раз.';
    } else {
        $name    = trim($_POST['name'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (!$name || !$message) {
            $error = 'Пожалуйста, заполните обязательные поля: имя и сообщение.';
        } else {
            DB::insert(
                "INSERT INTO contacts_form (name, phone, email, subject, message) VALUES (?, ?, ?, ?, ?)",
                [$name, $phone, $email, $subject, $message]
            );
            $success = true;
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumbs">
      <a href="<?= url() ?>">Главная</a>
      <span>›</span>
      <span>Контакты</span>
    </div>
    <h1>Контакты</h1>
    <p>Мы готовы ответить на ваши вопросы.</p>
  </div>
</div>

<div class="container">
  <div class="contacts-layout">
    <!-- Контактная информация -->
    <div class="contacts-info">
      <h2 style="margin-bottom:32px">Реквизиты организации</h2>

      <div class="contact-row">
        <div class="contact-row__icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div>
          <div class="contact-row__label">Адрес</div>
          <div class="contact-row__value"><?= h(setting('site_address')) ?></div>
        </div>
      </div>

      <div class="contact-row">
        <div class="contact-row__icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.12 1.22 2 2 0 012.11 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.09a16 16 0 006 6l.45-.45a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
        </div>
        <div>
          <div class="contact-row__label">Телефон</div>
          <div class="contact-row__value">
            <a href="tel:<?= h(str_replace([' ','(',')'], '', setting('site_phone'))) ?>"><?= h(setting('site_phone')) ?></a>
          </div>
        </div>
      </div>

      <div class="contact-row">
        <div class="contact-row__icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
        <div>
          <div class="contact-row__label">Email</div>
          <div class="contact-row__value">
            <a href="mailto:<?= h(setting('site_email')) ?>"><?= h(setting('site_email')) ?></a>
          </div>
        </div>
      </div>

      <div class="contact-row">
        <div class="contact-row__icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
          <div class="contact-row__label">Режим работы</div>
          <div class="contact-row__value"><?= h(setting('site_schedule')) ?></div>
        </div>
      </div>

      <?php if (setting('vk_url') || setting('telegram_url')): ?>
      <h3 style="margin-top:16px;margin-bottom:16px;font-size:1rem">Мы в социальных сетях</h3>
      <div style="display:flex;gap:10px">
        <?php if (setting('vk_url')): ?>
        <a href="<?= h(setting('vk_url')) ?>" target="_blank" class="btn btn-outline btn-sm">ВКонтакте</a>
        <?php endif; ?>
        <?php if (setting('telegram_url')): ?>
        <a href="<?= h(setting('telegram_url')) ?>" target="_blank" class="btn btn-outline btn-sm">Telegram</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <?php if (setting('contacts_map')): ?>
      <div class="map-wrapper" style="margin-top:32px">
        <?= setting('contacts_map') ?>
      </div>
      <?php else: ?>
      <div class="map-wrapper" style="margin-top:32px;flex-direction:column;gap:8px">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <span>Карта появится после настройки в панели администратора</span>
      </div>
      <?php endif; ?>
    </div>

    <!-- Форма обратной связи -->
    <div>
      <div class="contact-form">
        <h3>Написать нам</h3>

        <?php if ($success): ?>
        <div class="form-success">
          ✅ Ваше обращение отправлено! Мы свяжемся с вами в ближайшее время.
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="form-error"><?= h($error) ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" action="">
          <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">
          <div class="form-group">
            <label class="form-label">Ваше имя <span style="color:var(--accent)">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="Иван Иванов" required
                   value="<?= h($_POST['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Телефон</label>
            <input type="tel" name="phone" class="form-control" placeholder="+7 (___) ___-__-__"
                   value="<?= h($_POST['phone'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="example@mail.ru"
                   value="<?= h($_POST['email'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Тема обращения</label>
            <select name="subject" class="form-control">
              <option value="">— Выберите тему —</option>
              <option value="membership" <?= ($_POST['subject'] ?? '') === 'membership' ? 'selected' : '' ?>>Вступление в профсоюз</option>
              <option value="legal" <?= ($_POST['subject'] ?? '') === 'legal' ? 'selected' : '' ?>>Юридическая помощь</option>
              <option value="sanatorium" <?= ($_POST['subject'] ?? '') === 'sanatorium' ? 'selected' : '' ?>>Санаторно-курортное лечение</option>
              <option value="support" <?= ($_POST['subject'] ?? '') === 'support' ? 'selected' : '' ?>>Материальная помощь</option>
              <option value="other" <?= ($_POST['subject'] ?? '') === 'other' ? 'selected' : '' ?>>Другое</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Сообщение <span style="color:var(--accent)">*</span></label>
            <textarea name="message" class="form-control" rows="5" placeholder="Опишите ваш вопрос или ситуацию..." required><?= h($_POST['message'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
            Отправить обращение
          </button>
          <p style="font-size:.75rem;color:var(--text-muted);text-align:center;margin-top:12px">
            Нажимая «Отправить», вы соглашаетесь с обработкой персональных данных.
          </p>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
