<?php

class Admin_Model_Staff extends Zend_Db_Table_Abstract
{
    protected $_name = 'staff';
    protected $_primary = 'staff_id';

    public function getStaffByStaffId($staffId)
    {
        $where = $this->getAdapter()->quoteInto('staff_id = ?', $staffId);
        $row = $this->fetchRow($where);
        return $row;
    }

    public function getStaffByUsername($username)
    {
        $where = $this->getAdapter()->quoteInto('staff_username = ?', $username);
        $row = $this->fetchRow($where);
        return $row;
    }

    public function loadStaff()
    {
        $sql = 'SELECT s.staff_id
                     , s.staff_code
                     , s.staff_fullname
                     , s.staff_display_name
                     , s.staff_gender
                     , s.staff_birth_year
                     , s.staff_username
                     , s.staff_email
                     , s.staff_department_id
                     , s.staff_position
                     , s.staff_active
                     , s.staff_created
                     , s.staff_created_by_staff_id
                     , s.staff_last_updated
                     , s.staff_last_updated_by_staff_id
                     , d.dep_name           AS staff_department
                     , sc.staff_username    AS staff_created_by_username
                     , sm.staff_username    AS staff_last_updated_by_username
                     , r.role_id
                     , r.role_name
                  FROM staff s
                       LEFT JOIN department d ON s.staff_department_id = d.dep_id
                       LEFT JOIN staff sc ON s.staff_created_by_staff_id = sc.staff_id
                       LEFT JOIN staff sm ON s.staff_last_updated_by_staff_id = sm.staff_id
                       LEFT JOIN staff_role sr ON s.staff_id = sr.staff_role_staff_id
                       LEFT JOIN [role] r ON sr.staff_role_role_id = r.role_id
                 WHERE s.staff_deleted = 0
              ORDER BY s.staff_username ASC';

        $result = $this->getAdapter()->fetchAll($sql);

        return $result;
    }

    /**
     * Insert staff to DB.
     *
     * @param  array $data  Data row.
     * @return bool         Insert result.
     */
    public function insertStaff($data, $roleId)
    {
        try {
            if (!$this->isStaffExists($data['staff_username'])) {
                $data['staff_password'] = MD5($data['staff_password']);
                $data['staff_created'] = date('Y-m-d H:i:s');
                $data['staff_created_by_staff_id'] = $this->currentStaffId();
                $data['staff_deleted'] = 0;

                $staffId = $this->insert($data);

                $sql = 'INSERT INTO staff_role(staff_role_staff_id, staff_role_role_id) VALUES (?, ?)';
                $this->getAdapter()->query($sql, array($staffId, $roleId));

                return $staffId;
            } else {
                return 'staff_username';
            }
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Update staff information.
     *
     * @param  string $staffId   Staff id.
     * @param  array  $data     Data row.
     * @return bool             Update result.
     */
    public function updateStaff($staffId, $data, $roleId)
    {
        try {
            if (!$this->isStaffExists($data['staff_username'], $staffId)) {
                $data['staff_last_updated'] = date('Y-m-d H:i:s');
                $data['staff_last_updated_by_staff_id'] = $this->currentStaffId();

                $where = $this->getAdapter()->quoteInto('staff_id = ?', $staffId);

                $this->update($data, $where);

                $sql = 'UPDATE staff_role
                           SET staff_role_role_id = ?
                         WHERE staff_role_staff_id = ?';
                $this->getAdapter()->query($sql, array($roleId, $staffId));

                $logger = new Admin_Model_LogAdministration();
                $logger->writeLog('Cập nhật thông tin nhân viên, username: ' . $data['staff_username']);

                return 1;
            } else {
                return 'staff_username';
            }
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Delete staff.
     *
     * @param  string $staffId      Staff id to delete.
     * @param  string $username     Username to delete.
     * @param  string $email        Staff's email to delete'.
     * @return int                  The number of rows deleted.
     */
    public function deleteStaff($staffId, $username, $email)
    {
        try {
            $adapter = $this->getAdapter();

            $affectedCount = $this->update(
                array(
                    'staff_deleted' => 1,
                ),
                array(
                    $adapter->quoteInto('staff_id = ?', $staffId),
                    $adapter->quoteInto('staff_username = ?', $username),
                    $adapter->quoteInto('staff_email = ?', $email),
                )
            );

            $logger = new Admin_Model_LogAdministration();
            $logger->writeLog('Xóa nhân viên, username: ' . $username);

            return $affectedCount;
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Change staff status.
     *
     * @param  string $staffId  Staff id.
     * @return bool             Update result.
     */
    public function changeStaffStatus($staffId)
    {
        try {
            $where = $this->getAdapter()->quoteInto('staff_id = ?', $staffId);

            $affectedCount = $this->update(
                array('staff_active' => new Zend_Db_Expr('1 - staff_active')),
                $where
            );

            $staffObj = $this->getStaffByStaffId($staffId);

            $logger = new Admin_Model_LogAdministration();
            $logger->writeLog('Đổi trạng thái nhân viên, username: ' . $staffObj['staff_username']);

            return $affectedCount;
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Change staff status for all who belong to a specific role.
     *
     * @param  string $roleId       Role id.
     * @return bool                 Update result.
     */
    public function setInactiveForStaffByRoleId($roleId)
    {
        try {
            $sql = "UPDATE staff
                       SET staff_active = 0,
                           staff_last_updated = GETDATE(),
                           staff_last_updated_by_staff_id = ?
                     WHERE staff_id IN (SELECT staff_role_staff_id
                                          FROM staff_role
                                         WHERE staff_role_role_id = ?)";

            $bind = array(
                $this->currentStaffId(),
                $roleId
            );

            $this->getAdapter()->query($sql, $bind);

            $roleObj = (new Admin_Model_Role())->getRoleByRoleId($roleId);

            $logger = new Admin_Model_LogAdministration();
            $logger->writeLog('Xóa kích hoạt tất cả nhân viên của nhóm: ' . $roleObj['role_name']);

            return 1;
        } catch (Exception $err) {
            throw $err;
        }
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function isStaffExists($username, $staffId = null)
    {
        if ($staffId === null) {
            $select = $this->select()
                ->from('staff', array('staff_id'))
                ->where('UPPER(staff_username) = UPPER(?)', $username);
        } else {
            $select = $this->select()
                ->from('staff', array('staff_id'))
                ->where('UPPER(staff_username) = UPPER(?)', $username)
                ->where('staff_id <> ?', $staffId);
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
