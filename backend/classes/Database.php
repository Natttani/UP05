<?php
declare(strict_types=1);

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        self::ensureDatabaseExists();

        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $pdo->exec('PRAGMA foreign_keys = ON;');

        return self::$pdo = $pdo;
    }

    private static function ensureDatabaseExists(): void
    {
        if (is_file(DB_PATH)) {
            return;
        }

        if (!is_file(DB_DUMP_PATH)) {
            throw new RuntimeException('Не найден дамп БД: ' . DB_DUMP_PATH);
        }

        $dir = dirname(DB_PATH);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Не удалось создать папку для БД: ' . $dir);
        }

        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = file_get_contents(DB_DUMP_PATH);
        if ($sql === false) {
            throw new RuntimeException('Не удалось прочитать дамп БД');
        }

        $pdo->exec($sql);
        $pdo = null;
    }
}