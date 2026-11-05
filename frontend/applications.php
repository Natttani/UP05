<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';
Auth::requireUser();

$tab = $_GET['tab'] ?? 'all';
if (!in_array($tab, ['all', 'active', 'done'], true)) {
    $tab = 'all';
}

$apps = Application::forUserFiltered(Auth::id(), $tab);

$pageTitle = 'Мои заявки';
require __DIR__ . '/partials/header.php';
?>
<h1>Мои заявки</h1>

<nav class="tabs" aria-label="Фильтр заявок">
    <?php
    $tabs = ['all' => 'Все', 'active' => 'Активные', 'done' => 'Завершённые'];
    foreach ($tabs as $key => $label):
        $url = BASE_URL . 'frontend/applications.php' . ($key === 'all' ? '' : '?tab=' . $key);
    ?>
        <a href="<?= e($url) ?>" class="tab <?= $tab === $key ? 'is-active' : '' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</nav>

<?php if (!$apps): ?>
    <p>Заявок в этой категории нет. <a href="<?= e(BASE_URL) ?>frontend/application_create.php">Создать заявку</a>.</p>
<?php else: ?>
    <div class="cards">
        <?php foreach ($apps as $a): ?>
            <article class="card card-enter">
                <header class="card-head">
                    <strong><?= e($a['service_name']) ?></strong>
                    <span class="badge badge-status-<?= (int) $a['status_id'] ?>"><?= e($a['status_name']) ?></span>
                </header>
                <p><b>Адрес:</b> <?= e($a['address']) ?></p>
                <p><b>Дата:</b> <?= e(date('d.m.Y', strtotime($a['date']))) ?> <b>Время:</b> <?= e($a['time']) ?></p>
                <p><b>Оплата:</b> <?= e($a['payment_name']) ?></p>
                <?php if (!empty($a['custom_service_desc'])): ?>
                    <p><b>Описание услуги:</b> <?= e($a['custom_service_desc']) ?></p>
                <?php endif; ?>
                <?php if ((int) $a['status_id'] === Status::CANCELLED && !empty($a['cancel_reason'])): ?>
                    <p class="cancel-reason"><b>Причина отмены:</b> <?= e($a['cancel_reason']) ?></p>
                <?php endif; ?>

                <?php if ((int) $a['status_id'] === Status::DONE): ?>
                    <?php if ($a['review_id']): ?>
                        <div class="review"><b>Ваш отзыв:</b> <?= e($a['review_text']) ?> (оценка <?= (int) $a['review_rating'] ?>/5)</div>
                    <?php else: ?>
                        <form method="post" action="<?= e(BASE_URL) ?>backend/actions/review_create.php" class="form-inline">
                            <input type="hidden" name="application_id" value="<?= (int) $a['id'] ?>">
                            <label>Отзыв: <textarea name="text" required minlength="3"></textarea></label>
                            <label>Оценка:
                                <select name="rating" required>
                                    <?php for ($i = 1; $i <= 5; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
                                </select>
                            </label>
                            <button class="btn" type="submit">Оставить отзыв</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>