<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
Auth::requireUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . 'frontend/applications.php');
}

$appId  = (int) ($_POST['application_id'] ?? 0);
$text   = trim((string) ($_POST['text'] ?? ''));
$rating = (int) ($_POST['rating'] ?? 0);

$app = Application::find($appId);

if (!$app || (int) $app['user_id'] !== Auth::id()) {
    flash_set('error', 'Заявка не найдена');
    redirect(BASE_URL . 'frontend/applications.php');
}
if ((int) $app['status_id'] !== Status::DONE) {
    flash_set('error', 'Отзыв можно оставить только после выполнения услуги');
    redirect(BASE_URL . 'frontend/applications.php');
}
if (Review::existsFor($appId)) {
    flash_set('error', 'Отзыв по этой заявке уже оставлен');
    redirect(BASE_URL . 'frontend/applications.php');
}
if (mb_strlen($text) < 3) {
    flash_set('error', 'Текст отзыва слишком короткий');
    redirect(BASE_URL . 'frontend/applications.php');
}
if ($rating < 1 || $rating > 5) {
    flash_set('error', 'Оценка должна быть от 1 до 5');
    redirect(BASE_URL . 'frontend/applications.php');
}

Review::create($appId, Auth::id(), $text, $rating);
flash_set('success', 'Спасибо за отзыв!');
redirect(BASE_URL . 'frontend/applications.php');