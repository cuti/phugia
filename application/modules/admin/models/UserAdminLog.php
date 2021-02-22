<?php

class Admin_Model_UserAdminLog  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'useradmin_log_action';

    protected $_primary = 'useradmin_log_id'; 

    // /*get customer by phone*/
    // public function getCustomer($phone_number)
    // {
    //     $phone_number = (int)$phone_number;
    //     $row = $this->fetchRow('cus_cellphone = ' . $phone_number);
    //     if (!$row) {
    //         return null;
    //     }
    //     return $row->toArray();
    // }

    public function loadLogByFilter($search,$start,$length){
             
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select
                       ->from('useradmin_log_action',array('useradmin_log_id',
                       'page',
                       'action','useradmin_username','useradmin_id','access_object','ip',
                       'createddate'));                       
        if($search != ''){
            $query = $query->where('useradmin_log_action.useradmin_username LIKE ?', '%' . $search . '%');
        }      
                  
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
        //return $row;

    }


    public function countLogByFilter(){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select
                       ->from('useradmin_log_action',array('useradmin_log_id',
                       'page',
                       'action','useradmin_username','useradmin_id','access_object','ip',
                       'createddate')); 

        $row = $db->fetchAll($query);
        return $row;          

    }

}    