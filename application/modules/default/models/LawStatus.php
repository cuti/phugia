<?php

class Default_Model_LawStatus  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'law_status';

    protected $_primary = 'lawstatus_id'; 

    protected $_sequence = true;

    public function loadLawStatus()
    {
        $select = $this->select()
                       ->from($this,array('lawstatus_id','lawstatus_name'))
                       ->where('lawstatus = ?', '1');       
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    public function loadLawStatusById($id)
    {
        $select = $this->select()
                       ->from($this,array('lawstatus_id','lawstatus_name'))
                       ->where('lawstatus_id = ?', $id)
                       ->where('lawstatus = ?', '1');       
                        
        $row = $this->fetchAll($select);
        return $row;
    }
}