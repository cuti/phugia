<?php

class Default_Model_Module  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'module';

    protected $_primary = 'module_id'; 

    
    public function getModulesByUsername($username)
    {
         $db = Zend_Db_Table::getDefaultAdapter();
         $select = new Zend_Db_Select($db);
         $select->distinct()->from('module', array('module_type','module_name','module_id','module_order','module_controller','module_parent_id')) //the array specifies which columns I want returned in my result set
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
        ->order('module.module_order');
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