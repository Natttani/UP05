<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . 'frontend/login.php');
}

$login    = trim((string) ($_POST['login'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

$errors = [];
if ($login === '')    $errors['login'] = 'Введите логин';
if ($password === '') $errors['password'] = 'Введите пароль';

if ($errors) {
    Validator::flashErrors($errors, ['login' => $login]);
    redirect(BASE_URL . 'frontend/login.php');
}

$user = User::findByLogin($login);

if (!$user || !password_verify($password, $user['password'])) {
    Validator::flashErrors(['login' => 'Неверный логин или пароль'], ['login' => $login]);
    redirect(BASE_URL . 'frontend/login.php');
}

Auth::login($user);

if ($user['role'] === 'admin') {
    flash_set('success', 'Вы вошли как администратор');
    redirect(BASE_URL . 'frontend/admin.php');
}

flash_set('success', 'Вы вошли в систему');
redirect(BASE_URL . 'frontend/applications.php');