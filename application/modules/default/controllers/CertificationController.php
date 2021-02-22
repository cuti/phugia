<?php
// Fee controller 
class CertificationController extends Zend_Controller_Action
{
    public function init(){
        $this->view->BaseUrl=$this->_request->getBaseUrl();
    }

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




    public function listcertificationAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymenttrainingoffline = new Default_Model_PaymentTrainingOffline();  
        $cus_id = $this->getRequest()->getParam('cus_id'); 
        $data = $paymenttrainingoffline->loadPaymentLawyerOfflineToCreateCertificationByCusId($cus_id);
        $this->view->paymentofflines =  $data; 


        /*insert log action*/
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
        $currentdate = new Zend_Date();
        $useradminlog = new Default_Model_UserAdminLog();
        $datalog = array(
            'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
            'useradmin_username' => $this->identity->user_username,
            'action' => 'Xem danh sách chứng chỉ',
            'page' => $this->_request->getControllerName(),
            'useradmin_id' => $this->identity->user_id,
            'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
            'access_object' => ''
        );
        $useradminlog->insert($datalog);
    }
}