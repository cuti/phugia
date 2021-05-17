<?php

class Default_Model_CustomerType extends Zend_Db_Table_Abstract
{
    protected $_name = 'customer_type';
    protected $_primary = 'customer_type_id';

    public function loadCustomerType()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from('customer_type')->order('customer_type_name ASC');
        return $db->fetchAll($select);
    }
}
