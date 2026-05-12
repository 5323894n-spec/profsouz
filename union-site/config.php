<?php
// =============================================
// Конфигурация сайта
// =============================================

define('SITE_ROOT', __DIR__);
define('DB_PATH', SITE_ROOT . '/data/union.db');
define('UPLOADS_DIR', SITE_ROOT . '/uploads');
define('UPLOADS_URL', '/uploads');

// Базовый URL сайта (без слеша в конце)
// Для локального запуска оставьте пустым или укажите http://localhost/union-site
define('BASE_URL', '');

// Часовой пояс
date_default_timezone_set('Europe/Moscow');

// Режим разработки (false на боевом сервере)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
