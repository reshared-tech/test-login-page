<?php

namespace Admin\Models;

use App\Models\BaseModel;
use Exception;

/**
 * Model class for handling administrator-related database operations
 */
class AdministratorModel extends BaseModel
{
    /**
     * Retrieve administrator information by username
     *
     * @param string $name Administrator username
     * @return mixed Administrator record or null if not found
     */
    public function getByName($name)
    {
        return $this->database->prepare("SELECT * FROM `administrators` WHERE `name` = :name", [
            'name' => $name
        ])->find();
    }

    /**
     * Get total number of administrator logs
     *
     * @return int Total count of log records
     */
    public function getLogTotal()
    {
        return $this->getTotal('admin_logs');
    }

    /**
     * Get paginated list of administrator logs
     *
     * @param int $page Page number (default: 1)
     * @param int $size Number of items per page (default: 10)
     * @return array List of log records
     */
    public function getLogList($page = 1, $size = 10)
    {
        return $this->getList('admin_logs', $page, $size);
    }

    /**
     * Retrieve multiple administrators by their IDs
     *
     * @param array $ids Array of administrator IDs
     * @return array List of administrator records
     */
    public function getByIds($ids)
    {
        if (empty($ids)) {
            return [];
        }
        $idStr = implode(',', $ids);
        return $this->database->prepare("SELECT * FROM `administrators` WHERE `id` in ($idStr)")->findAll();
    }

    /**
     * Retrieve administrator information by ID
     * Special case: returns default admin for ID = 1
     *
     * @param int $id Administrator ID
     * @return mixed Administrator record or null if not found
     */
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
     * Save an administrator action log
     *
     * @param int $id Administrator ID
     * @param string $action Action description
     * @param array $detail Additional details about the action
     * @return bool True on success, false on failure
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