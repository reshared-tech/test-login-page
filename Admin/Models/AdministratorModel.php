<?php

namespace Admin\Models;

use App\Models\BaseModel;
use Exception;

class AdministratorModel extends BaseModel
{
    public function getByName($name)
    {
        return $this->database->prepare("SELECT * FROM `administrators` WHERE `name` = :name", [
            'name' => $name
        ])->find();
    }

    public function getById($id)
    {
        if ($id == 1) {
            return ['name' => 'admin', 'id' => 1];
        }

        return $this->database->prepare("SELECT * FROM `administrators` WHERE `id` = :id", [
            'id' => $id
        ])->find();
    }

    /**
     * @throws Exception
     */
    public function saveLog($id, $action, $detail = [])
    {
        $data = [
            'admin_id' => $id,
            'action' => $action,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        if (!empty($detail)) {
            $data['detail'] = json_encode($detail, JSON_UNESCAPED_UNICODE);
        }
        [$sql, $result] = $this->parseInsert('admin_logs', $data);
        return $this->database->execute($sql, $result);
    }
}