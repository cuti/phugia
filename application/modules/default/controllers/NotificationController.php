
<?php

class NotificationController extends Zend_Controller_Action
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

    public function indexAction(){
        $model = new Default_Model_Notification();         
        $data = $model->loadNotifications();
        $this->view->notifications =  $data;
    }

    public function editnotificationpageAction(){
        $notifacation_id = $this->getRequest()->getParam('id');
        
        $modelNotification = new Default_Model_Notification();
        $dateEmpty = '1900-01-01 00:00:00';
        $data = $modelNotification->getNotificationById($notifacation_id);
        if($data != null){
            $data['activationdate'] = ($data['activationdate'] != null && $data['activationdate'] != '' && $data['activationdate'] != $dateEmpty) ? date('d/m/Y',strtotime($data['activationdate'])) : '';
            $data['expiredate'] = ($data['expiredate'] != null && $data['expiredate'] != '' && $data['expiredate'] != $dateEmpty) ? date('d/m/Y',strtotime($data['expiredate'])) : '';
        }
        $this->view->data =  $data;

    }

    public function updateAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $filter = new Zend_Filter();
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput; 
                $currentdate = new Zend_Date();

                $model = new Default_Model_Notification();

                $activationdate = $filter->filter($arrInput['activationdate']);
                $date_activationdate = str_replace('/', '-', $activationdate);
                $final_activationdate =  date('Y-m-d', strtotime($date_activationdate)); 


                $expiredate = $filter->filter($arrInput['expiredate']);
                $date_expiredate = str_replace('/', '-', $expiredate);
                $final_expiredate =  date('Y-m-d', strtotime($date_expiredate)); 

                $this->auth = Zend_Auth::getInstance();
                $this->identity = $this->auth->getIdentity();

                if($this->view->parError == ''){               
                    $data = array(
                        'title' => $filter->filter($arrInput['title']),
                        'description' => $filter->filter($arrInput['description']),
                        'activationdate' => $final_activationdate,
                        'expiredate' => $final_expiredate,                     
                        'updateddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'updated_username' => $this->identity->user_username
                        //'status' =>            
                    );
                    $model->update($data, 'notification_id = '. (int)($filter->filter($arrInput['notification_id'])));                   
                    
                        
                }
            }
        }
    }

    public function createAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $filter = new Zend_Filter();
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput; 
                $currentdate = new Zend_Date();

                $model = new Default_Model_Notification();

                $activationdate = $filter->filter($arrInput['activationdate']);
                $date_activationdate = str_replace('/', '-', $activationdate);
                $final_activationdate =  date('Y-m-d', strtotime($date_activationdate)); 


                $expiredate = $filter->filter($arrInput['expiredate']);
                $date_expiredate = str_replace('/', '-', $expiredate);
                $final_expiredate =  date('Y-m-d', strtotime($date_expiredate)); 

                $this->auth = Zend_Auth::getInstance();
                $this->identity = $this->auth->getIdentity();

                if($this->view->parError == ''){               
                    $data = array(
                        'title' => $filter->filter($arrInput['title']),
                        'description' => $filter->filter($arrInput['description']),
                        'activationdate' => $final_activationdate,
                        'expiredate' => $final_expiredate,                     
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'created_username' => $this->identity->user_username
                        //'status' =>            
                    );
                    $model->insert($data);
                        
                }
            }
        }
    }


}