<?php

class ProfileController extends Zend_Controller_Action
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

        // $this->_helper->layout('homelayout')->disableLayout();
        // $customer = new Default_Model_Customers();   
        // $q = $this->getRequest()->getParam('searchword');
        // $data = $customer->getCustomer($q);

        // $attachments = new Default_Model_Attachments();
        // $dataAttachments = $attachments->loadAttachmentsByCusId($data['cus_id']);

        // if($data != null){
        //     if($dataAttachments != null && sizeof($dataAttachments)>0){
        //         foreach($dataAttachments as $atc){
        //             if($atc['type'] == 'anh'){
        //                 $data['attachment_name_image'] = $this->_request->getBaseUrl().'/files/upload/anh/'.$atc['attachment_name'];
        //                 $data['type_image'] = $atc['type'];
        //             }else{
        //                 $data['attachment_name_pdf'] = $atc['attachment_name'];
        //                 $data['type_pdf'] = $atc['type'];
        //             }
        //         }
        //     }
        // }


        // $this->view->profile = $data;
        // echo json_encode($data);
        // exit; 

    }

    public function printAction(){

    }

}