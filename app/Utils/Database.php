<?php

namespace App\Utils;

use PDO;
use PDOException;

class Database
{
    /**
     * @var PDO|null
     */
    private static $connection = null;

    /**
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection()
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../config.php';
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8', $config['db_host'], $config['db_name']);
            self::$connection = new PDO($dsn, $config['db_user'], $config['db_pass']);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$connection;
    }
}
