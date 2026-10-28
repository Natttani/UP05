<?php
declare(strict_types=1);

require_once __DIR__ . '/backend/bootstrap.php';


if (Auth::check()) {
    redirect(Auth::isAdmin() ? BASE_URL . 'frontend/admin.php' : BASE_URL . 'frontend/applications.php');
}
redirect(BASE_URL . 'frontend/login.php');