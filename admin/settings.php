<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$adminTitle = 'Настройки сайта';
$adminPage  = 'settings';

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        redirect(BASE_URL . '/admin/settings.php');
    }

    $fields = [
        'site_name', 'site_tagline', 'site_since', 'site_email', 'site_phone',
        'site_address', 'site_schedule', 'vk_url', 'telegram_url',
        'members_count', 'orgs_count', 'coverage', 'years_active',
        'hero_title', 'hero_text', 'about_short', 'contacts_map',
    ];

    $stmt = DB::get()->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)");
    foreach ($fields as $f) {
        $stmt->execute([$f, trim($_POST[$f] ?? '')]);
    }

    flash('success', 'Настройки сохранены.');
    redirect(BASE_URL . '/admin/settings.php');
}

if ($msg = flash('success')) $success = $msg;

// Загружаем все настройки
$allSettings = [];
foreach (DB::fetchAll("SELECT key, value FROM settings") as $row) {
    $allSettings[$row['key']] = $row['value'];
}
function s(string $k, array &$all, string $def = ''): string {
    return htmlspecialchars($all[$k] ?? $def, ENT_QUOTES, 'UTF-8');
}

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($success): ?><div class="alert alert-success">✅ <?= h($success) ?></div><?php endif; ?>

<form method="POST">
  <input type="hidden" name="_csrf" value="<?= h(csrf()) ?>">

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start">

    <!-- Основное -->
    <div>
      <div class="card" style="margin-bottom:16px">
        <h3 style="font-size:1rem;margin-bottom:20px;padding-bottom:10px;border-bottom:1px solid var(--border)">Основная информация</h3>
        <div class="form-group">
          <label class="form-label">Название профсоюза</label>
          <input type="text" name="site_name" class="form-control" value="<?= s('site_name', $allSettings) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Подзаголовок / описание</label>
          <input type="text" name="site_tagline" class="form-control" value="<?= s('site_tagline', $allSettings) ?>">
        </div>
        <div class="form-row form-row-2">
          <div class="form-group" style="margin:0">
            <label class="form-label">Год основания</label>
            <input type="text" name="site_since" class="form-control" value="<?= s('site_since', $allSettings, '1957') ?>">
          </div>
        </div>
      </div>

      <div class="card" style="margin-bottom:16px">
        <h3 style="font-size:1rem;margin-bottom:20px;padding-bottom:10px;border-bottom:1px solid var(--border)">Контактная информация</h3>
        <div class="form-group">
          <label class="form-label">Адрес офиса</label>
          <input type="text" name="site_address" class="form-control" value="<?= s('site_address', $allSettings) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Телефон</label>
          <input type="text" name="site_phone" class="form-control" value="<?= s('site_phone', $allSettings) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="site_email" class="form-control" value="<?= s('site_email', $allSettings) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Режим работы</label>
          <input type="text" name="site_schedule" class="form-control" value="<?= s('site_schedule', $allSettings) ?>" placeholder="Пн–Пт: 9:00–18:00">
        </div>
        <div class="form-group">
          <label class="form-label">ВКонтакте (URL)</label>
          <input type="text" name="vk_url" class="form-control" value="<?= s('vk_url', $allSettings) ?>">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">Telegram (URL)</label>
          <input type="text" name="telegram_url" class="form-control" value="<?= s('telegram_url', $allSettings) ?>">
        </div>
      </div>

      <div class="card">
        <h3 style="font-size:1rem;margin-bottom:20px;padding-bottom:10px;border-bottom:1px solid var(--border)">Карта (Яндекс/Google)</h3>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">Код карты <small>(iframe embed-код)</small></label>
          <textarea name="contacts_map" class="form-control" rows="4" placeholder='&lt;iframe src="https://maps.yandex.ru/..."&gt;&lt;/iframe&gt;'><?= s('contacts_map', $allSettings) ?></textarea>
          <p class="form-hint">Откройте Яндекс.Карты, найдите адрес, нажмите «Поделиться» → «HTML-код» и вставьте iframe.</p>
        </div>
      </div>
    </div>

    <!-- Контент главной страницы -->
    <div>
      <div class="card" style="margin-bottom:16px">
        <h3 style="font-size:1rem;margin-bottom:20px;padding-bottom:10px;border-bottom:1px solid var(--border)">Статистика (главная страница)</h3>
        <div class="form-row form-row-2">
          <div class="form-group" style="margin:0">
            <label class="form-label">Членов профсоюза</label>
            <input type="text" name="members_count" class="form-control" value="<?= s('members_count', $allSettings, '12 416') ?>">
          </div>
          <div class="form-group" style="margin:0">
            <label class="form-label">Первичных организаций</label>
            <input type="text" name="orgs_count" class="form-control" value="<?= s('orgs_count', $allSettings, '68') ?>">
          </div>
        </div>
        <div class="form-row form-row-2" style="margin-top:16px">
          <div class="form-group" style="margin:0">
            <label class="form-label">Охват предприятий</label>
            <input type="text" name="coverage" class="form-control" value="<?= s('coverage', $allSettings, '94%') ?>">
          </div>
          <div class="form-group" style="margin:0">
            <label class="form-label">Лет на стороне работников</label>
            <input type="text" name="years_active" class="form-control" value="<?= s('years_active', $allSettings, '69 лет') ?>">
          </div>
        </div>
      </div>

      <div class="card" style="margin-bottom:16px">
        <h3 style="font-size:1rem;margin-bottom:20px;padding-bottom:10px;border-bottom:1px solid var(--border)">Главный баннер (герой)</h3>
        <div class="form-group">
          <label class="form-label">Заголовок баннера</label>
          <input type="text" name="hero_title" class="form-control" value="<?= s('hero_title', $allSettings) ?>">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">Текст баннера</label>
          <textarea name="hero_text" class="form-control" rows="3"><?= s('hero_text', $allSettings) ?></textarea>
        </div>
      </div>

      <div class="card">
        <h3 style="font-size:1rem;margin-bottom:20px;padding-bottom:10px;border-bottom:1px solid var(--border)">О профсоюзе (краткое)</h3>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">Краткое описание <small>(блок на главной странице)</small></label>
          <textarea name="about_short" class="form-control" rows="4"><?= s('about_short', $allSettings) ?></textarea>
        </div>
      </div>
    </div>
  </div>

  <div style="margin-top:20px;position:sticky;bottom:0;background:var(--bg);padding:16px 0">
    <button type="submit" class="btn btn-primary btn-lg">💾 Сохранить все настройки</button>
  </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
