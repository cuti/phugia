<?php

class Admin_Model_Action  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'action';

    protected $_primary = 'action_id'; 

    protected $_sequence = true;

    public function loadlistactionassignrlid($id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()->from('action', array('action_id','action_name','action_actname','action_status')) //the array specifies which columns I want returned in my result set
       ->joinInner(
           'role',
           'role.action_id = action.action_id',
           array()) 
       ->where('role.rl_id = ?', $id)
       //->where('module.module_parent_id is null')
       ->where('action.action_status = ?', '1')
       ->group('action.action_status')
       ->group('action.action_actname')
       ->group('action.action_name')
       ->group('action.action_id');
       //->order('module.module_id');
        return $resultSet = $db->fetchAll($select);
    }

    public function loadAction()
    {
        $select = $this->select()
                       ->from($this,array('action_id','action_name','action_actname','action_status'));                       
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    public function loadActiveAction()
    {
        $select = $this->select()
                       ->from($this,array('action_id','action_name','action_actname'))
                       ->where('action_status = ?', '1');                            
                        
        $row = $this->fetchAll($select);
        return $row;
    }
}