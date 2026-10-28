<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . 'frontend/admin.php');
}

$appId       = (int) ($_POST['application_id'] ?? 0);
$newStatusId = (int) ($_POST['status_id'] ?? 0);
$reason      = trim((string) ($_POST['cancel_reason'] ?? ''));

// Сохраним фильтры и страницу, чтобы вернуться туда же
$backStatus  = (int) ($_POST['back_status'] ?? 0);
$backService = (int) ($_POST['back_service'] ?? 0);
$backPage    = max(1, (int) ($_POST['back_page'] ?? 1));

$query = http_build_query(array_filter([
    'status'  => $backStatus  ?: null,
    'service' => $backService ?: null,
    'page'    => $backPage > 1 ? $backPage : null,
]));
$backUrl = BASE_URL . 'frontend/admin.php' . ($query ? '?' . $query : '');

$app = Application::find($appId);
if (!$app) {
    flash_set('error', 'Заявка не найдена');
    redirect($backUrl);
}

// Допустимые статусы: только те, на которые можно перейти
$allowed = Status::TRANSITIONS[(int) $app['status_id']] ?? [];
if (!in_array($newStatusId, $allowed, true)) {
    flash_set('error', 'Недопустимый переход статуса');
    redirect($backUrl);
}

if ($newStatusId === Status::CANCELLED && $reason === '') {
    flash_set('error', 'Укажите причину отмены');
    redirect($backUrl);
}

if (!Application::changeStatus($appId, $newStatusId, $reason)) {
    flash_set('error', 'Не удалось изменить статус');
    redirect($backUrl);
}

flash_set('success', 'Статус заявки обновлён');
redirect($backUrl);