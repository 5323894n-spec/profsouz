<?php
// Проверка авторизации администратора
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/login.php?r=' . urlencode($_SERVER['REQUEST_URI'] ?? ''));
    exit;
}
