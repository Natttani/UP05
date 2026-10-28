<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';

if (Auth::check()) {
    redirect(Auth::isAdmin() ? BASE_URL . 'frontend/admin.php' : BASE_URL . 'frontend/applications.php');
}

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

$pageTitle = 'Вход';
require __DIR__ . '/partials/header.php';
?>
<h1>Вход</h1>

<form method="post" action="<?= e(BASE_URL) ?>backend/actions/login.php" class="form" novalidate>
    <div class="field">
        <label for="login">Логин</label>
        <input type="text" id="login" name="login"
               value="<?= e($old['login'] ?? '') ?>" required>
        <?php if (isset($errors['login'])): ?><div class="error"><?= e($errors['login']) ?></div><?php endif; ?>
    </div>

    <div class="field">
        <label for="password">Пароль</label>
        <input type="password" id="password" name="password" required>
        <?php if (isset($errors['password'])): ?><div class="error"><?= e($errors['password']) ?></div><?php endif; ?>
    </div>

    <button type="submit" class="btn">Войти</button>
</form>

<p class="switch">Еще не зарегистрированы?
    <a href="<?= e(BASE_URL) ?>frontend/register.php">Регистрация</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>