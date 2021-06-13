<?php

class Default_Model_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'user';
    protected $_primary = 'user_id';

    /**
     * Authenticate user.
     *
     * @param string  $username
     * @param string  $password
     * @return array  Record matched.
     */
    public function authenticate($username, $password)
    {
        $sql = "SELECT [user].*, dep.dep_name
                  FROM [user]
                       LEFT JOIN department dep ON [user].user_department_id = dep.dep_id
                 WHERE user_username = ? AND user_password = ? AND user_active = 1";
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
        $sql = "SELECT user_id FROM [user] WHERE user_username = ? AND user_password = ?";
        $bind = array($username, $password);
        $result = count($this->getAdapter()->fetchAll($sql, $bind));

        return $result > 0;
    }

    public function getUserByUsername($username)
    {
        $select = $this->select()->from($this)->where('user_username = ?', $username);
        return $this->fetchRow($select);
    }

    /**
     * Change user password.
     *
     * @param int     $userId
     * @param string  $password   New password.
     * @return int    The number of affected rows.
     */
    public function changeUserPassword($userId, $password)
    {
        $adapter = $this->getAdapter();
        $data = array('user_password' => MD5($password));
        $where = $adapter->quoteInto('user_id = ?', $userId);
        return $adapter->update('user', $data, $where);
    }

    /**
     * Update user info.
     *
     * @param int $userId
     * @param array $data   Column-value pairs of fields to update.
     * @return int          The number of affected rows.
     */
    public function updateUserInfo($userId, $data)
    {
        $adapter = $this->getAdapter();
        $where = $adapter->quoteInto('user_id = ?', $userId);
        return $adapter->update('user', $data, $where);
    }
}
