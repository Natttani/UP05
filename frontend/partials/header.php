<?php
/** @var string $pageTitle */
$pageTitle = $pageTitle ?? 'Блеск';
$flash = flash_get();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> — Блеск</title>
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>frontend/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <a class="logo" href="<?= e(BASE_URL) ?>index.php">Блеск</a>
    <nav class="site-nav">
        <?php if (Auth::check()): ?>
            <?php if (Auth::isAdmin()): ?>
                <a href="<?= e(BASE_URL) ?>frontend/admin.php">Админ-панель</a>
            <?php else: ?>
                <a href="<?= e(BASE_URL) ?>frontend/applications.php">Мои заявки</a>
                <a href="<?= e(BASE_URL) ?>frontend/application_create.php">Новая заявка</a>
            <?php endif; ?>
            <form method="post" action="<?= e(BASE_URL) ?>backend/actions/logout.php" class="inline">
                <button type="submit" class="link-btn">Выйти</button>
            </form>
        <?php else: ?>
            <a href="<?= e(BASE_URL) ?>frontend/login.php">Вход</a>
            <a href="<?= e(BASE_URL) ?>frontend/register.php">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>

<?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>" role="status">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<main class="site-main">