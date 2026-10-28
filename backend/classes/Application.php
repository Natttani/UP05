<?php
declare(strict_types=1);

final class Application
{
    public static function create(int $userId, array $data): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO applications
                (user_id, address, phone, date, time, service_id, payment_id, status_id, custom_service_desc)
             VALUES
                (:user_id, :address, :phone, :date, :time, :service_id, :payment_id, :status_id, :custom_desc)'
        );
        $stmt->execute([
            ':user_id'      => $userId,
            ':address'      => $data['address'],
            ':phone'        => $data['phone'],
            ':date'         => $data['date'],   
            ':time'         => $data['time'],   
            ':service_id'   => (int) $data['service_id'],
            ':payment_id'   => (int) $data['payment_id'],
            ':status_id'    => Status::NEW,
            ':custom_desc'  => $data['custom_service_desc'] ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function forUser(int $userId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT a.*,
                    s.name  AS service_name,
                    st.name AS status_name,
                    pm.name AS payment_name,
                    r.id    AS review_id,
                    r.text  AS review_text,
                    r.rating AS review_rating
             FROM applications a
             JOIN services s        ON s.id  = a.service_id
             JOIN statuses st       ON st.id = a.status_id
             JOIN payment_methods pm ON pm.id = a.payment_id
             LEFT JOIN reviews r    ON r.application_id = a.id
             WHERE a.user_id = :uid
             ORDER BY a.created_at DESC, a.id DESC'
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM applications WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function all(?int $statusId = null, ?int $serviceId = null, int $limit = 50, int $offset = 0): array
    {
        $sql = 'SELECT a.*, u.login AS user_login, u.full_name AS user_name,
                       s.name AS service_name, st.name AS status_name, pm.name AS payment_name
                FROM applications a
                JOIN users u            ON u.id  = a.user_id
                JOIN services s         ON s.id  = a.service_id
                JOIN statuses st        ON st.id = a.status_id
                JOIN payment_methods pm ON pm.id = a.payment_id
                WHERE 1=1';
        $params = [];
        if ($statusId !== null)  { $sql .= ' AND a.status_id  = :sid'; $params[':sid'] = $statusId; }
        if ($serviceId !== null) { $sql .= ' AND a.service_id = :svid'; $params[':svid'] = $serviceId; }
        $sql .= ' ORDER BY a.created_at DESC, a.id DESC LIMIT :lim OFFSET :off';

        $stmt = Database::pdo()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_INT);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countAll(?int $statusId = null, ?int $serviceId = null): int
    {
        $sql = 'SELECT COUNT(*) FROM applications a WHERE 1=1';
        $params = [];
        if ($statusId !== null)  { $sql .= ' AND a.status_id  = :sid'; $params[':sid'] = $statusId; }
        if ($serviceId !== null) { $sql .= ' AND a.service_id = :svid'; $params[':svid'] = $serviceId; }
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function setStatus(int $id, int $statusId, ?string $cancelReason = null): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE applications SET status_id = :sid, cancel_reason = :reason WHERE id = :id'
        );
        $stmt->execute([
            ':sid'    => $statusId,
            ':reason' => $cancelReason,
            ':id'     => $id,
        ]);
    }
}