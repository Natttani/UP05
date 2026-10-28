<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . 'frontend/register.php');
}

$data = [
    'login'     => (string) ($_POST['login'] ?? ''),
    'password'  => (string) ($_POST['password'] ?? ''),
    'full_name' => (string) ($_POST['full_name'] ?? ''),
    'phone'     => (string) ($_POST['phone'] ?? ''),
    'email'     => (string) ($_POST['email'] ?? ''),
];

$v = new Validator($data);
$v->required('login')->login('login')
  ->required('password')->password('password')
  ->required('full_name')->fullName('full_name')
  ->required('phone')->phone('phone')
  ->required('email')->email('email');

if (!$v->fails() && User::loginExists(trim($data['login']))) {
    $_SESSION['errors'] = ['login' => 'Такой логин уже занят'];
    Validator::flashErrors($_SESSION['errors'], $data);
    redirect(BASE_URL . 'frontend/register.php');
}

if ($v->fails()) {
    Validator::flashErrors($v->errors(), $data);
    redirect(BASE_URL . 'frontend/register.php');
}

User::create([
    'login'     => trim($data['login']),
    'password'  => $data['password'],
    'full_name' => trim($data['full_name']),
    'phone'     => trim($data['phone']),
    'email'     => trim($data['email']),
    'role'      => 'user',
]);

flash_set('success', 'Пользователь создан. Войдите в систему.');
redirect(BASE_URL . 'frontend/login.php');