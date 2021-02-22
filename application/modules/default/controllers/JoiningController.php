<?php

class JoiningController extends Zend_Controller_Action
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
        $joining = new Default_Model_Joining();
        $this->view->joinings = $joining->loadJoining();
    }

    public function updateAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if($this->view->parError == ''){ 
                    $lawyer = new Default_Model_Joining();
                    $data = array(
                        'joining_status'=> $filter->filter($arrInput['joining_status'])                                           
                    );
                    //$this->view->data = $data;
                    $lawyer->update($data, 'joining_id = '. (int)($filter->filter($arrInput['joining_id'])));                   
                }        
            }
        }    

    }

    public function detailAction(){   
        $this->_helper->layout('homelayout')->disableLayout();
        $joining = new Default_Model_Joining();   
        $joining_id = $this->getRequest()->getParam('joining_id');
        $data = $joining->loadJoiningById($joining_id);
        $this->view->joiningdata = $data;

    }

    public function createAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                // $startdate = $filter->filter($arrInput['joining_startdate']);
                // $date_startdate = str_replace('/', '-', $startdate);
                // $final_startdate =  date('Y-m-d', strtotime($date_startdate));
              
                // $enddate = $filter->filter($arrInput['joining_enddate']);
                // $date_enddate = str_replace('/', '-', $enddate);
                // $final_enddate =  date('Y-m-d', strtotime($date_enddate)); 

                if($this->view->parError == ''){
                    $joining = new Default_Model_Joining();
                    $data = array(                   
                        'joining_name' => $filter->filter($arrInput['joining_name']),                      
                        'joining_status' => '1',
                        'joining_startdate' => $filter->filter($arrInput['joining_startdate']),
                        'joining_enddate' => $filter->filter($arrInput['joining_enddate'])
                        
                    );

                    $joining->insert($data);                   
                   
               }        
            }
        }    
    }

}    
