<?php

class Default_Model_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'user';
    protected $_primary = 'user_id';

    public function num($username, $password)
    {
        $sql = "SELECT * FROM [user] WHERE user_username = ? AND user_password = ? AND user_status = 1";
        $bind = array($username, $password);
        $result = count($this->getAdapter()->fetchAll($sql, $bind));

        return $result;
    }

    public function getUserByUsername($username)
    {
        $select = $this->select()->from($this)->where('user_username = ?', $username);
        $row = $this->fetchRow($select);

        if (!$row) {
            return null;
        }

        return $row->toArray();
    }
}
