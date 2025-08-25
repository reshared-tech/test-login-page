<?php

namespace App\Models;

use Exception;
use Tools\Database;

abstract class BaseModel
{
    protected $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    /**
     * @throws Exception
     */
    protected function parseUpdate($table, $data)
    {
        if (empty($data)) {
            throw new Exception('Update data is empty');
        }

        $keys = [];
        $values = [];
        foreach ($data as $k => $v) {
            $keys[] = "`$k` = :$k";
            $values[":{$k}"] = $v;
        }
        $keyStr = implode(',', $keys);
        return ["UPDATE `$table` SET $keyStr", $values];
    }

    /**
     * @throws Exception
     */
    protected function parseInsert($table, $data)
    {
        if (empty($data)) {
            throw new Exception('Inserts data is empty');
        }

        $fields = '';
        $values = [];
        $result = [];
        foreach ($data as $k => $items) {
            ksort($items);
            $keys = '`' . implode('`,`', array_keys($items)) . '`';
            if ($fields === '') {
                $fields = $keys;
            } elseif ($fields !== $keys) {
                throw new Exception('Inserts data has different key');
            }

            $value = [];
            foreach ($items as $key => $val) {
                $value[] = ":$key{$k}";
                $result[$key . $k] = $val;
            }
            $values[$k] = '(' . implode(',', $value) . ')';
        }

        $valStr = implode(',', $values);

        $sql = "INSERT IGNORE INTO `$table`($fields) VALUES $valStr";

        return [$sql, $result];
    }

    protected function getTotal($table)
    {
        $res = $this->database->prepare("SELECT count(*) as `total` FROM `$table`")->find();

        return (int)$res['total'];
    }

    protected function getList($table, $page = 1, $size = 10)
    {
        $offset = ($page - 1) * $size;

        return $this->database->prepare("SELECT * FROM `$table` ORDER BY `id` DESC LIMIT $offset,$size")->findAll();
    }
}