<?php
class Admin_ModuleController extends Zend_Controller_Action{
	
	public function init(){ 
        $this->view->BaseUrl=$this->_request->getBaseUrl();
    } 


    public function  preDispatch(){
 	
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
 
        $username= $this->identity->user_username;
        $password= $this->identity->user_password;
 
        $users2 = new Admin_Model_UserAdmin();  
        if ($users2->num($username, $password)>0) {                     
        
        }else{
              $this->_redirect('/admin/login');exit;
        }
    }

    public function indexAction(){ 
        $module = new Admin_Model_Module();
        $data = $module->loadActiveModule();
        $this->view->modules =  $data;
   } 

}