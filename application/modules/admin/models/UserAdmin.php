<?php

class Admin_Model_UserAdmin  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'user_admin';

    protected $_primary = 'user_id'; 

    protected $_sequence = true;

    function num($username, $password)
    {
     
        $result = count($this->getAdapter()->fetchAll("select * from  user_admin  where
        user_username='".$username."' and 
        user_password='".$password."' "));

        //and users_active=1
         
        return $result;
          
    }

    public function loadUserAdminById($id){
        $id = (int)$id;
        $row = $this->fetchRow('user_id = ' . $id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function loadUserAdminByUsername($username){
        
        $row = $this->fetchRow('user_username = '.$username);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }



    public function loadUserAdmin()
    {
        $select = $this->select()
                       ->from($this,array('user_id','user_name','user_username','user_status'));                      
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    public function loadActiveUserAdmin()
    {
        $select = $this->select()
                       ->from($this,array('user_id','user_name','user_username','user_status'))
                       ->where('user_status = ?', '1');                       
                        
        $row = $this->fetchAll($select);
        return $row;
    }
}