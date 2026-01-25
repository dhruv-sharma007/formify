<?php

namespace Dhruv\Project\Database;

use PDO;
use PDOException;

final class Connection
{
    private static ?PDO $instance = null;

    public static function get(): PDO
    {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    'mysql:host=127.0.0.1;dbname=formapp;charset=utf8mb4',
                    'appuser',
                    'root',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                throw new \RuntimeException('DB Connection failed');
            }
        }

        return self::$instance;
    }
}
