<?php
namespace MyBox\Config;
use PDO;
use PDOException;

final class Database
{
    private static ?PDO $db = null;

    public static function connection(): PDO
    {
        if (self::$db !== null) {
            return self::$db;
        }

        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $name = getenv('DB_NAME') ?: 'mybox';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS');

        if ($pass === false) {
            $pass = '';
        }

        try {
            self::$db = new PDO(
                "mysql:host=$host;dbname=$name;charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            return self::$db;

        } catch (PDOException $e) {
            error_log('Error de conexión a MySQL: ' . $e->getMessage());

            throw new \RuntimeException(
                'No se pudo conectar a la base de datos.'
            );
        }
    }
}
