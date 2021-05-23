<?php

class Default_Model_CustomerType extends Zend_Db_Table_Abstract
{
    protected $_name = 'customer_type';
    protected $_primary = 'customer_type_id';

    public function loadCustomerType()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from('customer_type', array(
                'id' => 'customer_type_id',
                'text' => 'customer_type_name'
            ))
            ->order('customer_type_name ASC');

        return $db->fetchAll($select);
    }

    public function getCustomerTypeIdByCode($code)
    {
        $select = $this->select()
            ->from($this, array('customer_type_id'))
            ->where('customer_type_code = ?', trim($code));

        $row = $this->fetchRow($select);

        if ($row) {
            return $row['customer_type_id'];
        }

        return null;
    }
}
