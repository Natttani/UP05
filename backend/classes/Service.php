<?php
declare(strict_types=1);

final class Service
{
    public static function regular(): array
    {
        $stmt = Database::pdo()->prepare('SELECT id, name FROM services WHERE id <> :other ORDER BY id');
        $stmt->execute([':other' => SERVICE_OTHER_ID]);
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        return Database::pdo()->query('SELECT id, name FROM services ORDER BY id')->fetchAll();
    }
}