<?php

class Admin_Model_Role extends Zend_Db_Table_Abstract
{
    protected $_name = 'role';
    protected $_primary = 'role_id';

    public function getRoleByRoleId($roleId)
    {
        $where = $this->getAdapter()->quoteInto('role_id = ?', $roleId);
        $row = $this->fetchRow($where);
        return $row;
    }

    public function loadRole()
    {
        $sql = 'SELECT r.role_id
                     , r.role_name
                     , r.role_active
                     , r.role_created
                     , r.role_created_by_staff_id
                     , r.role_last_updated
                     , r.role_last_updated_by_staff_id
                     , sc.staff_username AS role_created_by_username
                     , sm.staff_username AS role_last_updated_by_username
                     , (SELECT COUNT(DISTINCT sr.sr_staff_id)
                          FROM staff_role sr JOIN staff s ON sr.sr_staff_id = s.staff_id
                         WHERE sr_role_id = r.role_id AND s.staff_deleted = 0) AS staff_count
                  FROM [role] r
                       LEFT JOIN staff sc ON r.role_created_by_staff_id = sc.staff_id
                       LEFT JOIN staff sm ON r.role_last_updated_by_staff_id = sm.staff_id
              ORDER BY r.role_name ASC';

        $result = $this->getAdapter()->fetchAll($sql);

        return $result;
    }

    public function loadRoleShort()
    {
        $sql = 'SELECT role_id AS id, role_name AS [text]
                  FROM [role]
                 WHERE role_active = 1
              ORDER BY role_name ASC';

        $result = $this->getAdapter()->fetchAll($sql);

        return $result;
    }

    public function loadMenuActionsByRole($roleId)
    {
        $sql = 'SELECT rm.rm_menu_id, rm.rm_action_id
                  FROM role_menu rm
                       JOIN menu m ON rm.rm_menu_id = m.menu_id
                 WHERE rm.rm_role_id = ? AND m.menu_active = 1 AND m.menu_parent_id IS NOT NULL';

        $result = $this->getAdapter()->fetchAll($sql, array($roleId));

        return $result;
    }

    /**
     * Insert role to DB.
     *
     * @param  array $data  Data row.
     * @return mixed        The primary key of the row inserted.
     */
    public function insertRole($data)
    {
        try {
            if (!$this->isRoleExists($data['role_name'])) {
                $data['role_created'] = date('Y-m-d H:i:s');
                $data['role_created_by_staff_id'] = $this->currentStaffId();
                return $this->insert($data);
            } else {
                throw new Exception('role_name');
            }
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Update role.
     *
     * @param  array $data  Key-value pairs of fields to be updated, include role_id.
     * @return int          The number of rows updated.
     */
    public function updateRole($data)
    {
        try {
            $roleId = $data['role_id'];

            if (!$this->isRoleExists($data['role_name'], $roleId)) {
                $updateData = array_filter($data, function($k) {
                    return $k !== 'role_id';
                }, ARRAY_FILTER_USE_KEY);

                $updateData['role_last_updated'] = date('Y-m-d H:i:s');
                $updateData['role_last_updated_by_staff_id'] = $this->currentStaffId();

                $where = $this->getAdapter()->quoteInto('role_id = ?', $roleId);

                $affectedCount = $this->update($updateData, $where);

                $logger = new Admin_Model_LogAdministration();
                $logger->writeLog('Cập nhật vai trò, tên vai trò: ' . $data['role_name']);

                return $affectedCount;
            } else {
                throw new Exception('role_name');
            }
        } catch (Exception $err) {
            throw $err;
        }
    }

    public function insertRoleMenuAction($roleId, $data)
    {
        try {
            $sql = 'DELETE FROM role_menu WHERE rm_role_id = ?';
            $this->getAdapter()->query($sql, array($roleId));

            $sql = 'INSERT INTO role_menu(rm_role_id, rm_menu_id, rm_action_id) VALUES (?, ?, ?)';
            $adapter = $this->getAdapter();

            foreach ($data as $menuAction) {
                $adapter->query($sql, array($roleId, $menuAction['menu'], $menuAction['action']));
            }
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Delete role.
     *
     * @param  int    $roleId   Role id.
     * @param  string $roleName Role name.
     * @return int              The number of rows deleted.
     */
    public function deleteRole($roleId, $roleName)
    {
        try {
            $adapter = $this->getAdapter();

            // Tự động gỡ nhân viên và menu ra khỏi nhóm này, theo CASCADE DELETE
            $affectedCount = $this->delete(
                array(
                    $adapter->quoteInto('role_id = ?', $roleId),
                    $adapter->quoteInto('role_name = ?', $roleName),
                )
            );

            $logger = new Admin_Model_LogAdministration();
            $logger->writeLog('Xóa vai trò, tên vai trò: ' . $roleName);

            return $affectedCount;
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Change role status.
     *
     * @param  string $roleId       Role id.
     * @param  bool   $isActive     Role is active or not.
     * @return bool                 The number of rows updated.
     */
    public function changeStatusRole($roleId, $isActive)
    {
        try {
            $sql = "UPDATE [role]
                       SET role_active = ?,
                           role_last_updated = GETDATE(),
                           role_last_updated_by_staff_id = ?
                     WHERE role_id = ?";

            $bind = array(
                $isActive ? 1 : 0,
                $this->currentStaffId(),
                $roleId
            );

            $stm = $this->getAdapter()->query($sql, $bind);
            $resultRole = $stm->execute();

            if (!$isActive) {
                (new Admin_Model_Staff())->setInactiveForStaffByRoleId($roleId);
            }

            $roleObj = $this->getRoleByRoleId($roleId);

            $logger = new Admin_Model_LogAdministration();

            if ($isActive) {
                $logger->writeLog('Kích hoạt nhóm người dùng: ' . $roleObj['role_name']);
            } else {
                $logger->writeLog('Xóa kích hoạt nhóm người dùng: ' . $roleObj['role_name']);
            }

            return $resultRole;
        } catch (Exception $err) {
            throw $err;
        }
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function isRoleExists($roleName, $roleId = null)
    {
        if ($roleId === null) {
            $select = $this->select()
                ->from('role', array('role_id'))
                ->where('UPPER(role_name) = UPPER(?)', $roleName);
        } else {
            $select = $this->select()
                ->from('role', array('role_id'))
                ->where('UPPER(role_name) = UPPER(?)', $roleName)
                ->where('role_id <> ?', $roleId);
        }

        $result = $this->fetchAll($select);

        return count($result) > 0;
    }

    /**
     * Get current logged in staff_id
     */
    private function currentStaffId()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $identity['staff_id'];
    }
}
