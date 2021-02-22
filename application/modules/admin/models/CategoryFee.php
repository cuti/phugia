<?php

class Admin_Model_CategoryFee  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'category_fee';

    protected $_primary = 'category_fee_id'; 

    protected $_sequence = true;

    /** dánh sách các loại phí trong hệ thống */
    public function loadCategoryFees()
    {
        $select = $this->select()
            ->from($this,array(
            'category_fee_id',
            'name',
            'type',
            'year',
            'baseondocument',
            'mooney',
            // 'createddate',
            // 'modifieddate',
            'status'
        ));                       
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    // public function loadActiveAction()
    // {
    //     $select = $this->select()
    //                    ->from($this,array('action_id','action_name','action_actname'))
    //                    ->where('action_status = ?', '1');                            
                        
    //     $row = $this->fetchAll($select);
    //     return $row;
    // }
}