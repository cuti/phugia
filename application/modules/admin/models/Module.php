<?php

class Admin_Model_Module  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'module';

    protected $_primary = 'module_id'; 

    public function loadlistmoduleassignrlid($id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()->from('module', array('module_type','module_name','module_id','module_controller','module_parent_id')) //the array specifies which columns I want returned in my result set
       ->joinInner(
           'role',
           'role.module_id = module.module_id',
           array()) 
       ->where('role.rl_id = ?', $id)
       //->where('module.module_parent_id is null')
       ->where('module.module_active = ?', '1')
       ->group('module.module_name')
       ->group('module.module_id')
       ->group('module.module_parent_id')
       ->group('module.module_order')
       ->group('module.module_controller')
       ->group('module.module_type')
       ->order('module.module_id');
        return $resultSet = $db->fetchAll($select);
    }

    public function loadActiveModule()
    {
        $select = $this->select()
                       ->from($this,array('module_id','module_name','module_active','module_type'))
                       ->where('module_active = ?', '1')
                       ->order('module_id');                          
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    public function loadModuleByModuleId($id){
        $id = (int)$id;
        $row = $this->fetchRow('module_id = ' . $id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }


    public function getModulesByUsername($username)
    {
         $db = Zend_Db_Table::getDefaultAdapter();
         $select = new Zend_Db_Select($db);
         $select->distinct()->from('module', array('module_type',
         'module_name','module_id','module_controller','module_parent_id')) //the array specifies which columns I want returned in my result set
        ->joinInner(
            'role',
            'role.module_id = module.module_id',
            array()) 
       ->joinInner(
           'acl',
           'acl.rl_id = role.rl_id',
           array()) 
        ->joinInner(
        'user_admin',
        'user_admin.user_id = acl.user_id',
        array())
        ->where('user_admin.user_username = ?', $username)
        ->where('module.module_parent_id is null')
        ->where('module.module_active = ?', '1')
        ->group('module.module_name')
        ->group('module.module_id')
        ->group('module.module_parent_id')
        ->group('module.module_order')
        ->group('module.module_controller')
        ->group('module.module_type')
        ->order('module.module_id');
        return $resultSet = $db->fetchAll($select);
    }

    function getSubcategoryByUsernameAndParentId($username,$parent_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from('module', array('module_name','module_id','module_controller','module_order','module_parent_id')) //the array specifies which columns I want returned in my result set
        ->joinInner(
            'role',
            'role.module_id = module.module_id',
            array()) 
       ->joinInner(
           'acl',
           'acl.rl_id = role.rl_id',
           array()) 
            ->joinInner(
           'user_admin',
           'user_admin.user_id = acl.user_id',
           array())
        ->where('user_admin.user_username = ?', $username )
        ->where('module.module_parent_id = ?',$parent_id)
        ->where('module.module_active = ?', '1')
        ->group('module.module_name')
        ->group('module.module_id')
        ->group('module.module_parent_id')
        ->group('module.module_order')
        ->group('module.module_controller')
        ->order('module.module_order');
        return $resultSet = $db->fetchAll($select);
    }

}    