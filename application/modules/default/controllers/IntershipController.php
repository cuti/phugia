<?php
// Intership controller
class IntershipController extends Zend_Controller_Action
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



    public function detailAction(){
        // $this->_helper->layout('layout')->disableLayout();
        // $intershipNumber = new Default_Model_Intership();
        // $intership_number_id = $this->getRequest()->getParam('inter_number_id');
        // $data = $intershipNumber->loadIntershipNumberById($intership_number_id);
        // $this->view->intershipnumberdata = $data;

        $this->_helper->layout('layout')->disableLayout();
        $intership = new Default_Model_Intership();
        $intership_id = $this->getRequest()->getParam('inter_id');
        $data = $intership->loadIntershipByInterId($intership_id);
        $this->view->intershipnumberdata = $data;

    }

    public function approveintershipnumberAction(){
        $this->_helper->layout('layout')->disableLayout();


        $filter = new Zend_Filter();
        $arrInput = $this->_request->getParams();
        $this->view->arrInput = $arrInput;

        $currentdate = new Zend_Date();
        //if($this->view->parError == ''){
        $intership = new Default_Model_IntershipNumber();
        $data = array(
            'intership_number_enddate'=> $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
            'intership_number_status' => '1'
        );
        //$this->view->data = $data;

        $intership->update($data, 'intership_number_id = '. (int)($filter->filter($arrInput['inter_number_id'])));

    }


    // public function updateAction(){
    //     $this->_helper->layout('layout')->disableLayout();

    //     $filter = new Zend_Filter();
    //     if ($this->getRequest()->isXmlHttpRequest()) {
    //         if ($this->getRequest()->isPost()) {

    //             $arrInput = $this->_request->getParams();
    //             $this->view->arrInput = $arrInput;

    //            if($this->view->parError == ''){
    //                 $intership = new Default_Model_Intership();
    //                 $data = array(
    //                     'intership_number_id'=> $filter->filter($arrInput['intership_number_id'])
    //                 );
    //                 //$this->view->data = $data;
    //                 $intership->update($data, 'inter_id = '. (int)($filter->filter($arrInput['inter_id'])));
    //             }
    //         }
    //     }

    // }

    public function updateAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

               //if($this->view->parError == ''){
                    $intership = new Default_Model_Intership();
                    $data = array(
                        'inter_number_name'=> $filter->filter($arrInput['inter_number_name']),
                        'payment_inter_status'=> $filter->filter($arrInput['payment_inter_status']),
                        'inter_note' => $filter->filter($arrInput['inter_note'])

                    );
                    //$this->view->data = $data;
                    $intership->update($data, 'inter_id = '. (int)($filter->filter($arrInput['inter_id'])));
                //}
            }
        }

    }

    public function listAction(){
        $this->_helper->layout('homelayout')->disableLayout();
         $intership = new Default_Model_Intership();
         $cus_id = $this->getRequest()->getParam('cus_id');
         $data = $intership->loadIntershipByCusId($cus_id);
         $this->view->interships =  $data;

         $lawyer = new Default_Model_Lawyer();
         $dataLawyer = $lawyer->loadLawyerByCusId($cus_id);
         if($dataLawyer != null ){
            if(sizeof($dataLawyer) > 0 ){
                $this->view->lawyerId =  '1';
            }
         }
         $this->view->lawyerId =  '';

    }

    // hiển thị thông tin tập sự ở mục profile index
    public function listdataAction(){
        $this->_helper->layout('homelayout')->disableLayout();
         $intership = new Default_Model_Intership();
         $cus_id = $this->getRequest()->getParam('cus_id');
         $data = $intership->loadIntershipByCusId($cus_id);

         $dateEmpty = '1900-01-01 00:00:00';
         array_walk ( $data, function (&$key) {
             $key["inter_regis_date"] = ($key["inter_regis_date"] != null && $key["inter_regis_date"] != '' && $key["inter_regis_date"] != '1900-01-01 00:00:00'
             ) ? date('d/m/Y',strtotime($key["inter_regis_date"])) : '';
             $key["organ_name"] = ($key["organ_name"] != '' && $key["organ_name"] != null) ? $key["organ_name"] : '';
             $key["duration"] = ($key["duration"] != '' && $key["duration"] != null) ? $key["duration"] : '';
             $key["intership_address"] = ($key["intership_address"] != '' && $key["intership_address"] != null) ? $key["intership_address"] : '';
         } );
         echo json_encode($data);
         exit;
    }

    public function indexAction(){
        $intershipnumber = new Default_Model_IntershipNumber();
        $this->view->intershipnumberdata = $intershipnumber->loadInterhipNumber();
    }

    public function addAction(){
        $cus_id = $this->getRequest()->getParam('cus_id');

        if($cus_id != null){
            $customer = new Default_Model_Customer();
            $customerdata = $customer->getCustomerByUserId($cus_id);
            $dateEmpty = '1900-01-01 00:00:00';
            $customerdata['cus_birthday'] =  ($customerdata['cus_birthday'] != null && $customerdata['cus_birthday'] != '' &&
            $customerdata['cus_birthday'] != $dateEmpty) ? date('d/m/Y', strtotime($customerdata['cus_birthday'])) : '';
            $this->view->customerdata = $customerdata;
        }

        $this->view->cus_id = $cus_id;


        //load list lawyer
        $lawyer = new Default_Model_Lawyer();
        $data = $lawyer->loadLawyer();
        $this->view->lawyers = $data;

        $intershipnum = new Default_Model_IntershipNumber();
        $data = $intershipnum->loadInterhipNumber();
        $this->view->internum =  $data;

        //load ds to chuc hanh nghe luat su
        $model = new Default_Model_OrganizationLawDetails();
        $dataOrganization = $model->loadOrganzationsLaw();
        $this->view->organizations = $dataOrganization;

    }

    public function addintershipnumberAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                $currentdate = new Zend_Date();

                if($this->view->parError == ''){
                    $intershipnumber = new Default_Model_IntershipNumber();
                    $data = array(
                        'intership_number_startdate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'intership_number_enddate' => $filter->filter($arrInput['intership_number_enddate']),
                        'intership_number_createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'intership_number_name' => $filter->filter($arrInput['intership_number_name']),
                        'intership_number_status' => '0'

                    );

                    $intershipnumber->insert($data);

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
                //$request = $this->getRequest();
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if(!Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                    $this->view->parError = 'Bạn phải chọn luật sư để tạo luật sư tập sự! <br>';
                }

                if(!Zend_Validate::is($arrInput['law_id'],'NotEmpty')){
                    $this->view->parError = 'Bạn phải người hướng dẫn cho luật sư tập sự! <br>';
                }

                if(Zend_Validate::is($arrInput['cus_member'],'NotEmpty')){
                    if($arrInput['cus_member'] == 1){
                        $this->view->parError = 'Bạn không thể thêm mới tập sự, bạn đã là thành viên của Đoàn.! <br>';
                    }
                }
                // if(Zend_Validate::is($arrInput['law_id'],'NotEmpty') && Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                //     if($arrInput['law_id'] == $arrInput['cus_id']){
                //         $this->view->parError = 'Bạn không thể hướng dẫn lý chính bạn! <br>';
                //     }
                // }
                if(Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                    $cus_id = $arrInput['cus_id'];
                    $db = Zend_Db_Table::getDefaultAdapter();
                    $select = new Zend_Db_Select($db);
                    $select->from('intership', array('inter_id','cus_id','inter_number_name','payment_inter_status'))
                    ->where('intership.cus_id = ?',$cus_id)
                    ->where('intership.payment_inter_status = ?',0)
                    ->order('inter_id desc')
                    ->limit(1);

                    $resultSet = $db->fetchRow($select);

                    if($resultSet != null && $resultSet['payment_inter_status'] != null) {
                        //if(trim($resultSet['inter_number_name']) == trim($filter->filter($arrInput['inter_number_name']))){
                            $this->view->parError = 'Hiện tại bạn đã được đăng kí tập sự trong đợt tập sự, bạn không thể đăng kí thêm được. Bạn phải thanh toán cho đợt tập sự hoặc bỏ đợt tập sự';
                        //}

                    }

                    // if($resultSet['intership_number_enddate'] != null && (strtotime($resultSet['intership_number_enddate']) + 60 > time())) {
                    //     $this->view->parError = 'Hiện tại đợt tập sự của bạn chưa kết thúc.';
                    // }
                }

                if(Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                    $cus_id = $arrInput['cus_id'];
                    $db = Zend_Db_Table::getDefaultAdapter();
                    $select = new Zend_Db_Select($db);
                    $select->from('intership', array('inter_id','cus_id','inter_number_name'))
                    ->where('intership.cus_id = ?',$cus_id)
                    ->order('inter_id desc')
                    ->limit(1);

                    $resultSet = $db->fetchRow($select);

                    if($resultSet != null && $resultSet['inter_number_name'] != null && $resultSet['inter_number_name'] != '') {
                        if(trim($resultSet['inter_number_name']) == trim($filter->filter($arrInput['inter_number_name']))){
                            $this->view->parError = 'Hiện tại bạn đã được đăng kí tập sự trong đợt tập sự, bạn không thể đăng kí thêm được.';
                        }

                    }

                    // if($resultSet['intership_number_enddate'] != null && (strtotime($resultSet['intership_number_enddate']) + 60 > time())) {
                    //     $this->view->parError = 'Hiện tại đợt tập sự của bạn chưa kết thúc.';
                    // }
                }

               if($this->view->parError == ''){
                    $date = new Zend_Date();
                    $intership = new Default_Model_Intership();

                    // $mil = $filter->filter($arrInput['inter_regis_date']);
                    // $seconds = $mil / 1000;
                    // $time_regis_date =  date("Y-m-d H:m:s", $seconds);

                    //format 2018-10-20 18:59:00 YYYY-MM-dd HH:mm:ss
                    $data = array(
                        // 'inter_code' => $intership->generationCode('PTS','OFFLINE'),
                        //'inter_code' => 'testcode',
                        'inter_regis_date' => $date->toString('YYYY-MM-dd HH:mm:ss'),
                        'inter_created_date' => $date->toString('YYYY-MM-dd HH:mm:ss'),
                        'cus_id' => $filter->filter($arrInput['cus_id']),
                        //'intership_number_id' => $filter->filter($arrInput['intership_number_id']),
                        'duration' =>  $filter->filter($arrInput['duration']),
                        'intership_address' =>  $filter->filter($arrInput['intership_address']),
                        'inter_number_name'  =>   $filter->filter($arrInput['inter_number_name']),
                        'payment_inter_status'  =>  0

                     );

                    $idintership = $intership->insert($data);

                    $guidelaw = new Default_Model_GuideLaw();

                    $data_guidelaw = array(
                        'law_id'=> $filter->filter($arrInput['law_id']),
                        'inter_id'=> $idintership,
                    );

                    $guidelaw->insert($data_guidelaw);

                    //update type
                    $modelCustomer = new Default_Model_Customer();
                    $dataCustomers = array(
                        'cus_type'=> 0
                    );

                    $modelCustomer->update($dataCustomers,'cus_id = '. (int)($filter->filter($arrInput['cus_id'])));
                 }
             }
         }
    }


}
