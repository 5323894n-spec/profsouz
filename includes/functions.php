<?php
// =============================================
// Вспомогательные функции
// =============================================

function setting(string $key, string $default = ''): string {
    static $cache = [];
    if (!isset($cache[$key])) {
        $row = DB::fetchOne("SELECT value FROM settings WHERE key = ?", [$key]);
        $cache[$key] = $row ? $row['value'] : $default;
    }
    return $cache[$key];
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string {
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string {
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

function uploadUrl(string $path): string {
    return BASE_URL . '/uploads/' . ltrim($path, '/');
}

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function slug(string $text): string {
    $translit = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo',
        'ж'=>'zh','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m',
        'н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u',
        'ф'=>'f','х'=>'kh','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'shch',
        'ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
        'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'Yo',
        'Ж'=>'Zh','З'=>'Z','И'=>'I','Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M',
        'Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U',
        'Ф'=>'F','Х'=>'Kh','Ц'=>'Ts','Ч'=>'Ch','Ш'=>'Sh','Щ'=>'Shch',
        'Ъ'=>'','Ы'=>'Y','Ь'=>'','Э'=>'E','Ю'=>'Yu','Я'=>'Ya',
    ];
    $text = strtr($text, $translit);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'item';
}

function uniqueSlug(string $table, string $base, int $excludeId = 0): string {
    $s = slug($base);
    $original = $s;
    $i = 1;
    while (true) {
        $row = DB::fetchOne(
            "SELECT id FROM {$table} WHERE slug = ? AND id != ?",
            [$s, $excludeId]
        );
        if (!$row) break;
        $s = $original . '-' . $i++;
    }
    return $s;
}

function formatDate(string $date, string $format = 'd.m.Y'): string {
    $ts = strtotime($date);
    return $ts ? date($format, $ts) : $date;
}

function formatDateRu(string $date): string {
    $months = ['','января','февраля','марта','апреля','мая','июня',
                'июля','августа','сентября','октября','ноября','декабря'];
    $ts = strtotime($date);
    if (!$ts) return $date;
    return date('j', $ts) . ' ' . $months[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

function uploadFile(array $file, string $subdir, array $allowedTypes = []): string|false {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if (!$allowedTypes) {
        $allowedTypes = ['image/jpeg','image/png','image/gif','image/webp',
                         'application/pdf','application/msword',
                         'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    }
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, $allowedTypes)) return false;

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = uniqid() . '.' . strtolower($ext);
    $dir = UPLOADS_DIR . '/' . $subdir;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) return false;
    return $subdir . '/' . $name;
}

function deleteUpload(string $path): void {
    $file = UPLOADS_DIR . '/' . ltrim($path, '/');
    if (file_exists($file)) unlink($file);
}

function paginate(int $total, int $perPage, int $current): array {
    $pages = (int)ceil($total / $perPage);
    return [
        'total'   => $total,
        'pages'   => $pages,
        'current' => $current,
        'offset'  => ($current - 1) * $perPage,
        'prev'    => $current > 1 ? $current - 1 : null,
        'next'    => $current < $pages ? $current + 1 : null,
    ];
}

function truncate(string $text, int $len = 160): string {
    $text = strip_tags($text);
    if (mb_strlen($text) <= $len) return $text;
    return mb_substr($text, 0, $len) . '…';
}

function csrf(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function verifyCsrf(): bool {
    $token = $_POST['_csrf'] ?? '';
    return hash_equals($_SESSION['csrf'] ?? '', $token);
}

function flash(string $key, string $msg = ''): string {
    if ($msg) {
        $_SESSION['flash'][$key] = $msg;
        return '';
    }
    $val = $_SESSION['flash'][$key] ?? '';
    unset($_SESSION['flash'][$key]);
    return $val;
}
