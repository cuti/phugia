<?php

class Default_Model_Menu extends Zend_Db_Table_Abstract
{
    protected $_name = 'menu';
    protected $_primary = 'menu_id';

    public function getMenuByUsername($username, $module)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        $select->distinct()
            ->from('menu', array('menu_id', 'menu_name', 'menu_icon', 'menu_url', 'menu_order', 'menu_group'))
            ->joinInner('role_menu', 'role_menu.role_menu_menu_id = menu.menu_id', array())
            ->joinInner('staff_role', 'staff_role.staff_role_role_id = role_menu.role_menu_role_id', array())
            ->joinInner('staff', 'staff.staff_id = staff_role.staff_role_staff_id', array())
            ->where('staff.staff_username = ?', $username)
            ->where('menu.menu_parent_id IS NULL')
            ->where('menu.menu_active = 1')
            ->where("menu.menu_module = ?", $module)
            ->order('menu.menu_order')
            ->order('menu.menu_name');

        return $resultSet = $db->fetchAll($select);
    }

    public function getSubMenuByUsernameAndParentId($username, $parent_id, $module)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        $select->distinct()
            ->from('menu', array('menu_id', 'menu_name', 'menu_icon', 'menu_url', 'menu_order'))
            ->joinInner('role_menu', 'role_menu.role_menu_menu_id = menu.menu_id', array())
            ->joinInner('staff_role', 'staff_role.staff_role_role_id = role_menu.role_menu_role_id', array())
            ->joinInner('staff', 'staff.staff_id = staff_role.staff_role_staff_id', array())
            ->where('staff.staff_username = ?', $username)
            ->where('menu.menu_parent_id = ?', $parent_id)
            ->where('menu.menu_active = 1')
            ->where("menu.menu_module = ?", $module)
            ->order('menu.menu_order')
            ->order('menu.menu_name');

        return $resultSet = $db->fetchAll($select);
    }

    public function getPermissionTree()
    {
        $tree = array(
            array(
                'key' => 'default',
                'title' => 'Điều hành',
                'folder' => true,
                'children' => $this->getMenuTree('default'),
            ),
            array(
                'key' => 'admin',
                'title' => 'Quản trị',
                'folder' => true,
                'children' => $this->getMenuTree('admin'),
            ),
        );

        return $tree;
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function getMenuTree($module)
    {
        $sql = 'SELECT menu_id AS [key], menu_name AS title
                  FROM menu
                 WHERE     menu_parent_id IS NULL
                       AND menu_active = 1
                       AND menu_module = ?
              ORDER BY menu_name ASC';

        $tree = $this->getAdapter()->fetchAll($sql, array($module));

        foreach ($tree as &$node) {
            $node['folder'] = true;
            $node['children'] = $this->getSubMenuTree($module, $node['key']);
        }

        return $tree;
    }

    private function getSubMenuTree($module, $parent)
    {
        $sql = 'SELECT menu_id AS [key], menu_name AS title
                  FROM menu
                 WHERE     menu_parent_id = ?
                       AND menu_active = 1
                       AND menu_module = ?
              ORDER BY menu_name ASC';

        $tree = $this->getAdapter()->fetchAll($sql, array($parent, $module));

        foreach ($tree as &$node) {
            $node['folder'] = true;
            $node['children'] = $this->getActions($node['key']);
        }

        return $tree;
    }

    private function getActions($menuId)
    {
        require_once 'Utility.php';

        $cache = Utility::getCache();
        $result = $cache->load('actions' . $menuId);

        if (!$result) {
            $result = $this->cacheActions($cache, $menuId);
        }

        return $result;
    }

    private function cacheActions(&$cache, $menuId)
    {
        $sql = "SELECT (CAST(? AS VARCHAR) + '_' + CAST(action_id AS VARCHAR)) AS [key], action_name AS title
                  FROM action
              ORDER BY action_id ASC";

        $actions = $this->getAdapter()->fetchAll($sql, array($menuId));

        $cache->save($actions, 'actions' . $menuId);

        return $actions;
    }
}
