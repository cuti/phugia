<?php

class Default_Model_City extends Zend_Db_Table_Abstract
{
    protected $_name = 'city';
    protected $_primary = 'city_id';

    public function loadCity()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from('city', array(
                'id' => 'city_id',
                'text' => 'city_name',
                'country_id' => 'city_country_id'
            ))
            ->where('city_active = 1')
            ->order('city_order ASC')
            ->order('city_name ASC');

        return $db->fetchAll($select);
    }

    public function getCityIdByName($name)
    {
        $select = $this->select()
            ->from($this, array('city_id'))
            ->where('city_name = ?', trim($name));

        $row = $this->fetchRow($select);

        if ($row) {
            return $row['city_id'];
        }

        return null;
    }
}
