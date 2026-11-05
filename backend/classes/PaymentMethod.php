<?php
declare(strict_types=1);

final class PaymentMethod
{
    public static function all(): array
    {
        return Database::pdo()->query('SELECT id, name FROM payment_methods ORDER BY id')->fetchAll();
    }
}