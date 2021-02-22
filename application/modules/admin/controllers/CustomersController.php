<?php
// Login controller 
class Admin_CustomersController extends Zend_Controller_Action
{

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

    public function searchAction(){
        $this->_helper->layout('homelayout')->disableLayout();

        // if ($this->getRequest()->isXmlHttpRequest()) {
        //     if ($this->getRequest()->isPost()) {
    
                 $q = $this->getRequest()->getParam('searchword');                
                 $search = new Admin_Model_Customers();
                 $result = $search->getCustomer($q);
               
                 //$this->view->data = $result;
                 $this->view->q = $q;
                if($result){
                    $this->view->data = "Tồn tại số điện thoại ";
                }else{
                    $this->view->data = "Không tồn tại số điện thoại ";     
                }    
    
        //     }
        // }       
   }
}