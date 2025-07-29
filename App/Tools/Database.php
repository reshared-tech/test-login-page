<?php

namespace App\Tools;

class Database
{
    static $pdo;
    protected $stmt;

    public function __construct($refresh = false)
    {
        if (!self::$pdo || $refresh) {
            $config = http_build_query(\App\Tools\Config::database, '', ';');
            self::$pdo = new \PDO(
                $config,
                null,
                null,
                [
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]
            );
        }
    }

    public function prepare($sql, $attributes = [])
    {
        $this->stmt = self::$pdo->prepare($sql)->execute($attributes);

        return $this;
    }

    public function fetch()
    {
        return $this->stmt->fetch();
    }

    public function fetchAll()
    {
        return $this->stmt->fetchAll();
    }

    public function lastId()
    {
        return self::$pdo->lastInsertId();
    }
}