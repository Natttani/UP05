<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';
Auth::requireAdmin();

$statusFilter  = isset($_GET['status'])  && $_GET['status']  !== '' ? (int) $_GET['status']  : null;
$serviceFilter = isset($_GET['service']) && $_GET['service'] !== '' ? (int) $_GET['service'] : null;

$perPage = 10;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$total   = Application::countAll($statusFilter, $serviceFilter);
$pages   = max(1, (int) ceil($total / $perPage));
if ($page > $pages) { $page = $pages; }
$offset  = ($page - 1) * $perPage;

$apps     = Application::all($statusFilter, $serviceFilter, $perPage, $offset);
$statuses = Database::pdo()->query('SELECT id, name FROM statuses ORDER BY id')->fetchAll();
$services = Database::pdo()->query('SELECT id, name FROM services WHERE id <> ' . SERVICE_OTHER_ID . ' ORDER BY id')->fetchAll();

$pageTitle = 'Админ-панель';
require __DIR__ . '/partials/header.php';
?>
<h1>Админ-панель</h1>
<p class="muted">Всего заявок: <?= (int) $total ?></p>

<form method="get" action="<?= e(BASE_URL) ?>frontend/admin.php" class="filters">
    <label>Статус:
        <select name="status">
            <option value="">Все</option>
            <?php foreach ($statuses as $s): ?>
                <option value="<?= (int) $s['id'] ?>" <?= $statusFilter === (int) $s['id'] ? 'selected' : '' ?>>
                    <?= e($s['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Услуга:
        <select name="service">
            <option value="">Все</option>
            <?php foreach ($services as $s): ?>
                <option value="<?= (int) $s['id'] ?>" <?= $serviceFilter === (int) $s['id'] ? 'selected' : '' ?>>
                    <?= e($s['name']) ?>
                </option>
            <?php endforeach; ?>
            <option value="<?= SERVICE_OTHER_ID ?>" <?= $serviceFilter === SERVICE_OTHER_ID ? 'selected' : '' ?>>Иная услуга</option>
        </select>
    </label>
    <button class="btn" type="submit">Фильтр</button>
    <a href="<?= e(BASE_URL) ?>frontend/admin.php" class="btn btn-ghost">Сбросить</a>
</form>

<?php if (!$apps): ?>
    <p>Заявок нет.</p>
<?php else: ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th><th>Клиент</th><th>Адрес</th><th>Дата/время</th>
                    <th>Услуга</th><th>Оплата</th><th>Статус</th><th>Действие</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($apps as $a): ?>
                <tr>
                    <td><?= (int) $a['id'] ?></td>
                    <td><?= e($a['user_name']) ?><br><small><?= e($a['user_login']) ?></small></td>
                    <td><?= e($a['address']) ?></td>
                    <td><?= e(date('d.m.Y', strtotime($a['date']))) ?><br><?= e($a['time']) ?></td>
                    <td>
                        <?= e($a['service_name']) ?>
                        <?php if (!empty($a['custom_service_desc'])): ?>
                            <br><small><?= e($a['custom_service_desc']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= e($a['payment_name']) ?></td>
                    <td>
                        <span class="badge badge-status-<?= (int) $a['status_id'] ?>">
                            <?= e($a['status_name']) ?>
                        </span>
                        <?php if ((int) $a['status_id'] === Status::CANCELLED && !empty($a['cancel_reason'])): ?>
                            <br><small class="cancel-reason"><?= e($a['cancel_reason']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php $allowed = Status::TRANSITIONS[(int) $a['status_id']] ?? []; ?>
                        <?php if (!$allowed): ?>
                            <small class="muted">—</small>
                        <?php else: ?>
                            <form method="post" action="<?= e(BASE_URL) ?>backend/actions/admin_status.php" class="status-form">
                                <input type="hidden" name="application_id" value="<?= (int) $a['id'] ?>">
                                <input type="hidden" name="back_status"  value="<?= $statusFilter ?? '' ?>">
                                <input type="hidden" name="back_service" value="<?= $serviceFilter ?? '' ?>">
                                <input type="hidden" name="back_page"    value="<?= $page ?>">

                                <select name="status_id" required>
                                    <?php foreach ($allowed as $sid): ?>
                                        <option value="<?= $sid ?>">
                                            <?= e($statuses[array_search($sid, array_column($statuses, 'id'))]['name'] ?? $sid) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <input type="text" name="cancel_reason" placeholder="Причина отмены (если отмена)">
                                <button class="btn btn-sm" type="submit">OK</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
        <nav class="pagination">
            <?php for ($p = 1; $p <= $pages; $p++): ?>
                <?php
                $q = array_filter(['status' => $statusFilter, 'service' => $serviceFilter, 'page' => $p > 1 ? $p : null]);
                $url = BASE_URL . 'frontend/admin.php' . ($q ? '?' . http_build_query($q) : '');
                ?>
                <a href="<?= e($url) ?>" class="page-link <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>