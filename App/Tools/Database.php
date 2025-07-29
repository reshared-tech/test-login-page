<?php

namespace App\Tools;

class Database
{
    static $pdo;
    protected $stmt;

    public function __construct($refresh = false)
    {
        if (!self::$pdo || $refresh) {
            $config = Config::database;
            self::$pdo = new \PDO(
                'mysql:' . http_build_query($config, '', ';'),
                $config['username'],
                $config['password'],
                [
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]
            );
        }
    }

    public function prepare($sql, $attributes = [])
    {
        $this->stmt = self::$pdo->prepare($sql);

        $this->stmt->execute($attributes);

        return $this;
    }

    public function find()
    {
        return $this->stmt->fetch();
    }

    public function findALl()
    {
        return $this->stmt->fetchAll();
    }

    public function lastId()
    {
        return self::$pdo->lastInsertId();
    }
}