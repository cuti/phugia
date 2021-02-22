<?php

class Default_Model_BillFeeOffline  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'bill_fee_offline';

    protected $_primary = 'bill_feeoffline_id'; 

    protected $_sequence = true;

    // public function loadCategoryFeeLawyer()
    // {
    //     $select = $this->select()
    //                    ->from($this,array('category_fee_lawyer_id','name','mooney'));
    //                    //->where('act_status = ?', '1');       
                        
    //     $row = $this->fetchAll($select);
    //     return $row;
    // }

}