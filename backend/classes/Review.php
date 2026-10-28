<?php
declare(strict_types=1);

final class Review
{
    public static function create(int $applicationId, int $userId, string $text, int $rating): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO reviews (application_id, user_id, text, rating)
             VALUES (:app_id, :uid, :text, :rating)'
        );
        $stmt->execute([
            ':app_id' => $applicationId,
            ':uid'    => $userId,
            ':text'   => $text,
            ':rating' => $rating,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function existsFor(int $applicationId): bool
    {
        $stmt = Database::pdo()->prepare('SELECT 1 FROM reviews WHERE application_id = :id LIMIT 1');
        $stmt->execute([':id' => $applicationId]);
        return (bool) $stmt->fetchColumn();
    }
}