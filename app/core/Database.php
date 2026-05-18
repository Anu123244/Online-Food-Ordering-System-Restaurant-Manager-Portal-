<?php
require_once __DIR__ . '/../config/config.php';

class Database {
    private static ?mysqli $connection = null;

    public static function connect(): mysqli {
        if (self::$connection === null) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            self::$connection->set_charset('utf8mb4');
        }
        return self::$connection;
    }
}
