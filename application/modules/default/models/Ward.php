<?php

class Default_Model_Ward extends Zend_Db_Table_Abstract
{
    protected $_name = 'ward';
    protected $_primary = 'ward_id';

    public function loadWard()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from('ward', array(
                'id' => 'ward_id',
                'text' => 'ward_name',
                'district_id' => 'ward_district_id'))
            ->where('ward_active = 1')
            ->order('ward_order ASC')
            ->order('ward_name ASC');

        return $db->fetchAll($select);
    }

    public function getWardIdByName($name)
    {
        $select = $this->select()
            ->from($this, array('ward_id'))
            ->where('ward_name = ?', trim($name));

        $row = $this->fetchRow($select);

        if ($row) {
            return $row['ward_id'];
        }

        return null;
    }
}
