<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';
Auth::requireUser();

$services = Database::pdo()->query('SELECT id, name FROM services WHERE id <> ' . SERVICE_OTHER_ID . ' ORDER BY id')->fetchAll();
$payments = Database::pdo()->query('SELECT id, name FROM payment_methods ORDER BY id')->fetchAll();
$user     = User::findById(Auth::id());

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

$defaultPhone = $old['phone'] ?? $user['phone'] ?? '';

$pageTitle = 'Новая заявка';
require __DIR__ . '/partials/header.php';
?>
<h1>Новая заявка</h1>

<form method="post" action="<?= e(BASE_URL) ?>backend/actions/application_create.php" class="form" novalidate>
    <div class="field">
        <label for="address">Адрес</label>
        <input type="text" id="address" name="address" value="<?= e($old['address'] ?? '') ?>" required>
        <?php if (isset($errors['address'])): ?><div class="error"><?= e($errors['address']) ?></div><?php endif; ?>
    </div>

    <div class="field">
        <label for="phone">Контактный телефон</label>
        <input type="text" id="phone" name="phone" value="<?= e($defaultPhone) ?>"
               placeholder="8(999)123-45-67"
               pattern="8\(\d{3}\)\d{3}-\d{2}-\d{2}" required>
        <?php if (isset($errors['phone'])): ?><div class="error"><?= e($errors['phone']) ?></div><?php endif; ?>
    </div>

    <div class="field">
        <label for="date">Дата (ДД.ММ.ГГГГ)</label>
        <input type="text" id="date" name="date" value="<?= e($old['date'] ?? '') ?>"
               placeholder="31.12.2026" pattern="\d{2}\.\d{2}\.\d{4}" required>
        <?php if (isset($errors['date'])): ?><div class="error"><?= e($errors['date']) ?></div><?php endif; ?>
    </div>

    <div class="field">
        <label for="time">Время (ЧЧ:ММ)</label>
        <input type="text" id="time" name="time" value="<?= e($old['time'] ?? '') ?>"
               placeholder="14:30" pattern="([01]\d|2[0-3]):[0-5]\d" required>
        <?php if (isset($errors['time'])): ?><div class="error"><?= e($errors['time']) ?></div><?php endif; ?>
    </div>

    <div class="field">
        <label for="service_id">Вид услуги</label>
        <select id="service_id" name="service_id" required>
            <option value="">— выберите —</option>
            <?php foreach ($services as $s): ?>
                <option value="<?= (int) $s['id'] ?>" <?= (string) ($old['service_id'] ?? '') === (string) $s['id'] ? 'selected' : '' ?>>
                    <?= e($s['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['service_id'])): ?><div class="error"><?= e($errors['service_id']) ?></div><?php endif; ?>
    </div>

    <div class="field">
        <label>
            <input type="checkbox" name="other_service" value="1" id="other_service"
                <?= !empty($old['other_service']) ? 'checked' : '' ?>>
            Иная услуга
        </label>
        <div id="other_service_wrap" class="field" <?= empty($old['other_service']) ? 'hidden' : '' ?>>
            <label for="custom_service_desc">Описание иной услуги</label>
            <textarea id="custom_service_desc" name="custom_service_desc"><?= e($old['custom_service_desc'] ?? '') ?></textarea>
            <?php if (isset($errors['custom_service_desc'])): ?><div class="error"><?= e($errors['custom_service_desc']) ?></div><?php endif; ?>
        </div>
    </div>

    <div class="field">
        <label for="payment_id">Способ оплаты</label>
        <select id="payment_id" name="payment_id" required>
            <option value="">— выберите —</option>
            <?php foreach ($payments as $p): ?>
                <option value="<?= (int) $p['id'] ?>" <?= (string) ($old['payment_id'] ?? '') === (string) $p['id'] ? 'selected' : '' ?>>
                    <?= e($p['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['payment_id'])): ?><div class="error"><?= e($errors['payment_id']) ?></div><?php endif; ?>
    </div>

    <button class="btn" type="submit">Отправить</button>
</form>

<?php require __DIR__ . '/partials/footer.php'; ?>