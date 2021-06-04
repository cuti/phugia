<?php

class Default_Model_Department extends Zend_Db_Table_Abstract
{
    protected $_name = 'department';
    protected $_primary = 'dep_id';

    public function loadDepartment()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from('department', array(
                'id' => 'dep_id',
                'text' => 'dep_name',
                'city_id' => 'dep_city_id'
            ))
            ->order('dep_name ASC');

        return $db->fetchAll($select);
    }
}
