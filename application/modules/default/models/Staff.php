<?php

class Default_Model_Staff extends Zend_Db_Table_Abstract
{
    protected $_name = 'staff';
    protected $_primary = 'staff_id';

    /**
     * Authenticate user.
     *
     * @param string  $username
     * @param string  $password
     * @return array  Record matched.
     */
    public function authenticate($username, $password)
    {
        $sql = "SELECT staff.*, dep.dep_name
                  FROM staff
                       LEFT JOIN department dep ON staff.staff_department_id = dep.dep_id
                 WHERE staff_username = ? AND staff_password = ? AND staff_active = 1";
        $bind = array($username, MD5($password));
        $result = $this->getAdapter()->fetchAll($sql, $bind);

        return $result;
    }

    /**
     * Validate username and password, without check if the user is active or not.
     *
     * @param string  $username
     * @param string  $password
     * @return bool   The validation is success or not.
     */
    public function validate($username, $password)
    {
        $sql = "SELECT staff_id FROM staff WHERE staff_username = ? AND staff_password = ?";
        $bind = array($username, $password);
        $result = count($this->getAdapter()->fetchAll($sql, $bind));

        return $result > 0;
    }

    public function getStaffByUsername($username)
    {
        $select = $this->select()->from($this)->where('staff_username = ?', $username);
        return $this->fetchRow($select);
    }

    /**
     * Change user password.
     *
     * @param int     $staffId
     * @param string  $password   New password.
     * @return int    The number of affected rows.
     */
    public function changeUserPassword($staffId, $password)
    {
        $adapter = $this->getAdapter();
        $data = array('staff_password' => MD5($password));
        $where = $adapter->quoteInto('staff_id = ?', $staffId);
        return $adapter->update('staff', $data, $where);
    }

    /**
     * Update staff info.
     *
     * @param int $staffId
     * @param array $data   Column-value pairs of fields to update.
     * @return int          The number of affected rows.
     */
    public function updateStaffInfo($staffId, $data)
    {
        $adapter = $this->getAdapter();
        $where = $adapter->quoteInto('staff_id = ?', $staffId);
        return $adapter->update('staff', $data, $where);
    }
}
