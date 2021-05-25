<?php

class Default_Model_CustomerCustomerType extends Zend_Db_Table_Abstract
{
    protected $_name = 'Customer__customer_type';
    protected $_primary = array('customer_id', 'customer_type_id');

    public function insertNew($cusId, $cusTypeId)
    {
        $row = array(
            'customer_id' => $cusId,
            'customer_type_id' => $cusTypeId,
        );

        return $this->insert($row);
    }

    public function deleteByCusId($cusId)
    {
        return $this->delete('customer_id = ' . $cusId);
    }
}
