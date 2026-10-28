<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
Auth::requireUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . 'frontend/application_create.php');
}

$data = [
    'address'             => trim((string) ($_POST['address'] ?? '')),
    'phone'               => trim((string) ($_POST['phone'] ?? '')),
    'date'                => trim((string) ($_POST['date'] ?? '')),
    'time'                => trim((string) ($_POST['time'] ?? '')),
    'service_id'          => trim((string) ($_POST['service_id'] ?? '')),
    'payment_id'          => trim((string) ($_POST['payment_id'] ?? '')),
    'other_service'       => !empty($_POST['other_service']),
    'custom_service_desc' => trim((string) ($_POST['custom_service_desc'] ?? '')),
];

$v = new Validator($data);
$v->required('address')
  ->required('phone')->phone('phone')
  ->required('date')->dateDmy('date')
  ->required('time')->timeHm('time')
  ->required('service_id')
  ->required('payment_id');


$pdo = Database::pdo();
$serviceOk = $pdo->prepare('SELECT 1 FROM services WHERE id = :id');
$serviceOk->execute([':id' => (int) $data['service_id']]);
if (!$serviceOk->fetchColumn()) {
    $v = $v;
}

$errors = $v->errors();

if (empty($errors['service_id'])) {
    $chk = $pdo->prepare('SELECT 1 FROM services WHERE id = :id');
    $chk->execute([':id' => (int) $data['service_id']]);
    if (!$chk->fetchColumn()) {
        $errors['service_id'] = 'Выберите услугу из списка';
    }
}
if (empty($errors['payment_id'])) {
    $chk = $pdo->prepare('SELECT 1 FROM payment_methods WHERE id = :id');
    $chk->execute([':id' => (int) $data['payment_id']]);
    if (!$chk->fetchColumn()) {
        $errors['payment_id'] = 'Выберите способ оплаты';
    }
}

if ($data['other_service']) {
    if ($data['custom_service_desc'] === '') {
        $errors['custom_service_desc'] = 'Опишите иную услугу';
    }
    $data['service_id'] = (string) SERVICE_OTHER_ID;
}

if ($errors) {
    Validator::flashErrors($errors, $data);
    redirect(BASE_URL . 'frontend/application_create.php');
}

$dt = DateTime::createFromFormat('d.m.Y', $data['date']);
$isoDate = $dt->format('Y-m-d');

Application::create(Auth::id(), [
    'address'             => $data['address'],
    'phone'               => $data['phone'],
    'date'                => $isoDate,
    'time'                => $data['time'],
    'service_id'          => (int) $data['service_id'],
    'payment_id'          => (int) $data['payment_id'],
    'custom_service_desc' => $data['other_service'] ? $data['custom_service_desc'] : null,
]);

flash_set('success', 'Заявка отправлена администратору');
redirect(BASE_URL . 'frontend/applications.php');