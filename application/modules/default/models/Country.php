<?php

class Default_Model_Country extends Zend_Db_Table_Abstract
{
    protected $_name = 'country';
    protected $_primary = 'country_id';

    public function loadCountry()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from('country', array(
                'id' => 'country_id',
                'text' => 'country_name',
                'vn_name' => 'country_vietnamese_name',
            ))
            ->where('country_active = 1')
            ->order('country_order ASC')
            ->order('country_name ASC');

        return $db->fetchAll($select);
    }

    public function getCountryIdByName($name)
    {
        $select = $this->select()
            ->from($this, array('country_id'))
            ->where('country_name = ?', trim($name));

        $row = $this->fetchRow($select);

        if ($row) {
            return $row['country_id'];
        }

        return null;
    }
}
