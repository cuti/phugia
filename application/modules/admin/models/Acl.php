<?php

class Admin_Model_Acl  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'acl';

    protected $_primary = 'acl_id'; 

    protected $_sequence = true;

    // public function loadAction()
    // {
    //     $select = $this->select()
    //                    ->from($this,array('action_id','action_name','action_actname','action_status'));                       
                        
    //     $row = $this->fetchAll($select);
    //     return $row;
    // }

    public function loadAclByUserId($id)
    {
        $select = $this->select()
                       ->from($this,array('role_id','acl_id'))
                       ->where('user_id = ?', $id);                            
                        
        $row = $this->fetchAll($select);
        return $row;
    }
}