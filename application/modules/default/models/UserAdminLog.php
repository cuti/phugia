<?php

class Default_Model_UserAdminLog  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'useradmin_log_action';

    protected $_primary = 'useradmin_log_id'; 

    protected $_sequence = true;

    // public function loadCity()
    // {
    //     $select = $this->select()
    //                    ->from($this,array('city_id','city_name'))
    //                    ->where('city_active = ?', '1');
                        
    //     $row = $this->fetchAll($select);
    //     // echo "<prev>";
    //     //     print_r($row);
    //     // echo "</prev>";
    //     return $row;
    // }
}
