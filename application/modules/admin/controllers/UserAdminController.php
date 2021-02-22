<?php
class Admin_UseradminController extends Zend_Controller_Action{
	
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

    public function createuseradminpageAction(){

    } 

    public function createAction(){
        $this->_helper->layout('layout')->disableLayout();
        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                $useradminModel = new Admin_Model_UserAdmin();
                if(!Zend_Validate::is($arrInput['username'],'NotEmpty')){
                    $checkUserExisted = $useradminModel->loadUserAdminByUsername(trim($filter->filter($arrInput['username'])));
                    if($checkUserExisted != null){
                        $this->view->parError = 'Tên đăng nhập đã tồn tại trong hệ thống !';   
                    }                         
                }  
                
                $currentdate = new Zend_Date();

                if($this->view->parError == ''){ 
                    $data = array(
                        'user_name' => trim($filter->filter($arrInput['username'])),
                        'user_username' => trim($filter->filter($arrInput['username'])),
                        'user_password' => md5(trim($filter->filter($arrInput['password']))),
                        'user_status' => 1,
                        'user_creatdate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss')
                    );

                    $useradminModel->insert($data); 
                }
            }
        }
    }

    public function indexAction(){ 
          $useradmin = new Admin_Model_UserAdmin();
          $data = $useradmin->loadUserAdmin();
          $this->view->list =  $data;
     } 

     public function updatestatuscustomerAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if($this->view->parError == ''){ 
                    $customer = new Admin_Model_UserAdmin();
                    $status = 0;
                    if($arrInput['user_status'] != '' && $arrInput['user_status'] == 'active'){
                        $status = 1;
                    }
                    $data = array(
                        'user_status' => $status
                    );

                    $customer->update($data, 'user_id = '. (int)($filter->filter($arrInput['user_id_lock'])));                   
                
                    /*insert log action*/
                    // $this->auth = Zend_Auth::getInstance();
                    // $this->identity = $this->auth->getIdentity();
                    // $currentdate = new Zend_Date();
                    // $useradminlog = new Default_Model_UserAdminLog();
                    // $datalog = array(
                    //     'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                    //     'useradmin_username' => $this->identity->user_username,
                    //     'action' => 'Cập nhật trạng thái luật sư',
                    //     'page' => $this->_request->getControllerName(),
                    //     'useradmin_id' => $this->identity->user_id,
                    //     'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                    //     'access_object' => $filter->filter($arrInput['cus_id_lock'])
                    // );
                    // $useradminlog->insert($datalog);

                }
            } 
        }   
    }

    /*update password*/
    public function updatenewpasswordAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if($this->view->parError == ''){ 
                    $customer = new Admin_Model_UserAdmin();
                    $data = array(
                        'user_password' => md5(trim($filter->filter($arrInput['password']))),
                    );

                    $customer->update($data, 'user_id = '. (int)($filter->filter($arrInput['user_id'])));                   
                
                    /*insert log action*/
                    // $this->auth = Zend_Auth::getInstance();
                    // $this->identity = $this->auth->getIdentity();
                    // $currentdate = new Zend_Date();
                    // $useradminlog = new Default_Model_UserAdminLog();
                    // $datalog = array(
                    //     'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                    //     'useradmin_username' => $this->identity->user_username,
                    //     'action' => 'Cập nhật mật khẩu luật sư',
                    //     'page' => $this->_request->getControllerName(),
                    //     'useradmin_id' => $this->identity->user_id,
                    //     'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                    //     'access_object' => $filter->filter($arrInput['cus_id'])
                    // );
                    // $useradminlog->insert($datalog);
                
                }        
            }
        }    

    }
	

}