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

    public function getLogTotal()
    {
        return $this->getTotal('admin_logs');
    }

    public function getLogList($page = 1, $size = 10)
    {
        return $this->getList('admin_logs', $page, $size);
    }

    public function getByIds($ids)
    {
        if (empty($ids)) {
            return [];
        }
        $idStr = implode(',', $ids);
        return $this->database->prepare("SELECT * FROM `administrators` WHERE `id` in ($idStr)")->findAll();
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