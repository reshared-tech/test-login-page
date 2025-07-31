<?php

namespace Admin\Models;

use Tools\Database;

abstract class BaseModel
{
    protected $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    /**
     * @throws \Exception
     */
    protected function parseInsert($table, $data)
    {
        if (empty($data)) {
            throw new \Exception('Inserts data is empty');
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
                throw new \Exception('Inserts data has different key');
            }

            $value = [];
            foreach ($items as $key => $val) {
                $value[] = ":$key{$k}";
                $result[$key.$k] = $val;
            }
            $values[$k] = '(' . implode(',', $value) . ')';
        }

        $valStr = implode(',', $values);

        $sql = "INSERT INTO `$table`($fields) VALUES $valStr";

        return [$sql, $result];
    }
}