<?php

class Default_Model_District extends Zend_Db_Table_Abstract
{
    protected $_name = 'district';
    protected $_primary = 'district_id';

    public function loadDistrict()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from('district', array(
                'id' => 'district_id',
                'text' => 'district_name',
                'city_id' => 'district_city_id'
            ))
            ->where('district_active = 1')
            ->order('district_order ASC')
            ->order('district_name ASC');

        return $db->fetchAll($select);
    }

    public function getDistrictIdByName($name)
    {
        $select = $this->select()
            ->from($this, array('district_id'))
            ->where('district_name = ?', trim($name));

        $row = $this->fetchRow($select);

        if ($row) {
            return $row['district_id'];
        }

        return null;
    }
}
