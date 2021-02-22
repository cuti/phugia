<?php

class Admin_Model_Role  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'role';

    protected $_primary = 'role_id'; 

    protected $_sequence = true;

    public function loadRolesByRlId($id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select
        ->from("role",array(
        'role_id'=>'role_id',
        'role_name'=>'role_name',
        'role_action'=>'role_action',
        'role_status'=>'role_status',
        'rl_id' => 'rl_id'))    
        ->joinLeft(
        'action',
        'action.action_id = role.action_id',
        array('action_name' =>'action_name',
        'action_actname'=>'action_actname')) 
        ->joinLeft(
        'module',
        'module.module_id = role.module_id',
        array('module_name' => 'module_name',
        'module_controller_name' => 'module_controller_name'));                     
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    public function loadRoles()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select
        ->from("role",array(
        'role_id'=>'role_id',
        'role_name'=>'role_name',
        'role_action'=>'role_action',
        'role_status'=>'role_status'))    
        ->joinLeft(
        'action',
        'action.action_id = role.action_id',
        array('action_name' =>'action_name',
        'action_actname'=>'action_actname')) 
        ->joinLeft(
        'module',
        'module.module_id = role.module_id',
        array('module_name' => 'module_name',
        'module_controller_name' => 'module_controller_name'));                     
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    public function loadRoleByRoleId($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from("module", array('module_controller','module_id'))
            ->joinInner(
            'role',
            'role.module_id = module.module_id',
            array()) 
            ->joinInner(
                'action',
                'role.action_id = action.action_id',
                array('action_actname'))  
            ->where('role.role_id = ?',$id);
            
            // ->limit(1);   
                        
        return $db->fetchRow($select);
    }
}