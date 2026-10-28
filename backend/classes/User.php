<?php
declare(strict_types=1);

final class User
{
    public static function create(array $data): int
    {
        $pdo = Database::pdo();

        $stmt = $pdo->prepare(
            'INSERT INTO users (login, password, full_name, phone, email, role)
             VALUES (:login, :password, :full_name, :phone, :email, :role)'
        );

        $stmt->execute([
            ':login'     => $data['login'],
            ':password'  => password_hash($data['password'], PASSWORD_DEFAULT),
            ':full_name' => $data['full_name'],
            ':phone'     => $data['phone'],
            ':email'     => $data['email'],
            ':role'      => $data['role'] ?? 'user',
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function findByLogin(string $login): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE login = :login LIMIT 1');
        $stmt->execute([':login' => $login]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function loginExists(string $login): bool
    {
        $stmt = Database::pdo()->prepare('SELECT 1 FROM users WHERE login = :login LIMIT 1');
        $stmt->execute([':login' => $login]);
        return (bool) $stmt->fetchColumn();
    }
}