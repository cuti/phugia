<?php
// Login controller 
class Admin_LoginController extends Zend_Controller_Action
{

    public function init(){ 
        $this->view->BaseUrl=$this->_request->getBaseUrl();
        
    } 

    public function preDispatch()
    {
        $this->_helper->layout->setLayoutPath(APPLICATION_PATH.'/modules/admin/views/scripts/login');
    }

    public function indexAction()
    {
    	
	 Zend_Session::rememberMe(7200); // 1 hour
	 Zend_Session::start();

       $this->view->pageTitle = 'Administrator Page';
     

	
			$username= $this->_request->getParam('username','');	
			$password= MD5($this->_request->getParam('password',''));
   if ($this->_request->isPost()) {
     
		        $users = new Admin_Model_UserAdmin();	    
                $auth = Zend_Auth::getInstance();
                $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(),'user_admin');
                $authAdapter->setIdentityColumn('user_username')
                            ->setCredentialColumn('user_password');

                $authAdapter->setIdentity($username)
                            ->setCredential($password);
                             
                              
                $result = $auth->authenticate($authAdapter);

  
                if ($result->isValid()) { 
                    // success: store database row to auth's storage  
                    // (Not the password though if  take cus_password trong ''!) 
                    
		        
                    $data = $authAdapter->getResultRowObject(); 
                    $auth->getStorage()->write($data);  
                    $_SESSION['login']="good";
                    $_SESSION['config']=$this->view->BaseUrl;
                    $_SESSION['username']= $username;
                    $this->_redirect('/admin/index');      
                }
      	   else
      	   {
      	    $this->view->note =  "Thông tin username hoặc password không đúng!!!";
      	   }
            
     
    }
  
 
    
    }   

	

 
}


