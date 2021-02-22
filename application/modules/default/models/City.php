<?php

class Default_Model_City  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'city';

    protected $_primary = 'city_id'; 

    protected $_sequence = true;

    public function loadCity()
    {
        $select = $this->select()
                       ->from($this,array('city_id','city_name'))
                       ->where('city_active = ?', '1');
                        
        $row = $this->fetchAll($select);
        // echo "<prev>";
        //     print_r($row);
        // echo "</prev>";
        return $row;
    }

    public function loadCityById($id)
    {
        $select = $this->select()
                       ->from($this,array('city_id','city_name'))
                       ->where('city_id = ?', $id);
                        
        $row = $this->fetchRow($select);
        return $row;
    }
}
