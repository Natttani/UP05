<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';

if (Auth::check()) {
    redirect(BASE_URL . 'index.php');
}

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

$pageTitle = 'Регистрация';
require __DIR__ . '/partials/header.php';
?>
<h1>Регистрация</h1>

<form method="post" action="<?= e(BASE_URL) ?>backend/actions/register.php" class="form" novalidate>
    <div class="field">
        <label for="login">Логин</label>
        <input type="text" id="login" name="login"
               value="<?= e($old['login'] ?? '') ?>"
               pattern="[A-Za-z0-9]{6,}" required>
        <small class="hint">Латиница и цифры, не менее 6 символов</small>
        <?php if (isset($errors['login'])): ?><div class="error"><?= e($errors['login']) ?></div><?php endif; ?>
    </div>

    <div class="field">
        <label for="password">Пароль</label>
        <input type="password" id="password" name="password" required>
        <small class="hint">Не менее 8 символов, минимум одна цифра</small>
        <?php if (isset($errors['password'])): ?><div class="error"><?= e($errors['password']) ?></div><?php endif; ?>
    </div>

    <div class="field">
        <label for="full_name">ФИО</label>
        <input type="text" id="full_name" name="full_name"
               value="<?= e($old['full_name'] ?? '') ?>"
               pattern="[А-Яа-яЁё\s]+" required>
        <?php if (isset($errors['full_name'])): ?><div class="error"><?= e($errors['full_name']) ?></div><?php endif; ?>
    </div>

    <div class="field">
        <label for="phone">Телефон</label>
        <input type="text" id="phone" name="phone"
               value="<?= e($old['phone'] ?? '') ?>"
               placeholder="8(999)123-45-67"
               pattern="8\(\d{3}\)\d{3}-\d{2}-\d{2}" required>
        <?php if (isset($errors['phone'])): ?><div class="error"><?= e($errors['phone']) ?></div><?php endif; ?>
    </div>

    <div class="field">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email"
               value="<?= e($old['email'] ?? '') ?>" required>
        <?php if (isset($errors['email'])): ?><div class="error"><?= e($errors['email']) ?></div><?php endif; ?>
    </div>

    <button type="submit" class="btn">Создать пользователя</button>
</form>

<p class="switch">Уже зарегистрированы?
    <a href="<?= e(BASE_URL) ?>frontend/login.php">Вход</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>