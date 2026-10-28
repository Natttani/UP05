<?php
declare(strict_types=1);

final class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true); 
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['role']    = (string) ($user['role'] ?? 'user');
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return self::check() ? (int) $_SESSION['user_id'] : null;
    }

    public static function isAdmin(): bool
    {
        return ($_SESSION['role'] ?? '') === 'admin';
    }

    public static function requireUser(): void
    {
        if (!self::check()) {
            flash_set('error', 'Войдите, чтобы продолжить');
            redirect(BASE_URL . 'frontend/login.php');
        }
    }

    public static function requireAdmin(): void
    {
        if (!self::check()) {
            flash_set('error', 'Войдите как администратор');
            redirect(BASE_URL . 'frontend/login.php');
        }
        if (!self::isAdmin()) {
            flash_set('error', 'Доступ только для администратора');
            redirect(BASE_URL . 'frontend/applications.php');
        }
    }
}