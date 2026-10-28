<?php
declare(strict_types=1);
require_once __DIR__ . '/../backend/bootstrap.php';
Auth::requireAdmin();
$pageTitle = 'Админ-панель';
require __DIR__ . '/partials/header.php';
?>
<h1>Админ-панель</h1>
<p>Раздел в разработке (следующий шаг).</p>
<?php require __DIR__ . '/partials/footer.php'; ?>