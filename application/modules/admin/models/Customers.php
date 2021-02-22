<?php

class Admin_Model_Customers  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'customers';

    protected $_primary = 'cus_id'; 

    /*get customer by phone*/
    public function getCustomer($phone_number)
    {
        $row = $this->fetchRow('cus_cellphone = ' . $phone_number);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

}    