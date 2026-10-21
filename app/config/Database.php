<?php
namespace MyBox\Config;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = getenv('DB_HOST') ?: 'localhost';
        $database = getenv('DB_NAME') ?: 'mybox';
        $username = getenv('DB_USER');
        $password = getenv('DB_PASS');

        try {
            self::$connection = new PDO(
                "mysql:host={$host};dbname={$database};charset=utf8mb4",
                $username === false ? '' : $username,
                $password === false ? '' : $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $exception) {
            error_log('MyBox database connection failed: ' . $exception->getMessage());
            throw new PDOException('No se pudo conectar con la base de datos.', 0, $exception);
        }

        return self::$connection;
    }
}
