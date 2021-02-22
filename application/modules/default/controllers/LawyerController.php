<?php
// Intership controller 
class LawyerController extends Zend_Controller_Action
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

    public function historyjoiningAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $history = new Default_Model_HistoryJoining();   
        $cus_id = $this->getRequest()->getParam('cus_id');
        $data = $history->loadHistoryJoiningByCusId($cus_id);
        $this->view->histories =  $data;  
    }

    public function detailhistoryAction(){   
        $this->_helper->layout('layout')->disableLayout();
        $history = new Default_Model_HistoryJoining();   
        $history_joining_id = $this->getRequest()->getParam('history_joining_id');
        $data = $history->loadByHistoryId($history_joining_id);
        $this->view->historydata = $data;    
      
    } 

    public function updatehistoryAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

               //if($this->view->parError == ''){ 
                    $intership = new Default_Model_HistoryJoining();
                    $data = array(
                        'law_joining_number'=> $filter->filter($arrInput['law_joining_number']),
                        //'payment_inter_status'=> $filter->filter($arrInput['payment_inter_status']),
                        'law_joining_note' => $filter->filter($arrInput['law_joining_note'])

                    );
                    //$this->view->data = $data;
                    $intership->update($data, 'history_joining_id = '. (int)($filter->filter($arrInput['history_joining_id'])));                   
                //}        
            }
        }    

    }

    public function indexAction(){
        //$this->_helper->layout('homelayout')->disableLayout();
        //load list status law

        $cus_id = $this->getRequest()->getParam('cus_id');

        $customer = new Default_Model_Customers();
        $customerdata = $customer->getCustomerByUserId($cus_id);
        $this->view->customerdata = $customerdata;

        $lawstatus = new Default_Model_LawStatus();
        $data = $lawstatus->loadLawStatusById(1);
        $this->view->status =  $data;

        // // //load list 
        $model = new Default_Model_OrganizationLawDetails();
        $data = $model->loadOrganzationsLaw();
        $this->view->organizations = $data;

        // // //load list 
        $activitys = new Default_Model_ActivityLaw();
        $data = $activitys->loadActivityLaw();
        $this->view->activitys_data =  $data;


        
    }

    public function detaillawyerpageAction(){
        //$this->_helper->layout('homelayout')->disableLayout();
        //load list status law
        $law_id = $this->getRequest()->getParam('law_id');
        $cus_id = $this->getRequest()->getParam('cus_id');

        $customer = new Default_Model_Customers();
        $customerdata = $customer->getCustomerByUserId($cus_id);
        $customerdata['cus_date_lawyer_number'] = date("d/m/Y", strtotime($customerdata['cus_date_lawyer_number']));
        $this->view->customerdata = $customerdata;

        $lawyer = new Default_Model_Lawyer();   
        //$law_id = $this->getRequest()->getParam('law_id');
        $dataLawyer = $lawyer->loadLawyerByLawId($law_id);
        $dataLawyer['law_certification_createdate'] = date("d/m/Y", strtotime($dataLawyer['law_certification_createdate']));
        $this->view->lawyerdata = $dataLawyer;

        $lawstatus = new Default_Model_LawStatus();
        $dataStatus = $lawstatus->loadLawStatus();
        $this->view->status =  $dataStatus;

        // // //load list 
        $model = new Default_Model_OrganizationLawDetails();
        $dataOrganDetails = $model->loadOrganzationsLaw();
        $this->view->organizations = $dataOrganDetails;

        // // //load list 
        $activitys = new Default_Model_ActivityLaw();
        $dataActivity= $activitys->loadActivityLaw();
        $this->view->activitys_data =  $dataActivity;


        
    }

    public function detailAction(){   
        $this->_helper->layout('homelayout')->disableLayout();
        $lawyer = new Default_Model_Lawyer();   
        $law_id = $this->getRequest()->getParam('law_id');
        $data = $lawyer->loadLawyerByLawId($law_id);
        $this->view->lawyerdata = $data;

        $modelCustomers = new Default_Model_Customers();
        $customers = $modelCustomers->getCustomerByUserId($data['cus_id']);
        $this->view->cus_lawyernumber = $customers['cus_lawyer_number'];

        //load list status law
        $lawstatus = new Default_Model_LawStatus();
        $data = $lawstatus->loadLawStatus();
        $this->view->status =  $data;

        // // //load list 
        $organizations = new Default_Model_OrganizationLaw();
        $data = $organizations->loadOrganzationLaw();
        $this->view->organizations_data =  $data;

        // // //load list 
        $activitys = new Default_Model_ActivityLaw();
        $data = $activitys->loadActivityLaw();
        $this->view->activitys_data =  $data;
    }

    public function listAction(){
        $this->_helper->layout('homelayout')->disableLayout();
         $lawyer = new Default_Model_Lawyer();   
         $cus_id = $this->getRequest()->getParam('cus_id');
         $data = $lawyer->loadLawyerByCusId($cus_id);
         $this->view->lawyers =  $data; 
    }

    public function listdataAction(){
        $this->_helper->layout('homelayout')->disableLayout();
         $lawyer = new Default_Model_Lawyer();   
         $cus_id = $this->getRequest()->getParam('cus_id');
         $data = $lawyer->loadLawyerByCusId($cus_id);
         array_walk ( $data, function (&$key) { 
             $key["cus_date_lawyer_number"] = ($key["cus_date_lawyer_number"] != null && $key["cus_date_lawyer_number"] != ''
             && $key["cus_date_lawyer_number"] != '1900-01-01 00:00:00' ) ? date('d/m/Y',strtotime($key["cus_date_lawyer_number"])) : ''; 
             $key["law_certification_createdate"] = ($key["law_certification_createdate"] != null && $key["law_certification_createdate"] != ''
             && $key["law_certification_createdate"] != '1900-01-01 00:00:00') ? date('d/m/Y',strtotime($key["law_certification_createdate"])) : ''; 
         });
         echo json_encode($data);
         exit;  
    }

    /*update information of customer*/
    public function updateAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

               if($this->view->parError == ''){ 
                    $lawyer = new Default_Model_Lawyer();
                    $data = array(
                        //'law_code'=> $filter->filter($arrInput['law_code']),
                        // 'act_id'=> $filter->filter($arrInput['act_id']),
                        'law_certfication_no'=> $filter->filter($arrInput['law_certfication_no']),
                        //'organ_lid'=> $filter->filter($arrInput['organ_lid']),
                        'lawstatus_id'=> $filter->filter($arrInput['lawstatus_id']),
                        'law_joining_number'=> $filter->filter($arrInput['law_joining_number']),
                        'law_organ_note' => $filter->filter($arrInput['law_organ_note'])
                        // ,
                        // 'law_organ_old'=> $filter->filter($arrInput['law_organ_old'])
                        //'law_type'=> $filter->filter($arrInput['law_type'])
                    );
                    //$this->view->data = $data;
                    $lawyer->update($data, 'law_id = '. (int)($filter->filter($arrInput['law_id'])));                   

                    $modelCustomers = new Default_Model_Customers();
                    if($filter->filter($arrInput['lawstatus_id']) != 1){
                        $dataCus = array(
                            'cus_lawyer_number' => $filter->filter($arrInput['law_code']),
                            'cus_status' => $filter->filter($arrInput['lawstatus_id']),
                            'cus_member' => 0
                        );    
                        $modelCustomers->update($dataCus, 'cus_id = '. (int)($filter->filter($arrInput['cus_id'])));
                    }else{
                        $dataCus = array(
                            'cus_lawyer_number' => $filter->filter($arrInput['law_code']),
                            'cus_status' => $filter->filter($arrInput['lawstatus_id'])                            
                        );    
                        $modelCustomers->update($dataCus, 'cus_id = '. (int)($filter->filter($arrInput['cus_id'])));
                    }
                  
                }        
            }
        }    

    }

     /*create new customer*/
     public function createAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                
                    $arrInput = $this->_request->getParams();
                    $this->view->arrInput = $arrInput;

                    $lawyer = new Default_Model_Lawyer();                   

                    if(!Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                        $this->view->parError = 'Bạn phải chọn người để tạo luật sư chính thức! ';
                    }

                    if(Zend_Validate::is($arrInput['cus_member'],'NotEmpty')){
                        if($arrInput['cus_member'] == 1){
                            $this->view->parError = 'Bạn đã là thành viên của Đoàn, không thể tạo gia nhập mới. ';                                                    
                        }
                    //    if((sizeof($lawyer->loadLawyerByCusId($arrInput['cus_id']))>0)){
                    //         $this->view->parError = 'Luật sư này đã gia nhập, bạn không thể tạo nữa! Nếu chưa là luật sư tại đoàn, vui lòng kiểm tra lại phí gia nhập đã thanh toán chưa. ';                                                    
                    //    }
                    }

                    //$duplicateCuslawyernumber = false;

                    // if(Zend_Validate::is($arrInput['law_code'],'NotEmpty')){
                    //     $dataCodes = $lawyer->loadLawyerByCusId($arrInput['cus_id']);
                    //     if(sizeof($dataCodes)>0){
                    //         foreach($dataCodes as $d){
                    //            if($d['cus_lawyer_number'] != '' && $d['cus_lawyer_number'] != null && $d['cus_lawyer_number'] == $arrInput['law_code']){
                    //             //$this->view->parError = 'Lỗi, trùng số thẻ luật sư! ';
                    //             $duplicateCuslawyernumber = true;
                    //            }     
                    //         }                            
                    //     }
                    // }

                   /* covert date cmnd*/
                    $law_code_createdate = $filter->filter($arrInput['law_code_createdate']);
                    $date_law = str_replace('/', '-', $law_code_createdate);
                    $final_law_code_createdate =  date('Y-m-d', strtotime($date_law));

                    /* covert date cmnd*/
                    $law_code_createdate_different = $filter->filter($arrInput['law_code_createdate_different']);
                    $date_law_different = str_replace('/', '-', $law_code_createdate_different);
                    $final_law_code_createdate_different =  date('Y-m-d', strtotime($date_law_different));

                    /* covert date birthday*/
                    $law_certification_createdate = $filter->filter($arrInput['law_certification_createdate']);
                    $date_law_certification_createdate = str_replace('/', '-', $law_certification_createdate);
                    $final_law_certification_createdate =  date('Y-m-d', strtotime($date_law_certification_createdate));

                    // $law_organ_old = NULL;
                    // if(isset($arrInput['law_organ_old'])){
                    //     $law_organ_old  = $filter->filter($arrInput['law_organ_old']);
                    // }

                    $law_joining_number = NULL;
                    if(isset($arrInput['law_joining_number'])){
                        $law_joining_number = $filter->filter($arrInput['law_joining_number']);
                    }

                    $this->auth = Zend_Auth::getInstance();
                    $this->identity = $this->auth->getIdentity();
            
                    $username= $this->identity->user_username;

                if($this->view->parError == ''){
                    $cus_id = $filter->filter($arrInput['cus_id']);
                    $db = Zend_Db_Table::getDefaultAdapter();
                    $select = new Zend_Db_Select($db);
                    $select->from('lawyer', array('law_id','cus_id'))
                    ->where('lawyer.cus_id = ?',$cus_id)                    
                    ->limit(1);
                    
                    $resultSet = $db->fetchRow($select);

                    $currentdate = new Zend_Date();            

                    if($resultSet != null){
                        if($resultSet['cus_id'] != null && $resultSet['law_id'] != null){
                            $data = array(       
                                // 'law_id'=> '1',                
                                //'cus_id' => $filter->filter($arrInput['cus_id']),
                                // 'law_code' => $filter->filter($arrInput['law_code']),                        
                                // 'law_code_createdate' => $final_law_code_createdate,
                                //'act_id' => $filter->filter($arrInput['act_id']),
                                'law_certfication_no' => $filter->filter($arrInput['law_certfication_no']),
                                'law_certification_createdate' =>  $final_law_certification_createdate,
                                'organ_lid' =>  $filter->filter($arrInput['organ_lid']),
                                'lawstatus_id' =>  $filter->filter($arrInput['lawstatus_id']),
                                'law_type' => $filter->filter($arrInput['joiningvalue']) == 'organ' ? 'Luật sư tại đoàn' :'Luật sư đoàn khác chuyển đến',
                                'law_joining_number' =>  $law_joining_number,
                                'law_organ_note' => $filter->filter($arrInput['law_organ_note']),
                                //'law_organ_old' =>  $law_organ_old,
                                //'law_code_createdate_different' => $final_law_code_createdate_different,
                                //'law_organ_address' => $filter->filter($arrInput['law_organ_address']),
                                //'district' => $filter->filter($arrInput['district']),
                                //'law_organ_phone' => $filter->filter($arrInput['law_organ_phone']),
                                'updateddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                                'updated_username' => $username
                              
                            );
        
                            $lawyer->update($data, 'law_id = '. (int)($resultSet['law_id'])); 
                        }
                        
                    }else{
                        $data = array(       
                            // 'law_id'=> '1',                
                            'cus_id' => $filter->filter($arrInput['cus_id']),
                            // 'law_code' => $filter->filter($arrInput['law_code']),                        
                            // 'law_code_createdate' => $final_law_code_createdate,
                            //'act_id' => $filter->filter($arrInput['act_id']),
                            'law_certfication_no' => $filter->filter($arrInput['law_certfication_no']),
                            'law_certification_createdate' =>  $final_law_certification_createdate,
                            'organ_lid' =>  $filter->filter($arrInput['organ_lid']),
                            'lawstatus_id' =>  $filter->filter($arrInput['lawstatus_id']),
                            'law_type' => $filter->filter($arrInput['joiningvalue']) == 'organ' ? 'Luật sư tại đoàn' :'Luật sư đoàn khác chuyển đến',
                            'law_joining_number' =>  $law_joining_number,
                            'law_organ_note' => $filter->filter($arrInput['law_organ_note']),
                            //'law_organ_old' =>  $law_organ_old,
                            'law_code_createdate_different' => $final_law_code_createdate_different,
                            //'law_organ_address' => $filter->filter($arrInput['law_organ_address']),
                            //'district' => $filter->filter($arrInput['district']),
                            //'law_organ_phone' => $filter->filter($arrInput['law_organ_phone']),
                            'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'created_username' => $username
                          
                        );
    
                        $lawyer->insert($data); 
                    }   
                    
                    //check truong hop neu la update cap nhat thong tin
                    $modelHistory = new Default_Model_HistoryJoining();
                    $currentdate = new Zend_Date(); 

                    $model = new Default_Model_Customers();
                    if(Zend_Validate::is($arrInput['crudation'],'NotEmpty')){
                        // case update
                        if(Zend_Validate::is($arrInput['lawstatus_id'],'NotEmpty')){
                            if($filter->filter($arrInput['lawstatus_id']) != 1){
                                $dataUpdate = array(
                                    // update cus member =0 boi vi luat su van chua thanh toan
                                    'cus_member'=> '0',
                                    'cus_status' => $filter->filter($arrInput['lawstatus_id']),
                                    'cus_lawyer_number' => $filter->filter($arrInput['law_code']),
                                    'cus_date_lawyer_number' => $final_law_code_createdate,
                                    'cus_type' => 1
                                );
                                $model->update($dataUpdate, 'cus_id = '. (int)($filter->filter($arrInput['cus_id'])));

                                if($resultSet['cus_id'] != null && $resultSet['law_id'] != null){
                                    $lawyer = new Default_Model_Lawyer(); 
                                    $dataLawyer = array(
                                        'lawstatus_id' =>  $filter->filter($arrInput['lawstatus_id']),
                                        'updateddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                                        'updated_username' => $username,
                                        'law_joining_number' =>  $law_joining_number
                                    );
                                    $lawyer->update($data, 'law_id = '. (int)($resultSet['law_id'])); 
                                }

                                //$cus_id = $filter->filter($arrInput['cus_id']);
                                $db = Zend_Db_Table::getDefaultAdapter();
                                $select = new Zend_Db_Select($db);
                                $select->from('history_joining', array('history_joining_id','cus_id'))
                                ->where('history_joining.cus_id = ?',$filter->filter($arrInput['cus_id']))  
                                ->order('history_joining.history_joining_id desc')                  
                                ->limit(1);
                                $resultHistory = $db->fetchRow($select);

                                if($resultHistory != null){
                                    if($resultHistory['history_joining_id'] != null){
                                        $dataHistory = array(
                                            'law_joining_number' => $law_joining_number
                                        );                                
                                        $modelHistory->update($dataHistory, 'history_joining_id = '. (int)($resultHistory['history_joining_id']));
                                    }
                                }
                                
                            }else{
                                $dataUpdate = array(
                                    // update cus member =0 boi vi luat su van chua thanh toan
                                    'cus_member'=> '1',
                                    'cus_status' => $filter->filter($arrInput['lawstatus_id']),
                                    'cus_lawyer_number' => $filter->filter($arrInput['law_code']),
                                    'cus_date_lawyer_number' => $final_law_code_createdate,
                                    'cus_type' => 1
                                );
                                $model->update($dataUpdate, 'cus_id = '. (int)($filter->filter($arrInput['cus_id'])));
                            
                                if($resultSet['cus_id'] != null && $resultSet['law_id'] != null){
                                    $lawyer = new Default_Model_Lawyer(); 
                                    $dataLawyer = array(
                                        'lawstatus_id' =>  $filter->filter($arrInput['lawstatus_id']),
                                        'updateddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                                        'updated_username' => $username
                                    );
                                    $lawyer->update($data, 'law_id = '. (int)($resultSet['law_id'])); 
                                }
                            
                            
                            }
                        }               
                        
                    }else{
                        $dataHistory = array(
                            'cus_id' => $filter->filter($arrInput['cus_id']),
                            'law_joining_number' => $law_joining_number,
                            'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'created_username' => $username,
                            'law_joining_note' => $filter->filter($arrInput['law_organ_note']),
                            'payment_joining_status' => 0
                        );
                        $modelHistory->insert($dataHistory);
                            
                        
                        $dataUpdate = array(
                            // update cus member =0 boi vi luat su van chua thanh toan
                            'cus_member'=> '1',
                            'cus_status' => $filter->filter($arrInput['lawstatus_id']),
                            'cus_lawyer_number' => $filter->filter($arrInput['law_code']),
                            'cus_date_lawyer_number' => $final_law_code_createdate,
                            'cus_type' => 1
                        );
                        //if($filter->filter($arrInput['joiningvalue']) == 'organ'){
                        $model->update($dataUpdate, 'cus_id = '. (int)($filter->filter($arrInput['cus_id'])));
                        //}
                    }                                 
                    $this->view->data = $data;
                    //exit;
               }        
            }
        }    
    }

    // funtion xóa tên sẽ được lưu vào bảng lawyer_removed có 2 lý do xóa 
    // kỉ luật và nợ 18 tháng
    public function removeAction(){
        $this->_helper->layout('layout')->disableLayout();

        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();

        $username= $this->identity->user_username;
        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                $lawyer = new Default_Model_Lawyer();
                $modelLawyerRemoved = new Default_Model_LawyerRemoved(); 
                $model = new Default_Model_Customers(); 

                if($this->view->parError == ''){

                    $law_ids = $this->getRequest()->getParam('law_ids');
                    
                    $currentdate = new Zend_Date();

                    if($law_ids != null && sizeof($law_ids)){
                        foreach($law_ids as $id){
                            $dataLawyerCusId = $lawyer->loadLawyerByLawId((int)($id));                           
                           
                            $data = array(       
                                'law_id' => (int)$id,
                                'year' => $filter->filter($arrInput['yeardelete'])
                                ,'type' => $filter->filter($arrInput['type']) == 'notien' ? 'Nợ Đoàn phí' : 'Kỷ luật' 
                                ,'type_deleted' => $filter->filter($arrInput['type']) == 'notien' ? 'nophi' : 'kiluat' 
                                ,'document_removed' =>$filter->filter($arrInput['document'])                                  
                                ,'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss')
                                ,'created_username' => $username
                                ,'cus_id' => (int)($dataLawyerCusId['cus_id'])
                            );                        
                            $modelLawyerRemoved->insert($data);

                            $dataLawyer = array(
                               'lawstatus_id' => '7'     
                            );
                            $lawyer->update($dataLawyer, 'law_id = '. (int)($id));
                            
                            
                            $dataCustomerUpdate = array(
                                'cus_member'=> 0,
                                'cus_status'=> '7'
                            );
                            $model->update($dataCustomerUpdate, 'cus_id = '. (int)($dataLawyerCusId['cus_id']));
                            // $dataUpdate = array(
                            //     // update cus member =0 boi vi luat su van chua thanh toan
                            //     'cus_member'=> '0',
                            //     'cus_status' => $filter->filter($arrInput['lawstatus_id']),
                            //     'cus_lawyer_number' => $filter->filter($arrInput['law_code'])
                            // );
                            // //if($filter->filter($arrInput['joiningvalue']) == 'organ'){
                            //     $model->update($dataUpdate, 'cus_id = '. (int)($filter->filter($arrInput['cus_id'])));
                        }
                    }              
                        
                   
                    //}
                                       
                    $this->view->data = $data;
                    //exit;
               }        
            }
        }        

    }

 
}    