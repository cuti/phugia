<?php

class Default_Model_Joining  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'joining';

    protected $_primary = 'joining_id'; 

    protected $_sequence = true;    

    public function loadJoiningById($joining_id){
        $row = $this->fetchRow('joining_id = ' .(int) $joining_id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function loadJoining()
    {
        $select = $this->select()
                       ->from($this,array('joining_id','joining_name','joining_status',
                       'joining_startdate','joining_enddate'));                     
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    public function loadJoiningActive()
    {
        $select = $this->select()
                       ->from($this,array('joining_id','joining_name','joining_status',
                       'joining_startdate','joining_enddate'))
                       ->where('joining_status = 1');              
                        
        $row = $this->fetchAll($select);
        return $row;
    }

}    