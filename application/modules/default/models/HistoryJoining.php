<?php

class Default_Model_HistoryJoining  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'history_joining';

    protected $_primary = 'history_joining_id'; 

    protected $_sequence = true;

    public function loadHistoryJoiningByCusId($cus_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
                       ->from('history_joining',array(
                           'history_joining_id'
                       ,'cus_id'
                       ,'law_id'
                       ,'law_joining_number'
                       ,'createddate'
                       ,'created_username'
                       ,'law_joining_note'
                       ,'payment_joining_status'))    
                       ->joinLeft(
                        'customers',
                        'customers.cus_id = history_joining.cus_id',
                        array('cus_firstname'=>'cus_firstname', 'cus_lastname'=>'cus_lastname'
                        ,'cus_cellphone'=>'cus_cellphone',
                        'cus_sex'=>'cus_sex' ,'cus_identity_card'=>'cus_identity_card'))            
                        ->joinLeft(
                            'lawyer',
                            'lawyer.cus_id = history_joining.cus_id',
                        array('law_certfication_no' => 'law_certfication_no',
                        'law_certification_createdate' => 'law_certification_createdate'))     
                       ->where('history_joining.cus_id = ?', $cus_id)
                       ->order('history_joining.history_joining_id desc');       
                        
        $row = $db->fetchAll($select);
        return $row;
    }    

    public function loadByHistoryId($history_id){
        $row = $this->fetchRow('history_joining_id = ' .(int) $history_id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

}