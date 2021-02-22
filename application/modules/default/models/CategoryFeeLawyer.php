<?php

class Default_Model_CategoryFeeLawyer  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'category_fee_lawyer';

    protected $_primary = 'category_fee_lawyer_id'; 

    protected $_sequence = true;

    public function loadCategoryFeeLawyer($type)
    {
        $select = $this->select()
                       ->from($this,array('category_fee_lawyer_id','name','mooney','type'))
                       ->where('type = ?', $type);       
                        
        $row = $this->fetchAll($select);
        return $row;
    }

}