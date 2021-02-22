<?php
// Fee controller 
class TrainingManagementController extends Zend_Controller_Action
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

    // craet luat su tinh
    public function createnewcustomerpageAction(){
        $city = new Default_Model_City();
        $data = $city->loadCity();
        $this->view->cities =  $data;

        // load thông tin của tchn ls
        $organizationDetail = new Default_Model_OrganizationLawDetails();
        $dataOrganLawDetails = $organizationDetail ->loadOrganzationsLaw();
        $this->view->organizations_data =  $dataOrganLawDetails;

        /*insert log action*/
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
        $currentdate = new Zend_Date();
        $useradminlog = new Default_Model_UserAdminLog();
        $datalog = array(
            'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
            'useradmin_username' => $this->identity->user_username,
            'action' => 'Xem danh sách thành phố',
            'page' => $this->_request->getControllerName(),
            'useradmin_id' => $this->identity->user_id,
            'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
            'access_object' => ''
        );
        $useradminlog->insert($datalog);

    }


    public function createnewcustomerAction(){
        $this->_helper->layout('layout')->disableLayout();
        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {         
                    $arrInput = $this->_request->getParams();
                    $this->view->arrInput = $arrInput;

                    $customer = new Default_Model_Customers();
                    if(Zend_Validate::is($arrInput['cus_cellphone'],'NotEmpty')){
                        if($customer->validateIndentityCardOrPhoneOrEmail('phone',
                            $filter->filter($arrInput['cus_cellphone'])) >0 ){
                            $this->view->parError = 'Lỗi: Số điện thoại đã tồn tại trong hệ thống!';        
                            
                            $datatest = array(
                                'id' => '',
                                'error' => $this->view->parError,
                                'type' => $filter->filter($arrInput['submittype'])                               
                            );

                            echo json_encode($datatest);
                            exit;  
                        }
                        
                    }

                    if(Zend_Validate::is($arrInput['cus_identity_card'],'NotEmpty')){
                        if($customer->validateIndentityCardOrPhoneOrEmail('ID',
                            $filter->filter($arrInput['cus_identity_card'])) >0 ){
                            $this->view->parError = 'Lỗi: Số CMND đã tồn tại trong hệ thống!';
                            
                            $datatest = array(
                                'id' => '',
                                'error' => $this->view->parError,
                                'type' => $filter->filter($arrInput['submittype'])                               
                            );
                            echo json_encode($datatest);
                            exit;                                    
                        }
                        
                    }

                    if(Zend_Validate::is($arrInput['cus_email'],'NotEmpty')){
                        if($customer->validateIndentityCardOrPhoneOrEmail('email',
                            $filter->filter($arrInput['cus_email'])) >0 ){
                            $this->view->parError = 'Lỗi: Email đã tồn tại trong hệ thống!'; 
                            
                            $datatest = array(
                                'id' => '',
                                'error' => $this->view->parError,
                                'type' => $filter->filter($arrInput['submittype'])                               
                            );
                            echo json_encode($datatest);
                            exit;         
                        }                        
                    }

                    if(Zend_Validate::is($arrInput['cus_identity_card'],'NotEmpty')){
                        if($customer->validateIndentityCardOrPhoneOrEmail('username',
                            $filter->filter($arrInput['cus_identity_card'])) >0 ){
                            $this->view->parError = 'Lỗi : Username đã tồn tại trong hệ thống!'; 
                            
                            $datatest = array(
                                'id' => '',
                                'error' => $this->view->parError,
                                'type' => $filter->filter($arrInput['submittype'])                               
                            );
                            echo json_encode($datatest);
                            exit;        
                        }                        
                    }

                   /* covert date cmnd*/
                    $final_cmnd = '';
                    $cmnd = $filter->filter($arrInput['cus_identity_date']);
                    if($cmnd != null && $cmnd != ''){
                        $date_cmnd = str_replace('/', '-', $cmnd);
                        $final_cmnd =  date('Y-m-d', strtotime($date_cmnd));
                    }                  

                    /* covert date birthday*/
                    $final_birthday = '';
                    $birthday = $filter->filter($arrInput['cus_birthday']);
                    if($birthday != null && $birthday != ''){                        
                        $date_birthday = str_replace('/', '-', $birthday);
                        $final_birthday =  date('Y-m-d', strtotime($date_birthday));
                    }

                    $cus_date_lawyer_number_final = '';
                    $cus_date_lawyer_number = $filter->filter($arrInput['cus_date_lawyer_number']);
                    if($cus_date_lawyer_number != null && $cus_date_lawyer_number != ''){                        
                        $cus_date_lawyer_number_1 = str_replace('/', '-', $cus_date_lawyer_number);
                        $cus_date_lawyer_number_final =  date('Y-m-d', strtotime($cus_date_lawyer_number_1));
                    }
                   

                    /* covert date passport*/
                    // $passport = $filter->filter($arrInput['cus_passport_date']);
                    // $date_passport = str_replace('/', '-', $passport);
                    // $final_passport =  date('Y-m-d', strtotime($date_passport)); 

                    /* covert date cus_joining_communist_youth*/
                    // $cus_joining_communist_youth = $filter->filter($arrInput['cus_joining_communist_youth']);
                    // $date_cus_joining_communist_youth = str_replace('/', '-', $cus_joining_communist_youth);
                    // $final_cus_joining_communist_youth =  date('Y-m-d', strtotime($date_cus_joining_communist_youth)); 

                    /* covert date cus_joining_communist_prepare*/
                    $final_cus_joining_communist_prepare = '';
                    $cus_joining_communist_prepare = $filter->filter($arrInput['cus_joining_communist_prepare']);
                    if($cus_joining_communist_prepare != null && $cus_joining_communist_prepare != ''){                        
                        $date_cus_joining_communist_prepare = str_replace('/', '-', $cus_joining_communist_prepare);
                        $final_cus_joining_communist_prepare =  date('Y-m-d', strtotime($date_cus_joining_communist_prepare));
                    }                   

                    /* covert date cus_joining_communist*/
                    $final_cus_joining_communist = '';
                    $cus_joining_communist = $filter->filter($arrInput['cus_joining_communist']);
                    if($cus_joining_communist != null && $cus_joining_communist != ''){                        
                        $date_cus_joining_communist = str_replace('/', '-', $cus_joining_communist);
                        $final_cus_joining_communist =  date('Y-m-d', strtotime($date_cus_joining_communist)); 
                    }                   

                    // $image = $filter->filter($arrInput['image']);
                    // $attachment = $filter->filter($arrInput['attachment']);
                    $gioitinh = 'Nam';
                    if($filter->filter($arrInput['cus_sex']) == "nu"){
                        $gioitinh = "Nữ";
                    }        
                    
                if($this->view->parError == ''){                

                    $currentdate = new Zend_Date();                    
                   
                    $data = array(                       
                        'cus_firstname' => $filter->filter($arrInput['cus_firstname']),
                        'cus_lastname' => $filter->filter($arrInput['cus_lastname']),
                        'cus_sex' => $gioitinh,
                        // 'cus_country' => $filter->filter($arrInput['cus_country']),
                        'cus_birthday' => $final_birthday,
                        // 'cus_joining_communist_youth' => $final_cus_joining_communist_youth,
                        //'cus_joining_communist_prepare' => $final_cus_joining_communist_prepare,
                        //'cus_joining_communist' => $final_cus_joining_communist,
                        //'cus_nation' => $filter->filter($arrInput['cus_nation']),
                        // 'cus_birthplace' => $filter->filter($arrInput['cus_birthplace']),
                        'cus_cellphone' => $filter->filter($arrInput['cus_cellphone']),  
                        // 'cus_homephone' => $filter->filter($arrInput['cus_homephone']),  
                        'cus_identity_card' => $filter->filter($arrInput['cus_identity_card']), 
                        'cus_identity_place' => $filter->filter($arrInput['cus_identity_place']),
                        'cus_identity_date' => $final_cmnd,
                        // 'cus_passport_card' => $filter->filter($arrInput['cus_passport_card']),
                        // 'cus_passport_place' => $filter->filter($arrInput['cus_passport_place']),
                        // 'cus_passport_date' => $final_passport,
                        //'cus_address_resident' => $filter->filter($arrInput['cus_address_resident']),
                        //'cus_educations' => $filter->filter($arrInput['cus_educations']),
                        //'cus_language_level' => $filter->filter($arrInput['cus_language_level']),
                        'cus_date_created' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'city_id' => $filter->filter($arrInput['city_id']),                                             
                        'cus_username' => $filter->filter($arrInput['cus_identity_card']),
                        'cus_password' => md5(trim($filter->filter($arrInput['cus_identity_card']))),
                        'cus_fullname' => ($filter->filter($arrInput['cus_firstname']).' '.$filter->filter($arrInput['cus_lastname'])),
                        'cus_email' => $filter->filter($arrInput['cus_email']),
                        //'cus_major' => $filter->filter($arrInput['cus_major']),
                        //'cus_address_resident_now' => $filter->filter($arrInput['cus_address_resident_now']),
                        'cus_member' => '0',
                        'cus_status' => '1',
                        'cus_active' => '1',
                        //'cus_religion'=> $filter->filter($arrInput['cus_religion']),
                        'cus_ls_tinh' => 1,
                        'cus_date_lawyer_number_final' => $cus_date_lawyer_number_final,
                        'cus_lawyer_number' => $filter->filter($arrInput['cus_lawyer_number']),
                        'cus_lawyer_cityid' => $filter->filter($arrInput['cus_lawyer_cityid'])
                        //'cus_organization' => $filter->filter($arrInput['cus_organization']),
                        //'cus_lawyer_number' =>  $filter->filter($arrInput['cus_lawyer_number'])
                     );

                    $customernewid = $customer->insert($data);

                    // $attachments = new Default_Model_Attachments();
                    
                    //anh 3x4
                //     if ($_FILES['image']['name'] != '')
                //     {
                //             $imagefile = $_FILES['image']['name'];               
                //             $folder = './files/upload/anh/'.$customernewid.'/';
                //             if(!is_dir($folder)){
                //                 mkdir($folder);
                //             }else{
                //                 if(file_exists($folder.$_FILES['image']['name'])){
                //                     //unlink('./files/upload/ly-lich/' . $data['attachment_name']);
                //                     unlink($folder.$_FILES['image']['name']);
                //                 }   
                                
                //             }

                //               // Upload file                          
                //             if(move_uploaded_file($_FILES['image']['tmp_name'], $folder.$_FILES['image']['name'])){
                //                // if(mime_content_type($folder.$_FILES['image']['name']) == 'image/jpeg'){
                //                     $newName = '1_anh_'.$customernewid.'.jpg';
                //                     rename($folder.$_FILES['image']['name'],$folder.$newName);
                //                     $imagefile =  $newName;                                            
                //                 // }else{
                //                 //     $newName = '1_anh_'.$customernewid.'.png';
                //                 //     rename($folder.$_FILES['image']['name'],$folder.$newName);
                //                 //     $imagefile =  $newName;      
                //                 // }                                                                    
                //             }
                //            //move_uploaded_file($_FILES['image']['tmp_name'], './files/upload/anh/'.$_FILES['image']['name']);
                //            //$imagefile =  $_FILES["image"]["name"];
                //            $dataacttachment = array(
                //             'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                //             'cus_id' => $customernewid,
                //             'attachment_name' => $imagefile,
                //             'type' => 'anh'
                //             );
                //             $attachments->insert($dataacttachment);
                       
                //    }
   
                   //ly lich
                //    if ($_FILES['attachment']['name']){
                       
                //         $attachmentfile = $_FILES['attachment']['name'];               
                //         $folder = './files/upload/ly-lich/'.$customernewid.'/';
                //         if(!is_dir($folder)){
                //             mkdir($folder);
                //         }else{
                //             if(file_exists($folder.$_FILES['attachment']['name'])){
                //                 //unlink('./files/upload/ly-lich/' . $data['attachment_name']);
                //                 unlink($folder.$_FILES['attachment']['name']);
                //             }   
                            
                //         }
                //         if(move_uploaded_file($_FILES['attachment']['tmp_name'], $folder.$_FILES['attachment']['name'])){
                //             //if(mime_content_type($folder.$_FILES['attachment']['tmp_name']) == 'application/pdf'){
                //                 $newName = '1_lylich_'.$customernewid.'.pdf';
                //                 rename($folder.$_FILES['attachment']['name'],$folder.$newName);
                //                 $attachmentfile =  $newName;
                                
                //             //}         
                //         }      

                //         // Upload file
                //         //move_uploaded_file($_FILES['attachment']['tmp_name'], './files/upload/ly-lich/'.$_FILES['attachment']['name']);
                //         $dataacttachmentlylich = array(
                //         'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                //         'cus_id' => $customernewid,
                //         'attachment_name' => $attachmentfile,
                //         'type' => 'lylich'                

                //         );
                //         $attachments->insert($dataacttachmentlylich);
                       
                //    }

                //ly lich
                // if ($_FILES['attachment2']['name']){
    
                //     $attachmentfile = $_FILES['attachment2']['name'];               
                //     $folder = './files/upload/ly-lich/'.$customernewid.'/';
                //     if(!is_dir($folder)){
                //         mkdir($folder);
                //     }else{
                //         if(file_exists($folder.$_FILES['attachment2']['name'])){
                //             //unlink('./files/upload/ly-lich/' . $data['attachment_name']);
                //             unlink($folder.$_FILES['attachment2']['name']);
                //         }   
                        
                //     }
                //     if(move_uploaded_file($_FILES['attachment2']['tmp_name'], $folder.$_FILES['attachment2']['name'])){
                //         //if(mime_content_type($folder.$_FILES['attachment']['tmp_name']) == 'application/pdf'){
                //             $newName = '2_lylich_'.$customernewid.'.pdf';
                //             rename($folder.$_FILES['attachment2']['name'],$folder.$newName);
                //             $attachmentfile =  $newName;
                            
                //         //}         
                //     }      

                //     // Upload file
                //     //move_uploaded_file($_FILES['attachment']['tmp_name'], './files/upload/ly-lich/'.$_FILES['attachment']['name']);
                //     $dataacttachmentlylich = array(
                //     'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                //     'cus_id' => $customernewid,
                //     'attachment_name' => $attachmentfile,
                //     'type' => 'khac'                

                //     );
                //     $attachments->insert($dataacttachmentlylich);
                    
                // }
                
                    //$idcustomer = $customer->insert($data);                   
                    //$this->view->idcustomer = $idcustomer;
                    if($filter->filter($arrInput['submittype']) == 'intership'){
                        $this->view->link = 'fee/intership';
                        $this->view->linkName = 'Đi đến trang tạo phí tập sự';

                    }else if($filter->filter($arrInput['submittype']) == 'joining'){
                        $this->view->link = 'fee/joining';
                        $this->view->linkName = 'Đi đến trang tạo phí gia nhập';
                    }

                    /*insert log action*/
                    $this->auth = Zend_Auth::getInstance();
                    $this->identity = $this->auth->getIdentity();
                    $currentdate = new Zend_Date();
                    $useradminlog = new Default_Model_UserAdminLog();
                    $datalog = array(
                        'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                        'useradmin_username' => $this->identity->user_username,
                        'action' => 'Tạo mới thông tin luật sư',
                        'page' => $this->_request->getControllerName(),
                        'useradmin_id' => $this->identity->user_id,
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'access_object' => $customernewid
                    );
                    $useradminlog->insert($datalog);                    
                    //exit;
                    
                    //$datatest['id'] = $customernewid;
                    $datatest = array(
                        'id' => $customernewid,
                        'error' => '',
                        'type' => $filter->filter($arrInput['submittype'])                               
                    );
                    echo json_encode($datatest);
                    exit; 
                    
               }        
            }
        }    

    }


    public function checkinAction(){
        $categorytraining = new Default_Model_CategoryTraining();         
        $data = $categorytraining->loadCategoryTrainingActive();
        $this->view->categoriestrainings =  $data;
    }

    public function listtraineddatatableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');        

        $payment = new Default_Model_PaymentTrainingOffline();
        $data = $payment->loadPaymentLawyerOfflineByFilter($start,$length,$search);

        $results = array(            
        );

        if($data != null && sizeof($data)){
            foreach($data as $pay){ 
                $text = ''; 
                if($pay['payment_training_off_status'] == 1){
                   $text = 'Đã thanh toán';
                }else{
                   $text = 'Chưa thanh toán';  
                }  
                
                $checkin ='';
                // if($pay['checkin'] == 1){
                //     $checkin = 'Đã điểm danh';
                //  }else{
                //     $checkin = 'Chưa điểm danh';  
                //  }  

                $values  = $pay['payment_training_off_code'];
                $value = explode("_", $values);

                array_push($results,[
                $pay['payment_training_off_id'],
                $pay['cus_firstname'].' '.$pay['cus_lastname'],
                $pay['law_code'],
                $pay['law_code_createdate'],
                $pay['payment_training_off_code'],
                $pay['cat_train_name'],
                $pay['cat_train_number'],
                $checkin  
                ]);                 
            }
        }


        $totalrecords = $payment->countPaymentLawyerOfflineByFilter();

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($totalrecords)),
            "recordsFiltered" => intval(count($totalrecords)),
            "data"            => $results
        );
       //$cattraining = new Default_Model_CategoryTraining();
     
        echo json_encode($json_data);
        exit;
     }

     public function createcertificationnumberAction(){
        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                if($arrInput['id'] != null){

                    //offline
                    $paymenttraining = new Default_Model_PaymentTrainingOffline();
                    $data = $paymenttraining->loadPaymentTrainingOfflineByCategoryTrainingId($arrInput['id']);
                    if($data != null && sizeof($data)>0){
                        foreach($data as $dt){                        
                            $values  = $dt['payment_training_off_code'];
                            // $value = explode("-", $values);
                            // $dataUpdate = array('training_certification_number' => $value[0]);
                            $dataUpdate = array('training_certification_number' => $values);
                            $paymenttraining->update($dataUpdate,'payment_training_off_id = '.(int)$dt['payment_training_off_id']);                      
                        }  
                    }  
                    
                    //online
                    // $model = new Default_Model_PaymentTrainingOnline();
                    // $dataonline = $model ->loadPaymentTrainingOnlineByCategoryTrainingId($arrInput['id']);
                    // if($dataonline != null && sizeof($dataonline)>0){
                    //     foreach($dataonline as $dt){                        
                    //         $valueOnline  = $dt['payment_training_code'];
                    //         // $val = explode("_", $valueOnline);
                    //         // $dataUpdateOnline = array('training_certification_number' => $val[0]);
                    //         $dataUpdateOnline = array('training_certification_number' => $valueOnline);

                    //         $model->update($dataUpdateOnline,'payment_training_id = '.(int)$dt['payment_training_id']);                      
                    //     }    
                    // }
                }               
                //exit;
            }
        }
     }

    public function updatecheckinAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                if($arrInput['ids'] != null && sizeof($arrInput['ids'])>0){
                    foreach($arrInput['ids'] as $id){ 
                        $paymentOff = new Default_Model_PaymentTrainingOffline();
                        $data = array('checkin'=>'1');
                        $paymentOff->update($data,'payment_training_off_id = '.(int)$id);                      
                    }                   
                }               
                //exit;
            }
        }
    }
    
    public function indexAction(){
        $categoryFree = new Default_Model_CategoryFee();         
        $dataCategories = $categoryFree->loadCategoryFee('training');
        $this->view->categoryfee = $dataCategories;

        // lấy giá tiền phí thành viên năm hiện tại
        $currentDate = new Zend_Date();
        $year =  $currentDate->get('YYYY'); 
        $amountYear = 0;
        if($dataCategories != null && sizeof($dataCategories)>0){
            foreach($dataCategories as $value){
                if($value['year'] == $year ){
                    $amountYear = $value['mooney'];                        
                    break;
                }
            }
        }
        $this->view->amountYear = $amountYear;
        //$this->_helper->layout('homelayout')->disableLayout();
        $categorytraining = new Default_Model_CategoryTraining();         
        $data = $categorytraining->loadCategoryTrainingActiveAndNotStarted();
        $this->view->categoriestrainings =  $data;

        $paymenttrainingoffline = new Default_Model_PaymentTrainingOffline();
        $payment_training_off_code = $paymenttrainingoffline->generationCode('BDĐT','');
        $this->view->payment_training_off_code = $payment_training_off_code;
    }


    public function listAction(){
        $paymenttrainingoffline = new Default_Model_PaymentTrainingOffline();   
        $data = $paymenttrainingoffline->loadPaymentLawyerOffline();
        $this->view->paymentofflines =  $data; 
    }

    public function listdataAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymenttrainingoffline = new Default_Model_PaymentTrainingOffline();      
        $cus_id = $this->getRequest()->getParam('cus_id');
        $data = $paymenttrainingoffline->loadPaymentTrainingOfflineByCusId($cus_id);

         $dateEmpty = '1900-01-01 00:00:00';
         array_walk ( $data, function (&$key) {
             $key["payment_training_off_created_date"] = ($key["payment_training_off_created_date"] != null && $key["payment_training_off_created_date"] != '' && $key["payment_training_off_created_date"] != '1900-01-01 00:00:00'
             ) ? date('d/m/Y',strtotime($key["payment_training_off_created_date"])) : '';
             $key["payment_training_off_code"] = ($key["payment_training_off_code"] != '' && $key["payment_training_off_code"] != null) ? $key["payment_training_off_code"] : '';
             $key["name"] = ($key["name"] != '' && $key["name"] != null) ? $key["name"] : '';
            // $key["intership_address"] = ($key["intership_address"] != '' && $key["intership_address"] != null) ? $key["intership_address"] : '';
         } );
         echo json_encode($data);
         exit; 
    }

    /*load history fee training by customer id*/
    public function listtrainingAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymentoffline = new Default_Model_PaymentTrainingOffline();   
        $cus_id = $this->getRequest()->getParam('cus_id');
        $data = $paymentoffline->loadPaymentTrainingOfflineByCusId($cus_id);
        $this->view->paymentofflines =  $data; 
    }


    public function certificationAction(){
        // $paymenttrainingoffline = new Default_Model_PaymentTrainingOffline();   
        // $data = $paymenttrainingoffline->loadPaymentLawyerOfflineToCreateCertification();
        // $this->view->paymentofflines =  $data; 
        $categorytraining = new Default_Model_CategoryTraining();         
        $data = $categorytraining->loadCategoryTrainingActive();
        $this->view->categoriestrainings =  $data;
    }

    public function listtrainedcertificationdatatableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');        

        $payment = new Default_Model_PaymentTrainingOffline();

        //create training certification number

        if($search != null){
            //offline
            $paymenttraining = new Default_Model_PaymentTrainingOffline();
            $data = $paymenttraining->loadPaymentTrainingOfflineByCategoryTrainingId($search);
            if($data != null && sizeof($data)>0){
                $index = 1;                
                foreach($data as $dt){ 
                    $text = '';       
                    $lengthindex = strlen($index);
                    if($lengthindex == 1){
                        $text = '000'.$index;
                    }else if($lengthindex == 2){
                        $text = '00'.$index;    
                    }else if($lengthindex == 3){
                        $text = '0'.$index;
                    }else if($lengthindex == 4){
                        $text = $index;
                    }else{
                        $text = $index;
                    }                     
                    //$values  = $dt['payment_training_off_code'];                   
                    $dataUpdate = array('training_certification_number' => $text);
                    $paymenttraining->update($dataUpdate,'payment_training_off_id = '.(int)$dt['payment_training_off_id']);                      
                    $index += 1;
                }  
            } 
        }



        $data = $payment->loadLawyerOfflineToCreatingCertificationByFilter($start,$length,$search);

        $results = array(            
        );
        $dateEmpty = '1900-01-01 00:00:00';
        if($data != null && sizeof($data)){
            foreach($data as $pay){ 
                $text = ''; 
                if($pay['payment_training_off_status'] == 1){
                   $text = 'Đã thanh toán';
                }else{
                   $text = 'Chưa thanh toán';  
                }  
                
                // $checkin ='';
                // if($pay['checkin'] == 1){
                //     $checkin = 'Đã điểm danh';
                //  }else{
                //     $checkin = 'Chưa điểm danh';  
                //  }  

                $values  = $pay['payment_training_off_code'];
                $value = explode("_", $values);

                array_push($results,[
                // $pay['payment_training_off_id'],
                $pay['cus_firstname'].' '.$pay['cus_lastname'],
                $pay['cus_lawyer_number'],
                ($pay['cus_date_lawyer_number'] != null && $pay['cus_date_lawyer_number'] != '' && 
                $pay['cus_date_lawyer_number'] != $dateEmpty) ? date('d/m/Y', strtotime($pay['cus_date_lawyer_number'])) : '',
                $pay['cus_address_resident'],
                $pay['cus_address_resident_now'],
                $pay['payment_training_off_code'],
                $pay['cat_train_name'],
                $pay['cat_train_number'],
                //$checkin,
                $pay['training_certification_number']
                ]);                 
            }
        }

        //$totalrecords = $payment->countLawyerOfflineToCreatingCertificationByFilter();

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($results)),
            "recordsFiltered" => intval(count($results)),
            "data"            => $results
        );
       //$cattraining = new Default_Model_CategoryTraining();
     
        echo json_encode($json_data);
        exit;
     }

    public function detailAction(){   
        $this->_helper->layout('homelayout')->disableLayout();
        $paymenttrainingoffline = new Default_Model_PaymentTrainingOffline();   
        $paymenttraining_id = $this->getRequest()->getParam('payment_training_off_id');
        $data = $paymenttrainingoffline->loadPaymentTrainingOfflineById($paymenttraining_id);
        $this->view->paymenttraningofflinedata =  $data; 

        $lawyer = new Default_Model_Lawyer();
        $data = $lawyer->loadLawyer();
        $this->view->lawyers = $data;
    }

    /** 
     * deleted phieu boi duong -- update status from 1  to 0
    */
    public function deleteAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $arrInput = $this->_request->getParams();
            
            if($this->view->parError == ''){
                $date = new Zend_Date();
                $payment = new Default_Model_PaymentTrainingOffline();
                
                $data = array(                 
                    'payment_training_off_status'=> 0,
                    'payment_training_off_updatedate' =>  $date->toString('YYYY-MM-dd HH:mm:ss')                   
                );
                
                $payment->update($data, 'payment_training_off_id = '. (int)($filter->filter($arrInput['payment_training_off_id'])));                     
            }        
        }    

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
                    $date = new Zend_Date();
                    $payment = new Default_Model_PaymentTrainingOffline();

                    if($filter->filter($arrInput['action_type']) == 'update'){
                        $data = array(                 
                            //'payment_training_off_status'=> $filter->filter($arrInput['payment_training_off_status']),
                            'cus_id'=> $filter->filter($arrInput['cus_id']),
                            'payment_training_off_updatedate' =>  $date->toString('YYYY-MM-dd HH:mm:ss')                   
                        );
                        
                        $payment->update($data, 'payment_training_off_id = '. (int)($filter->filter($arrInput['payment_training_off_id'])));                     
                    }else{
                        $data = array(                 
                            //'payment_training_off_status'=> $filter->filter($arrInput['payment_training_off_status']), 
                            'payment_training_off_updatedate' =>  $date->toString('YYYY-MM-dd HH:mm:ss')   
                                          
                        );                       
                        $payment->update($data, 'payment_training_off_id = '. (int)($filter->filter($arrInput['payment_training_off_id'])));                     
                    }                   
                }        
            }
        }    

    }


    public function createAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                //$request = $this->getRequest();
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;
                $paymenttraining = new Default_Model_PaymentTrainingOffline();    

                if(!Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                    $this->view->parError = 'Bạn phải chọn luật sư để tạo phí bồi dưỡng!';
                }
    
                if(!Zend_Validate::is($arrInput['category_training_id'],'NotEmpty')){
                    $this->view->parError = 'Bạn phải chọn khóa bồi dưỡng';
                }          

                // finc paymenttraining with cus_id and catid . if cus has registerd cat, it would rejected
                if(Zend_Validate::is($arrInput['cus_id'],'NotEmpty') && Zend_Validate::is($arrInput['category_training_id'],'NotEmpty')){
                   $dataPaymentOfCus = $paymenttraining->loadTrainingPaymentOfflineByCusIdAndCatTrainingId($arrInput['cus_id'],$arrInput['category_training_id']); 
                   if($dataPaymentOfCus != null && sizeof($dataPaymentOfCus) >0 ){
                        $this->view->parError = 'Bạn đã đăng kí và thanh toán cho khóa bồi dưỡng rồi. Không đăng kí tiếp được nữa.';
                   }
                }

                if(Zend_Validate::is($arrInput['amount'],'NotEmpty')){                    
                    if($arrInput['amount'] == 0 && $arrInput['age'] == 0){
                        $this->view->parError = 'Bạn không cần phải tạo phí bồi dưỡng, bạn được miễn phí!';
                    }                                   
                }  
                

                // if(Zend_Validate::is($arrInput['age'],'NotEmpty')){
                //     $namhanhnghe = (int)date('Y') - (int)date('Y',strtotime($filter->filter($arrInput['law_certification_createdate']))); 
                //     echo "<prev>";
                //         echo $namhanhnghe;
                //     echo "</prev>";
                //     exit;
                //     if($namhanhnghe >= 2){
                //         $this->view->parError = 'Bạn đã đủ tuổi và hành nghề trên 20 bạn không cần phải tạo biên nhận bồi dưỡng!';
                //     }                                     
                // }
                 
               if($this->view->parError == ''){
                    $date = new Zend_Date();
                    // $billoffline = new Default_Model_BillTrainingOffline();
                                    
                    // $data = array(              
                    //     'category_fee_training_id' => $filter->filter($arrInput['category_fee_training_id']),
                    //     'category_training_id' => $filter->filter($arrInput['category_training_id']),
                    //     'cus_id' => $filter->filter($arrInput['cus_id']),
                    //     'createddate' => $date->toString('YYYY-MM-dd HH:mm:ss')
                    //  );

                    // $billoffline_id = $billoffline->insert($data);

                                     
                    
                    $data_paymenttraining = array(
                        'payment_training_off_code' => $filter->filter($arrInput['payment_training_off_code']),
                        'payment_training_off_status' => '1',
                        'payment_training_off_created_date' => $date->toString('YYYY-MM-dd HH:mm:ss'),
                        'amount' =>  $filter->filter($arrInput['amount']),
                        'payment_type' => 'offline',
                        'category_training_id' => $filter->filter($arrInput['category_training_id']),
                        'category_fee_id' => $filter->filter($arrInput['category_fee_id']),
                        'cus_id' => $filter->filter($arrInput['cus_id'])              

                    );
                    $paymenttraining ->insert($data_paymenttraining);  
                                      
                 }                   
             }
         }    
    }

    public function printtrainingpaymentAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymentlawyertrainingoffline = new Default_Model_PaymentTrainingOffline();   
        $payment_training_off_id = $this->getRequest()->getParam('payment_training_off_id');
        $data = $paymentlawyertrainingoffline->loadPaymentTrainingOfflineById($payment_training_off_id);
        $valueWords = $this->convertNumberToWords( $data["amount"]);
        $data["amount_word"] = $valueWords; // works
        $this->view->paymentlawyertrainingofflinedata =  $data; 
    }

    /* function convert từ số sang chữ*/
    private function convertNumberToWords($number) {

        $hyphen      = ' ';
        $conjunction = '  ';
        $separator   = ' ';
        $negative    = 'âm ';
        $decimal     = ' phẩy ';
        $dictionary  = array(
        0                   => 'Không',
        1                   => 'Một',
        2                   => 'Hai',
        3                   => 'Ba',
        4                   => 'Bốn',
        5                   => 'Năm',
        6                   => 'Sáu',
        7                   => 'Bảy',
        8                   => 'Tám',
        9                   => 'Chín',
        10                  => 'Mười',
        11                  => 'Mười một',
        12                  => 'Mười hai',
        13                  => 'Mười ba',
        14                  => 'Mười bốn',
        15                  => 'Mười năm',
        16                  => 'Mười sáu',
        17                  => 'Mười bảy',
        18                  => 'Mười tám',
        19                  => 'Mười chín',
        20                  => 'Hai mươi',
        30                  => 'Ba mươi',
        40                  => 'Bốn mươi',
        50                  => 'Năm mươi',
        60                  => 'Sáu mươi',
        70                  => 'Bảy mươi',
        80                  => 'Tám mươi',
        90                  => 'Chín mươi',
        100                 => 'trăm',
        1000                => 'ngàn',
        1000000             => 'triệu',
        1000000000          => 'tỷ',
        1000000000000       => 'nghìn tỷ',
        1000000000000000    => 'ngàn triệu triệu',
        1000000000000000000 => 'tỷ tỷ'
        );
            
        if (!is_numeric($number)) {
        return false;
        }
            
        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
        'convertNumberToWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
        E_USER_WARNING
        );
        return false;
        }
            
        if ($number < 0) {
        return $negative . $this->convertNumberToWords(abs($number));
        }
            
        $string = $fraction = null;
            
        if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
        }
            
        switch (true) {
        case $number < 21:
        $string = $dictionary[$number];
        break;
        case $number < 100:
        $tens   = ((int) ($number / 10)) * 10;
        $units  = $number % 10;
        $string = $dictionary[$tens];
        if ($units) {
        $string .= $hyphen . $dictionary[$units];
        }
        break;
        case $number < 1000:
        $hundreds  = $number / 100;
        $remainder = $number % 100;
        $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
        if ($remainder) {
        $string .= $conjunction . $this->convertNumberToWords($remainder);
        }
        break;
        default:
        $baseUnit = pow(1000, floor(log($number, 1000)));
        $numBaseUnits = (int) ($number / $baseUnit);
        $remainder = $number % $baseUnit;
        $string = $this->convertNumberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
        if ($remainder) {
        $string .= $remainder < 100 ? $conjunction : $separator;
        $string .= $this->convertNumberToWords($remainder);
        }
        break;
        }
            
        if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
        $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
        }
            
        return $string;
    }

    public function printtraningcattrainingAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $model = new Default_Model_PaymentTrainingOffline();   
        $category_training_id = $this->getRequest()->getParam('category_training_id');
        $datas = $model->loadTrainingOfflineToCreateCertificationByCatTrainingId($category_training_id);
        $results = array();

        //$this->view->certificationsTinh = '';
        if($datas != null && sizeof($datas) > 0){
            foreach($datas as $data){ 
                $tinhthanh = '';
                if($data['cus_member'] != 1){
                    $modeCity = new Default_Model_City();
                    if($data['cus_lawyer_cityid'] != null && $data['cus_lawyer_cityid'] != 0){
                        $dataTinh = $modeCity ->loadCityById($data['cus_lawyer_cityid']); 
                        $tinhthanh = $dataTinh['city_name'];                     
                    }           
                }
                $data['tinhthanh'] = $tinhthanh;
                array_push($results,$data);
            }            
        }

        $this->view->certifications =  $results;    
        
        // echo "<prev>";
        //     echo print_r($results);
        // echo "</prev>";
        // exit;
    }

}