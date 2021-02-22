<?php

class Default_Model_Action  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'action';

    protected $_primary = 'action_id'; 

    protected $_sequence = true;

    public function loadAction()
    {
        $select = $this->select()
                       ->from($this,array('action_id','action_name','action_actname',
                       'action_createdate','action_status','action_updatedate'))
                       ->where('action_status = ?', '1');       
                        
        $row = $this->fetchAll($select);
        return $row;
    }

}