<?php

class ExportController extends Zend_Controller_Action{
	
	public function  preDispatch(){
 	
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
 
        $username= $this->identity->user_username;
        $password= $this->identity->user_password;
 
        $users2 = new Default_Model_UserAdmin();  
        if ($users2->num($username, $password)>0) {                     
        
        }else{
              $this->_redirect('/default/login');exit;
        }
    }

}


