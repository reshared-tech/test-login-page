<?php

namespace Tools;

class Database
{
    static $pdo;
    protected $stmt;

    public function __construct($refresh = false, $useDb = true)
    {
        if (!self::$pdo || $refresh) {
            $config = Config::database;
            if (!$useDb) {
                unset($config['dbname']);
            }
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

    public function execute($sql, $attributes = [])
    {
        $this->stmt = self::$pdo->prepare($sql);

        return $this->stmt->execute($attributes);
    }

    public function exec($sql)
    {
        return self::$pdo->exec($sql);
    }

    public function find()
    {
        return $this->stmt->fetch();
    }

    public function findAll()
    {
        return $this->stmt->fetchAll();
    }

    public function lastId()
    {
        return self::$pdo->lastInsertId();
    }
}