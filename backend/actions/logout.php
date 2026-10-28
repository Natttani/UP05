<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . 'index.php');
}

Auth::logout();
flash_set('success', 'Вы вышли из системы');
redirect(BASE_URL . 'frontend/login.php');