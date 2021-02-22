<?php
class Admin_IndexController extends Zend_Controller_Action{
	
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
        $intership = new Default_Model_Intership();
        $countintership = $intership->countIntershipByFilter();
        $this->view->numberintership = count($countintership);

        $lawyer = new Default_Model_Lawyer();
        $countlawyer = $lawyer->countLawyerActiveByFilter('1');
        $this->view->numberlawyer = count($countlawyer);

        $customer = new Default_Model_Customers();
        $countcustomer = $customer->loadCustomers();
        $this->view->countcustomer = count($countcustomer);
  
    } 
	

}