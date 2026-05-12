<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$db = DB::get();

// Создание таблиц
$db->exec("
-- Администраторы
CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    name TEXT NOT NULL,
    created_at TEXT DEFAULT (datetime('now','localtime'))
);

-- Настройки сайта
CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT
);

-- Новости и статьи
CREATE TABLE IF NOT EXISTS news (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    excerpt TEXT,
    content TEXT,
    image TEXT,
    category TEXT DEFAULT 'news',
    published INTEGER DEFAULT 1,
    views INTEGER DEFAULT 0,
    created_at TEXT DEFAULT (datetime('now','localtime')),
    updated_at TEXT DEFAULT (datetime('now','localtime'))
);

-- Категории новостей
CREATE TABLE IF NOT EXISTS news_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0
);

-- Галерея
CREATE TABLE IF NOT EXISTS gallery (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    file TEXT NOT NULL,
    type TEXT DEFAULT 'photo',
    album TEXT,
    sort_order INTEGER DEFAULT 0,
    published INTEGER DEFAULT 1,
    created_at TEXT DEFAULT (datetime('now','localtime'))
);

-- Альбомы галереи
CREATE TABLE IF NOT EXISTS gallery_albums (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    cover TEXT,
    sort_order INTEGER DEFAULT 0
);

-- Документы
CREATE TABLE IF NOT EXISTS documents (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    file TEXT NOT NULL,
    category TEXT DEFAULT 'general',
    sort_order INTEGER DEFAULT 0,
    published INTEGER DEFAULT 1,
    created_at TEXT DEFAULT (datetime('now','localtime'))
);

-- Члены профсоюза (руководство)
CREATE TABLE IF NOT EXISTS members (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    position TEXT,
    photo TEXT,
    phone TEXT,
    email TEXT,
    description TEXT,
    sort_order INTEGER DEFAULT 0,
    published INTEGER DEFAULT 1
);

-- Первичные организации
CREATE TABLE IF NOT EXISTS organizations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    address TEXT,
    chairman TEXT,
    phone TEXT,
    members_count INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    published INTEGER DEFAULT 1
);

-- Обращения/заявки (форма обратной связи)
CREATE TABLE IF NOT EXISTS contacts_form (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT,
    email TEXT,
    subject TEXT,
    message TEXT NOT NULL,
    status TEXT DEFAULT 'new',
    admin_note TEXT,
    created_at TEXT DEFAULT (datetime('now','localtime'))
);

-- Полезные ссылки/партнёры
CREATE TABLE IF NOT EXISTS links (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    url TEXT NOT NULL,
    logo TEXT,
    sort_order INTEGER DEFAULT 0,
    published INTEGER DEFAULT 1
);

-- Страница \"Помощь членам\" — FAQ/помощь
CREATE TABLE IF NOT EXISTS help_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    category TEXT DEFAULT 'general',
    sort_order INTEGER DEFAULT 0,
    published INTEGER DEFAULT 1
);

-- Статический контент страниц
CREATE TABLE IF NOT EXISTS pages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT NOT NULL UNIQUE,
    title TEXT NOT NULL,
    content TEXT,
    updated_at TEXT DEFAULT (datetime('now','localtime'))
);
");

