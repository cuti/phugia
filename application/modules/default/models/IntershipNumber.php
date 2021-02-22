<?php

class Default_Model_IntershipNumber  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'intership_number';

    protected $_primary = 'intership_number_id'; 

    protected $_sequence = true;    

    public function loadIntershipNumberById($intership_number_id){
        $row = $this->fetchRow('intership_number_id = ' .(int) $intership_number_id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function loadInterhipNumber()
    {
        $select = $this->select()
                       ->from($this,array('intership_number_id','intership_number_name','intership_number_status',
                       'intership_number_startdate','intership_number_enddate'))
                       ->order('intership_number_startdate DESC');                   
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    public function loadIntershipNumberActive(){
        $select = $this->select()
                       ->from($this,array('intership_number_id','intership_number_name','intership_number_status',
                       'intership_number_startdate','intership_number_enddate'))
                       //->where('intership_number_status = 1')
                       ->order('intership_number_createddate');                     
                        
        $row = $this->fetchAll($select);
        return $row;

    }

    // public function loadJoiningActive()
    // {
    //     $select = $this->select()
    //                    ->from($this,array('joining_id','joining_name','joining_status',
    //                    'joining_startdate','joining_enddate'))
    //                    ->where('joining_status = 1');              
                        
    //     $row = $this->fetchAll($select);
    //     return $row;
    // }

}    