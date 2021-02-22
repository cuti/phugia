<?php

class Default_Model_ActivityLaw  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'activity_law';

    protected $_primary = 'act_id'; 

    protected $_sequence = true;

    public function loadActivityLaw()
    {
        $select = $this->select()
                       ->from($this,array('act_id','act_name'))
                       ->where('act_status = ?', '1');       
                        
        $row = $this->fetchAll($select);
        return $row;
    }

}