// Начальные данные
// Администратор по умолчанию (логин: admin, пароль: admin123)
$db->exec("INSERT OR IGNORE INTO admins (username, password, name)
    VALUES ('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'Администратор')");

// Настройки сайта
$settings = [
    'site_name'        => 'Профсоюз 69',
    'site_tagline'     => 'Тверская областная организация профсоюза работников жизнеобеспечения',
    'site_since'       => '1957',
    'site_email'       => 'info@profsouz69.ru',
    'site_phone'       => '+7 (4822) 00-00-00',
    'site_address'     => 'г. Тверь, ул. Советская, д. 1',
    'site_schedule'    => 'Пн–Пт: 9:00–18:00',
    'vk_url'           => 'https://vk.com/',
    'telegram_url'     => '',
    'members_count'    => '12 416',
    'orgs_count'       => '68',
    'coverage'         => '94%',
    'years_active'     => '69 лет',
    'hero_title'       => 'Защищаем права каждого работника',
    'hero_text'        => 'Тверская областная организация профсоюза работников жилищно-коммунального хозяйства, дорожного и лесного хозяйства с 1957 года.',
    'about_short'      => 'Профсоюз 69 — это объединение работников жизнеобеспечения Тверской области. Мы защищаем трудовые права, добиваемся достойных условий труда и заработной платы.',
    'contacts_map'     => '',
];
$stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)");
foreach ($settings as $k => $v) {
    $stmt->execute([$k, $v]);
}

// Категории новостей
$db->exec("INSERT OR IGNORE INTO news_categories (slug, name, sort_order) VALUES
    ('news', 'Новости', 1),
    ('events', 'Мероприятия', 2),
    ('achievements', 'Достижения', 3),
    ('announcements', 'Объявления', 4)");

// Тестовые новости
$news_samples = [
    ['Подписано отраслевое соглашение на 2026—2028 годы',
     'podpisano-soglashenie-2026-2028',
     'Профсоюз подписал новое отраслевое соглашение, которое предусматривает повышение заработной платы и улучшение условий труда.',
     '<p>Тверская областная организация профсоюза работников жизнеобеспечения подписала отраслевое соглашение на 2026—2028 годы. Документ предусматривает ежегодное повышение заработной платы не ниже уровня инфляции, улучшение условий охраны труда и социальные гарантии для работников.</p><p>Соглашение подписано совместно с работодателями отрасли и органами власти Тверской области. Документ вступает в силу с 1 января 2026 года.</p>',
     'achievements'],
    ['Профсоюз восстановил 4 кондуктора через суд',
     'vosstanovleny-4-kondyktora',
     'По результатам судебных разбирательств при поддержке профсоюза четыре кондуктора восстановлены на рабочих местах.',
     '<p>Правовой отдел профсоюза провёл успешную работу по защите прав четырёх кондукторов, незаконно уволенных с предприятия городского транспорта. После обращения в суд все четверо восстановлены в должности с выплатой компенсации.</p><p>Это ещё раз подтверждает: членство в профсоюзе — реальная защита ваших трудовых прав.</p>',
     'achievements'],
    ['Конференция: итоги работы за год',
     'konferenciya-itogi-goda',
     'Состоялась ежегодная конференция членов профсоюза, на которой подведены итоги работы за прошедший год.',
     '<p>На конференции профсоюза собрались делегаты от 68 первичных организаций региона. Председатель профсоюза представил отчёт о проделанной работе: заключённые соглашения, выигранные судебные дела, оказанная материальная помощь членам профсоюза.</p>',
     'events'],
    ['Начался приём заявлений на санаторно-курортное лечение',
     'priyom-zayavleniy-sanatoriy',
     'Члены профсоюза могут подать заявки на получение льготных путёвок в санатории.',
     '<p>Профсоюз объявляет о начале приёма заявлений на санаторно-курортное лечение в 2026 году. Члены профсоюза имеют право на частичную компенсацию стоимости путёвки.</p><p>Заявления принимаются в офисе профсоюза по адресу: г. Тверь, ул. Советская, д. 1. Срок подачи — до 1 июля 2026 года.</p>',
     'announcements'],
    ['День работников ЖКХ: праздничные мероприятия',
     'den-rabotnikov-zhkh-meropriyatiya',
     'Профсоюз организует торжественные мероприятия в честь Дня работников жилищно-коммунального хозяйства.',
     '<p>20 марта — профессиональный праздник работников ЖКХ. Профсоюз подготовил программу торжественных мероприятий: награждение лучших работников отрасли, концертная программа, товарищеский ужин.</p>',
     'events'],
];

$stmt = $db->prepare("INSERT OR IGNORE INTO news (title, slug, excerpt, content, category, published) VALUES (?, ?, ?, ?, ?, 1)");
foreach ($news_samples as $n) {
    $stmt->execute($n);
}

// Образцы руководства профсоюза
$db->exec("INSERT OR IGNORE INTO members (name, position, sort_order, published) VALUES
    ('Иванов Алексей Петрович', 'Председатель профсоюза', 1, 1),
    ('Смирнова Ольга Николаевна', 'Заместитель председателя', 2, 1),
    ('Козлов Дмитрий Владимирович', 'Председатель ревизионной комиссии', 3, 1),
    ('Петрова Марина Сергеевна', 'Главный правовой инспектор труда', 4, 1),
    ('Федоров Сергей Александрович', 'Технический инспектор труда', 5, 1)");

// Примеры организаций
$db->exec("INSERT OR IGNORE INTO organizations (name, address, chairman, members_count, sort_order, published) VALUES
    ('ПО «ТверьТеплоСеть»', 'г. Тверь, ул. Тепловая, 5', 'Михайлов А.В.', 312, 1, 1),
    ('МУП «Тверьводоканал»', 'г. Тверь, пр. Победы, 32', 'Степанова Е.К.', 287, 2, 1),
    ('МУП «ПАТП-1»', 'г. Тверь, ул. Транспортная, 15', 'Николаев П.С.', 198, 3, 1),
    ('ООО «ТверьЖКС»', 'г. Тверь, ул. Коммунальная, 8', 'Орлова Н.В.', 156, 4, 1)");

// Страницы
$db->exec("INSERT OR IGNORE INTO pages (slug, title, content) VALUES
    ('about', 'О профсоюзе', '<h2>История профсоюза</h2><p>Тверская областная организация профсоюза работников жилищно-коммунального хозяйства, дорожного и лесного хозяйства образована в 1957 году. За прошедшие десятилетия профсоюз объединил тысячи работников отрасли и стал надёжным защитником их трудовых прав.</p><h2>Наша миссия</h2><p>Мы защищаем права и интересы работников жизнеобеспечения Тверской области: ведём переговоры с работодателями, заключаем коллективные договоры, представляем интересы членов в судах.</p>'),
    ('help', 'Помощь членам профсоюза', '<p>Членство в профсоюзе даёт вам реальную защиту ваших трудовых прав. Наши специалисты готовы помочь в решении любых трудовых споров.</p>'),
    ('contacts', 'Контакты', '')");

// FAQ
$db->exec("INSERT OR IGNORE INTO help_items (question, answer, category, sort_order, published) VALUES
    ('Как вступить в профсоюз?', 'Для вступления в профсоюз необходимо подать заявление на имя председателя первичной профсоюзной организации на вашем предприятии. Если первичной организации нет — обратитесь в региональный офис профсоюза.', 'membership', 1, 1),
    ('Сколько составляет членский взнос?', 'Членский взнос составляет 1% от ежемесячного заработка. Для пенсионеров — бесплатно.', 'membership', 2, 1),
    ('Какую юридическую помощь оказывает профсоюз?', 'Правовая инспекция профсоюза оказывает бесплатную юридическую помощь членам профсоюза: консультации, составление документов, представление интересов в суде.', 'legal', 3, 1),
    ('Как получить материальную помощь?', 'Члены профсоюза могут обратиться за материальной помощью при наступлении трудной жизненной ситуации. Заявление подаётся в первичную организацию или в региональный офис.', 'support', 4, 1),
    ('Как получить льготную путёвку?', 'Профсоюз ежегодно распределяет льготные путёвки в санатории и дома отдыха. Заявления принимаются в I полугодии текущего года в региональном офисе.', 'support', 5, 1)");

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Установка</title><style>body{font-family:sans-serif;max-width:600px;margin:50px auto;padding:20px}h1{color:#1c2530}.ok{color:#27ae60}.btn{background:#1c2530;color:#fff;padding:12px 24px;text-decoration:none;border-radius:8px;display:inline-block;margin-top:20px}</style></head><body>";
echo "<h1>Установка сайта Профсоюза 69</h1>";
echo "<p class='ok'>✓ База данных успешно создана</p>";
echo "<p class='ok'>✓ Таблицы созданы</p>";
echo "<p class='ok'>✓ Начальные данные загружены</p>";
echo "<hr><p><strong>Данные для входа в админ-панель:</strong><br>Логин: <code>admin</code><br>Пароль: <code>admin123</code></p>";
echo "<p style='color:#c0392b'><strong>⚠ Важно:</strong> После установки смените пароль в настройках и удалите файл <code>setup.php</code> с сервера!</p>";
echo "<a href='admin/' class='btn'>Перейти в админ-панель</a> &nbsp; <a href='index.php' class='btn' style='background:#c96442'>На сайт</a>";
echo "</body></html>";
