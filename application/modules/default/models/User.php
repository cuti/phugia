<?php

class Default_Model_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'user';
    protected $_primary = 'user_id';

    public function num($username, $password)
    {
        $sql = "SELECT user_id FROM [user] WHERE user_username = ? AND user_password = ? AND user_status = 1";
        $bind = array($username, $password);
        $result = count($this->getAdapter()->fetchAll($sql, $bind));

        return $result;
    }

    public function getUserByUsername($username)
    {
        $select = $this->select()->from($this)->where('user_username = ?', $username);
        return $this->fetchRow($select);
    }
}
