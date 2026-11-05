<?php
declare(strict_types=1);

require_once __DIR__ . '/backend/bootstrap.php';

if (Auth::check() && Auth::isAdmin()) {
    redirect(BASE_URL . 'frontend/admin.php');
}

$pageTitle = 'Блеск — клининговая компания';
require __DIR__ . '/frontend/partials/header.php';
?>

<section class="hero">
    <h1>Чистота, которой можно доверять</h1>
    <p>Уборка квартир и офисов. Оставьте заявку — мы приедем.</p>
</section>

<section class="slider" id="slider" aria-label="Галерея работ">
    <div class="slider-track">
        <img class="slider-slide is-active" src="<?= e(BASE_URL) ?>frontend/assets/img/slider/slide1.jpeg" alt="Чистый интерьер">
        <img class="slider-slide" src="<?= e(BASE_URL) ?>frontend/assets/img/slider/slide2.jpeg" alt="Сотрудники за работой">
        <img class="slider-slide" src="<?= e(BASE_URL) ?>frontend/assets/img/slider/slide3.jpeg" alt="Инвентарь для уборки">
        <img class="slider-slide" src="<?= e(BASE_URL) ?>frontend/assets/img/slider/slide4.jpeg" alt="До и после уборки">
    </div>
    <button class="slider-btn slider-prev" type="button" aria-label="Назад">‹</button>
    <button class="slider-btn slider-next" type="button" aria-label="Вперёд">›</button>
    <div class="slider-dots" id="slider-dots"></div>
</section>

<section class="cta">
    <?php if (Auth::check()): ?>
        <a class="btn" href="<?= e(BASE_URL) ?>frontend/application_create.php">Оставить заявку</a>
        <a class="btn btn-ghost" href="<?= e(BASE_URL) ?>frontend/applications.php">Мои заявки</a>
    <?php else: ?>
        <a class="btn" href="<?= e(BASE_URL) ?>frontend/register.php">Зарегистрироваться</a>
        <a class="btn btn-ghost" href="<?= e(BASE_URL) ?>frontend/login.php">Войти</a>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/frontend/partials/footer.php'; ?>