<?php

class Default_Model_Menu extends Zend_Db_Table_Abstract
{
    protected $_name = 'menu';
    protected $_primary = 'menu_id';

    public function getMenuByUsername($username)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        $select->distinct()
            ->from('menu', array('menu_id', 'menu_name', 'menu_url', 'menu_order'))
            ->joinInner('role_menu', 'role_menu.rm_menu_id = menu.menu_id', array())
            ->joinInner('user_role', 'user_role.ur_role_id = role_menu.rm_role_id', array())
            ->joinInner('user', '[user].user_id = user_role.ur_user_id', array())
            ->where('[user].user_username = ?', $username)
            ->where('menu.menu_parent_id IS NULL')
            ->where('menu.menu_active = 1')
            ->where("menu.menu_group = 'DIEUHANH'")
            ->order('menu.menu_order')
            ->order('menu.menu_name');

        return $resultSet = $db->fetchAll($select);
    }

    public function getSubMenuByUsernameAndParentId($username, $parent_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        $select->distinct()
            ->from('menu', array('menu_id', 'menu_name', 'menu_url', 'menu_order'))
            ->joinInner('role_menu', 'role_menu.rm_menu_id = menu.menu_id', array('rm_action_id'))
            ->joinInner('user_role', 'user_role.ur_role_id = role_menu.rm_role_id', array())
            ->joinInner('user', '[user].user_id = user_role.ur_user_id', array())
            ->where('[user].user_username = ?', $username)
            ->where('menu.menu_parent_id = ?', $parent_id)
            ->where('menu.menu_active = 1')
            ->where("menu.menu_group = 'DIEUHANH'")
            ->order('menu.menu_order')
            ->order('menu.menu_name');

        return $resultSet = $db->fetchAll($select);
    }

}
