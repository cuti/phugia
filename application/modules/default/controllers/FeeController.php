<?php
// Fee controller
class FeeController extends Zend_Controller_Action
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
        //$this->_helper->layout('homelayout')->disableLayout();
    }

    /* fee membership*/
    public function memberAction(){

        $paymentoffline = new Default_Model_PaymentLawyerOffline();
        $payment_lawyer_off_code = $paymentoffline->generationCode('PTV','');
        $this->view->payment_lawyer_off_code = $payment_lawyer_off_code;


        $categoryFree = new Default_Model_CategoryFee();
        $data = $categoryFree->loadCategoryFee('member');
        $currentDate = new Zend_Date();
        $year =  $currentDate->get('YYYY');

        // lấy giá tiền phí thành viên năm hiện tại
        $amountYear = 0;
        if($data != null && sizeof($data)>0){
            foreach($data as $value){
                if($value['year'] == $year ){
                    $amountYear = $value['mooney'];
                    break;
                }
            }
        }
        $this->view->amountYear = $amountYear;
        $this->view->categoryfee =  $data;

    }

    /*fee internship*/
    public function intershipAction(){

        $categoryFree = new Default_Model_CategoryFee();
        $data = $categoryFree->loadCategoryFee('intership');
        $currentDate = new Zend_Date();
        $year =  $currentDate->get('YYYY');

        // lấy giá tiền phí thành viên năm hiện tại
        $amountYear = 0;
        if($data != null && sizeof($data)>0){
            foreach($data as $value){
                if($value['year'] == $year ){
                    $amountYear = $value['mooney'];
                    break;
                }
            }
        }
        $this->view->amountYear = $amountYear;
        $this->view->categoryfee =  $data;

        $intershipnumber = new Default_Model_IntershipNumber();
        $data = $intershipnumber->loadIntershipNumberActive();
        $this->view->intershipdata = $data;


        $paymentintershipoffline = new Default_Model_PaymentIntershipOffline();
        $payment_inter_off_code = $paymentintershipoffline->generationCode('PTS','');
        $this->view->payment_inter_off_code = $payment_inter_off_code;

    }

    public function joiningAction(){
        $categoryfreelawyer = new Default_Model_CategoryFee();
        $data = $categoryfreelawyer->loadCategoryFee('joining');
        $this->view->categoryfee =  $data;

        $paymentoffline = $paymentoffline = new Default_Model_PaymentJoiningOffline();
        $this->view->paymentjoiningoffcode = $paymentoffline->generationCode('PGN','');

        $joining = new Default_Model_Joining();
        $data_joining = $joining-> loadJoiningActive();
        $this->view->joiningdata = $data_joining;
    }

    /*load history fee by customer id*/
    public function listAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymentoffline = new Default_Model_PaymentLawyerOffline();
        $cus_id = $this->getRequest()->getParam('cus_id');
        $data = $paymentoffline->loadPaymentLawyerOfflineByCusId($cus_id);
        $this->view->paymentofflines =  $data;
    }

    public function listdataAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymentoffline = new Default_Model_PaymentLawyerOffline();
        $cus_id = $this->getRequest()->getParam('cus_id');
        $data = $paymentoffline->loadPaymentLawyerOfflineByCusId($cus_id);
        //$this->view->paymentofflines =  $data;

        //$dateEmpty = '1900-01-01 00:00:00';
        array_walk ( $data, function (&$key) {
            $key["payment_lawyer_off_created_date"] = ($key["payment_lawyer_off_created_date"] != null && $key["payment_lawyer_off_created_date"] != '' && $key["payment_lawyer_off_created_date"] != '1900-01-01 00:00:00'
            ) ? date('d/m/Y',strtotime($key["payment_lawyer_off_created_date"])) : '';
            //$key["payment_training_off_code"] = ($key["payment_training_off_code"] != '' && $key["payment_training_off_code"] != null) ? $key["payment_training_off_code"] : '';
            //$key["name"] = ($key["name"] != '' && $key["name"] != null) ? $key["name"] : '';
           // $key["intership_address"] = ($key["intership_address"] != '' && $key["intership_address"] != null) ? $key["intership_address"] : '';
        } );
        echo json_encode($data);
        exit;
    }

      /*load history fee intership by customer id*/
    public function listintershipAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymentoffline = new Default_Model_PaymentIntershipOffline();
        $cus_id = $this->getRequest()->getParam('cus_id');
        $data = $paymentoffline->loadPaymentIntershipOfflineByCusId($cus_id);
        $this->view->paymentofflines =  $data;
    }

      /*load history fee intership by customer id*/
    public function listjoiningAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymentoffline = new Default_Model_PaymentJoiningOffline();
        $cus_id = $this->getRequest()->getParam('cus_id');
        $data = $paymentoffline->loadPaymentJoiningOfflineByCusId($cus_id);
        $this->view->paymentofflines =  $data;
    }

    /*load list history fee*/
    public function confirmfeelistAction(){
        //$this->_helper->layout('homelayout')->disableLayout();
        $paymentoffline = new Default_Model_PaymentLawyerOffline();
        $datapaymentofflines = $paymentoffline->loadPaymentLawyerOffline();
        $this->view->paymentofflines =  $datapaymentofflines;

        $paymentjoiningoffline = new Default_Model_PaymentJoiningOffline();
        $datapaymentjoiningofflines = $paymentjoiningoffline->loadPaymentJoiningOffline();
        $this->view->paymentjoiningofflines =  $datapaymentjoiningofflines;

        $paymentintershipoffline = new Default_Model_PaymentIntershipOffline();
        $datapaymentintershipofflines = $paymentintershipoffline->loadPaymentIntershipOffline();
        $this->view->paymentintershipofflines =  $datapaymentintershipofflines;

        $modelPaymentOffline = new Default_Model_PaymentOffline();
        $paymentOffline = $modelPaymentOffline->loadPaymentOffline();
        $this->view->paymentOfflines =  $paymentOffline;
    }

    public function detailconfirmfeeAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymentoffline = new Default_Model_PaymentLawyerOffline();
        $id = $this->getRequest()->getParam('id');
        $data = $paymentoffline->loadPaymentLawyerOfflinegById($id);
        $this->view->paymentofflinedata =  $data;
    }

     /*create fee memberr*/
     public function createfeejoiningAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        $history_id = null;
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                //$request = $this->getRequest();
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if(!Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                    $this->view->parError = 'Bạn phải chọn luật sư để tạo phí gia nhập!';
                }

                if(Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                    // $model = new Default_Model_Lawyer();
                    // $datalawyer = $model->loadLawyerByCusId($arrInput['cus_id']);
                    // if(sizeof($datalawyer) == 0){
                    //     $this->view->parError = 'Bạn chưa được thêm mới gia nhập vào Đoàn nên không thể đóng phí gia nhập!';
                    // }

                    //if(Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                        $cus_id = $arrInput['cus_id'];

                        $modelCustomers = new Default_Model_Customer();

                        $dataCustomer = $modelCustomers->getCustomerByUserId($cus_id);

                        // if($dataCustomer != null && $dataCustomer['cus_lawyer_cityid'] == 1 ){
                        //     $this->view->parError = 'Bạn là luật sư ở tỉnh, bạn không thể đóng phí tập sự' ;
                        // }

                        if($dataCustomer != null )
                            $db = Zend_Db_Table::getDefaultAdapter();
                            $select = new Zend_Db_Select($db);
                            $select->from('history_joining', array('history_joining_id','cus_id','payment_joining_status'))
                            ->where('history_joining.cus_id = ?',$cus_id)
                            ->where('history_joining.payment_joining_status = ?',0)
                            ->order('history_joining_id desc')
                            ->limit(1);

                            $resultSet = $db->fetchRow($select);

                            if($resultSet != null){
                                if($resultSet['payment_joining_status'] == 1){
                                    $this->view->parError = 'Hiện tại đợt gia nhập của bạn đã được đóng phí' ;
                                }else{
                                    $history_id = $resultSet['history_joining_id'];
                                }
                            }else{
                                $this->view->parError = 'Bạn không thể đóng phí gia nhập khi chưa được thêm vào gia nhập mới. Bạn phải thêm vào đợt tập sự, trước khi đóng phí.' ;
                            }

                        }
                    //}

                }

                // if(Zend_Validate::is($arrInput['cus_member'],'NotEmpty')){
                //    if($arrInput['cus_member'] == 1){
                //     $this->view->parError = 'Bạn đã là thành viên của Đoàn, bạn không cần gia nhập!';
                //    }
                // }

                if($this->view->parError == ''){
                    $date = new Zend_Date();
                    // bỏ đi bảng law_num và bảng billfeeoffline không dùng nữa
                    $paymentoffline = new Default_Model_PaymentJoiningOffline();

                    $data_paymentoffline = array(
                        'payment_joining_off_code' => $filter->filter($arrInput['payment_joining_off_code']),
                        'payment_joining_off_status' => '0',
                        'payment_joining_off_created_date' => $date->toString('YYYY-MM-dd HH:mm:ss'),
                        'amount' => $filter->filter($arrInput['amount']),
                        //'amount' =>  ereg_replace("[^0-9]", "",$filter->filter($arrInput['amount'])),
                        'category_fee_id' => $filter->filter($arrInput['category_fee_id']),
                        'cus_id' => $filter->filter($arrInput['cus_id']),
                        'payment_type' => 'offline',
                        'history_id' => $history_id

                    );
                    $idpaymentjoining = $paymentoffline ->insert($data_paymentoffline);

                     /*insert log action*/
                     $this->auth = Zend_Auth::getInstance();
                     $this->identity = $this->auth->getIdentity();
                     $currentdate = new Zend_Date();
                     $useradminlog = new Default_Model_UserAdminLog();
                     $datalog = array(
                         'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                         'useradmin_username' => $this->identity->user_username,
                         'action' => 'Tạo biên nhận phí gia nhập',
                         'page' => $this->_request->getControllerName(),
                         'useradmin_id' => $this->identity->user_id,
                         'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                         'access_object' => $idpaymentjoining
                     );
                     $useradminlog->insert($datalog);

                 }
             }

    }


    // public function testAction(){
    //     $cus_id = 1;
    //     $db = Zend_Db_Table::getDefaultAdapter();
    //     $select = new Zend_Db_Select($db);

    //     $query2 = $select->from('payment_intership',array('payment_inter_id'))
    //     ->joinInner(
    //         'bill_fee_lawyer_temp',
    //         'bill_fee_lawyer_temp.bill_feeoffline_id = payment_intership.bill_feelawyer_temp_id',
    //         array())
    //     ->where('bill_fee_lawyer_temp.cus_id = ?', $cus_id);

    //     $query1 = $select->from('payment_intership_offline',array('payment_inter_off_id'))
    //     ->joinInner(
    //         'bill_fee_offline',
    //         'bill_fee_offline.bill_feeoffline_id = payment_intership_offline.bill_feeoffline_id',
    //         array())
    //     ->where('bill_fee_offline.cus_id = ?', $cus_id);
    //     //union($query2);


    //     echo $query1;

    //     exit;
    // }

    /*create fee memberr*/
    public function createAction(){
        $this->_helper->layout('layout')->disableLayout();

        $dateEmpty = '1900-01-01 00:00:00';
        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                //$request = $this->getRequest();
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                //check lại chỗ này phí thành viên chỉ có luật sư ở đoàn mới đóng thôi
                // người dùng phải chọn customer
                if(!Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                    $this->view->parError = 'Bạn phải chọn luật sư để tạo phí thành viên!';
                }

                // check xem customer người dùng chọn có phải là ls của đoàn
                if(Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                    if(Zend_Validate::is($arrInput['cus_member'],'NotEmpty') && $arrInput['cus_member'] != 1  ){
                        $this->view->parError = 'Bạn không phải là luật sư của đoàn!';
                    }else{
                        if(Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                            if($arrInput['cus_age_year'] == 1){
                                $this->view->parError = 'Bạn đã trên 75 tuổi. Bạn không cần phải đóng phí thành viên!';
                            }
                        }
                         // check xem nếu lơn hơn 75 thì không cần nộp phí
                         // check cho neu no tien thi van phai dong tien
                        // if(Zend_Validate::is($arrInput['cus_age'],'NotEmpty')){
                        //     if($arrInput['cus_age'] >= 75){
                        //         $modelCustomers = new Default_Model_Customer();
                        //         $endmonth = $modelCustomers->getEndMonthByCusId($arrInput['cus_id']);
                        //         if($endmonth != null && $endmonth != $dateEmpty && $endmonth != '' ){
                        //             //$currentdate = new Zend_Date();
                        //             $endyear = $arrInput['cus_age_year'] + 75;
                        //             //$month = date('n');
                        //             //$year = date('Y');
                        //             $data = preg_split("#/#", $endmonth);
                        //             if($data[1] < $endyear){

                        //             }
                        //             //print_r($var);
                        //         }else{

                        //         }

                        //         //$this->view->parError = 'Bạn đã trên 75 tuổi. Bạn không cần phải đóng phí thành viên!';
                        //     }
                        // }

                    }
                }


                //check xem ngày hiện tại đã đến hạn nộp phí chưa


               if($this->view->parError == ''){
                    $date = new Zend_Date();

                    $numbermonth = 0;
                    if($filter->filter($arrInput['month']) >0 ){
                        $numbermonth = $filter->filter($arrInput['month']) - 1;
                    }

                    $text = "+".$numbermonth." months";


                    $dateUse = str_replace('/', '-', '01/'.$filter->filter($arrInput['startmonth']));
                    $effectiveMonth = date('m/Y', strtotime($text,strtotime($dateUse))
                    );

                    $yearSaving = date('Y', strtotime($text,strtotime($dateUse)));
                    $endmonthdate = $yearSaving.'-'.date('m', strtotime($text,strtotime($dateUse))).'-01 00:00:00';

                    $paymentoffline = new Default_Model_PaymentLawyerOffline();

                    $data_paymentoffline = array(
                        'payment_lawyer_off_code' => $filter->filter($arrInput['payment_lawyer_off_code']),
                        'payment_lawyer_off_status' => '0',
                        'payment_lawyer_off_created_date' => $date->toString('YYYY-MM-dd HH:mm:ss'),
                        //'amount' => ereg_replace("[^0-9]", "", $filter->filter($arrInput['amount'])),
                        'amount' => ($filter->filter($arrInput['amount'])*$filter->filter($arrInput['month'])),
                        'month' => $filter->filter($arrInput['month']),
                        'startedmonth' => $filter->filter($arrInput['startmonth']),
                        'payment_type' => $filter->filter($arrInput['type']),
                        'endmonth' => $effectiveMonth,
                        'endmonthdate' => $endmonthdate,
                        'year' => $yearSaving,
                        'cus_id'=> $filter->filter($arrInput['cus_id']),
                        'category_fee_id' => $filter->filter($arrInput['category_fee_id'])
                    );
                    $idpaymentfeemember = $paymentoffline ->insert($data_paymentoffline);

                    /*insert log action*/
                    $this->auth = Zend_Auth::getInstance();
                    $this->identity = $this->auth->getIdentity();
                    $currentdate = new Zend_Date();
                    $useradminlog = new Default_Model_UserAdminLog();
                    $datalog = array(
                        'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                        'useradmin_username' => $this->identity->user_username,
                        'action' => 'Tạo biên nhận phí thành viên',
                        'page' => $this->_request->getControllerName(),
                        'useradmin_id' => $this->identity->user_id,
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'access_object' => $idpaymentfeemember
                    );
                    $useradminlog->insert($datalog);

                 }
             }
         }
    }


    public function createfeeintershipAction(){
        $this->_helper->layout('layout')->disableLayout();

        $inter_id = '';
        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                //$request = $this->getRequest();
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if(!Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                    $this->view->parError = 'Bạn phải chọn luật sư để tạo phí tập sự!';
                }

                // $countPaymentOnline = new Default_Model_PaymentIntershipOnline();
                // $countPaymentOffline = new Default_Model_PaymentIntershipOffline();
                // if(count($countPaymentOnline) > 0 || count($countPaymentOffline) ){
                //     $this->view->parError = 'Bạn đã đóng phí tập sự, bạn không thể đóng nữa!';
                // }

                if(Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                    $cus_id = $arrInput['cus_id'];

                    $modelCustomers = new Default_Model_Customer();

                    $dataCustomer = $modelCustomers->getCustomerByUserId($cus_id);

                    // if($dataCustomer != null && $dataCustomer['cus_lawyer_cityid'] == 1 ){
                    //     $this->view->parError = 'Bạn là luật sư ở tỉnh, bạn không thể đóng phí tập sự' ;
                    // }

                    if($dataCustomer != null && $dataCustomer['cus_member'] != null &&
                    $dataCustomer['cus_member'] == 1){
                        $this->view->parError = 'Bạn không thể đóng phí tập sự khi đã là thành viên của Đoàn.' ;
                    }else{
                        $db = Zend_Db_Table::getDefaultAdapter();
                        $select = new Zend_Db_Select($db);
                        $select->from('intership', array('inter_id','cus_id','payment_inter_status'))
                        // ->joinInner(
                        //     'intership_number',
                        //     'intership_number.intership_number_id = intership.intership_number_id',
                        //     array('intership_number_enddate'))
                        ->where('intership.cus_id = ?',$cus_id)
                        ->where('intership.payment_inter_status = ?',0)
                        ->order('inter_id desc')
                        ->limit(1);

                        $resultSet = $db->fetchRow($select);

                        if($resultSet != null){
                            // if($resultSet['payment_inter_status'] == 1){
                            //     $this->view->parError = 'Hiện tại đợt tập sự của bạn đã được đóng phí' ;
                            // }else{
                                $inter_id = $resultSet['inter_id'];
                                // $db = Zend_Db_Table::getDefaultAdapter();
                                // $select = new Zend_Db_Select($db);
                                // $select->from('payment_intership_offline', array('inter_id','cus_id','payment_inter_off_id'))
                                // ->where('payment_intership_offline.cus_id = ?',$cus_id)
                                // ->where('payment_intership_offline.inter_id = ?',$resultSet['inter_id'])
                                // //->order('inter_id desc')
                                // ->limit(1);

                                // $resultSet1 = $db->fetchRow($select);
                                // if($resultSet1 != null){
                                //     if($resultSet1['payment_inter_off_id'] != null){

                                //     }
                                // }
                            //}
                        }else{
                            $this->view->parError = 'Bạn không thể đóng phí tập sự khi chưa được thêm vào đợt tập sự. Bạn phải thêm vào đợt tập sự, trước khi đóng phí.' ;
                        }

                    }
                }

               if($this->view->parError == ''){
                    $date = new Zend_Date();
                    $law_num = new Default_Model_LawyerNumber();

                    // $mil = $filter->filter($arrInput['regis_date']);
                    // $seconds = $mil / 1000;
                    // $time_regis_date =  date("Y-m-d H:m:s", $seconds);

                    //load so tien
                    $modelCategoryFee = new Default_Model_CategoryFee();
                    $dataModelCategoryFee = $modelCategoryFee->loadCategoryFeeById($filter->filter($arrInput['category_fee_id']));
                    $amount = $dataModelCategoryFee['mooney'] != 0 ? $dataModelCategoryFee['mooney'] : 2000000;

                    // create payment intership
                    $paymentoffline = new Default_Model_PaymentIntershipOffline();

                    $data_paymentoffline = array(
                        'payment_inter_off_code' => $filter->filter($arrInput['payment_inter_off_code']),
                        'payment_inter_off_status' => '0',
                        'payment_inter_off_created_date' => $date->toString('YYYY-MM-dd HH:mm:ss'),
                        //'amount' =>  ereg_replace("[^0-9]", "", $filter->filter($arrInput['amount'])),
                        'amount' => $amount,
                        'category_fee_id' => $filter->filter($arrInput['category_fee_id']),
                        'cus_id'=> $filter->filter($arrInput['cus_id']),
                        'payment_type' => 'offline',
                        'inter_id' => $inter_id
                    );

                    $idpaymentinter = $paymentoffline ->insert($data_paymentoffline);

                     /*insert log action*/
                     $this->auth = Zend_Auth::getInstance();
                     $this->identity = $this->auth->getIdentity();
                     $currentdate = new Zend_Date();
                     $useradminlog = new Default_Model_UserAdminLog();
                     $datalog = array(
                         'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                         'useradmin_username' => $this->identity->user_username,
                         'action' => 'Tạo biên nhận phí tập sự',
                         'page' => $this->_request->getControllerName(),
                         'useradmin_id' => $this->identity->user_id,
                         'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                         'access_object' => $idpaymentinter
                     );
                     $useradminlog->insert($datalog);

                 }
             }
         }
    }

    public function detailjoiningpaymentAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymentjoiningoffline = new Default_Model_PaymentJoiningOffline();
        $payment_joining_off_id = $this->getRequest()->getParam('payment_joining_off_id');
        $data = $paymentjoiningoffline->loadPaymentJoiningOfflineById($payment_joining_off_id);
        $this->view->paymentjoiningofflinedata =  $data;

        $type= $this->getRequest()->getParam('type');
        if($type == "confirm"){
            $this->view->actionname = "confirm";
        }else{
            $this->view->actionname = "cancel";
        }
    }

    public function updatejoiningpaymentAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if($this->view->parError == ''){
                    $payment = new Default_Model_PaymentJoiningOffline();
                    $data = array(
                        'payment_joining_off_status'=> $filter->filter($arrInput['payment_joining_off_status'])
                    );
                    //$this->view->data = $data;
                    $payment->update($data, 'payment_joining_off_id = '. (int)($filter->filter($arrInput['payment_joining_off_id'])));

                    if($filter->filter($arrInput['payment_joining_off_status']) == 1){
                        $customer = new Default_Model_Customer();
                        $dataCus = array(
                            'cus_member' => 1
                        );
                        $customer->update($dataCus, 'cus_id = '. (int)($filter->filter($arrInput['cus_id'])));

                        $modelHistory = new Default_Model_HistoryJoining();
                        $dataHistory = array(
                            'payment_joining_status' => 1
                        );
                        $modelHistory->update($dataHistory, 'history_joining_id = '. (int)($filter->filter($arrInput['history_id'])));

                    }

                     /*insert log action*/
                     $this->auth = Zend_Auth::getInstance();
                     $this->identity = $this->auth->getIdentity();
                     $currentdate = new Zend_Date();
                     $useradminlog = new Default_Model_UserAdminLog();
                     $datalog = array(
                         'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                         'useradmin_username' => $this->identity->user_username,
                         'action' => 'Cập nhật biên nhận phí gia nhập',
                         'page' => $this->_request->getControllerName(),
                         'useradmin_id' => $this->identity->user_id,
                         'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                         'access_object' => $filter->filter($arrInput['payment_joining_off_id'])
                     );
                     $useradminlog->insert($datalog);


                }
            }
        }

    }

    public function updatedifferentpaymentAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if($this->view->parError == ''){
                    $payment = new Default_Model_PaymentOffline();
                    $data = array(
                        'payment_off_status'=> $filter->filter($arrInput['payment_off_status'])
                    );
                    //$this->view->data = $data;
                    $payment->update($data, 'payment_id = '. (int)($filter->filter($arrInput['payment_id'])));

                      /*insert log action*/
                      $this->auth = Zend_Auth::getInstance();
                      $this->identity = $this->auth->getIdentity();
                      $currentdate = new Zend_Date();
                      $useradminlog = new Default_Model_UserAdminLog();
                      $datalog = array(
                          'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                          'useradmin_username' => $this->identity->user_username,
                          'action' => 'Cập nhật biên nhận phí khác',
                          'page' => $this->_request->getControllerName(),
                          'useradmin_id' => $this->identity->user_id,
                          'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                          'access_object' => $filter->filter($arrInput['payment_lawyer_off_id'])
                      );
                      $useradminlog->insert($datalog);

                }
            }
        }

    }

    //view deatail to change status fee different
    public function detaildifferentpaymentAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $payment_id = $this->getRequest()->getParam('payment_id');
        $model = new Default_Model_PaymentOffline();
        $data = $model->loadPaymentOfflineById($payment_id);
        $this->view->differentpayment = $data;
    }

    public function detailmemberpaymentAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymentlawyertrainingoffline = new Default_Model_PaymentLawyerOffline();
        $payment_lawyer_off_id = $this->getRequest()->getParam('payment_lawyer_off_id');
        $data = $paymentlawyertrainingoffline->loadPaymentLawyerOfflineById($payment_lawyer_off_id);
        $this->view->paymentlawyerofflinedata =  $data;

        $type= $this->getRequest()->getParam('type');
        if($type == "confirm"){
            $this->view->actionname = "confirm";
        }else{
            $this->view->actionname = "cancel";
        }

    }

    public function updatememberpaymentAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if($this->view->parError == ''){
                    $payment = new Default_Model_PaymentLawyerOffline();
                    $data = array(
                        'payment_lawyer_off_status'=> $filter->filter($arrInput['payment_lawyer_off_status'])
                    );
                    //$this->view->data = $data;
                    $payment->update($data, 'payment_lawyer_off_id = '. (int)($filter->filter($arrInput['payment_lawyer_off_id'])));

                    //update thông tin luật sư nếu payment được thanh toán
                    // if($filter->filter($arrInput['payment_lawyer_off_status']) == 1){
                    //     $modelLawyer = new Default_Model_Lawyer();
                    //     $dataLawyer = array(
                    //         'startmonth'=> $filter->filter($arrInput['startedmonth']),
                    //         'endmonth' => $filter->filter($arrInput['endmonth'])
                    //     );
                    //     $modelLawyer->update($dataLawyer, 'cus_id = '. (int)($filter->filter($arrInput['cus_id'])));

                    // }

                     /*insert log action*/
                     $this->auth = Zend_Auth::getInstance();
                     $this->identity = $this->auth->getIdentity();
                     $currentdate = new Zend_Date();
                     $useradminlog = new Default_Model_UserAdminLog();
                     $datalog = array(
                         'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                         'useradmin_username' => $this->identity->user_username,
                         'action' => 'Cập nhật biên nhận phí thành viên',
                         'page' => $this->_request->getControllerName(),
                         'useradmin_id' => $this->identity->user_id,
                         'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                         'access_object' => $filter->filter($arrInput['payment_lawyer_off_id'])
                     );
                     $useradminlog->insert($datalog);


                }
            }
        }

    }



    public function detailintershippaymentAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymentintershipoffline = new Default_Model_PaymentIntershipOffline();
        $payment_inter_off_id = $this->getRequest()->getParam('payment_inter_off_id');
        $data = $paymentintershipoffline->loadPaymentIntershipOfflineById($payment_inter_off_id);
        $this->view->paymentintershipofflinedata =  $data;

        $type= $this->getRequest()->getParam('type');
        if($type == "confirm"){
            $this->view->actionname = "confirm";
        }else{
            $this->view->actionname = "cancel";
        }

    }

    public function updateintershippaymentAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if($this->view->parError == ''){
                    $payment = new Default_Model_PaymentIntershipOffline();
                    $data = array(
                        'payment_inter_off_status'=> $filter->filter($arrInput['payment_inter_off_status'])
                    );
                    //$this->view->data = $data;
                    $payment->update($data, 'payment_inter_off_id = '. (int)($filter->filter($arrInput['payment_inter_off_id'])));


                    //$this->view->data = $data;
                    if($filter->filter($arrInput['payment_inter_off_status']) == 1){
                        $intership = new Default_Model_Intership();
                        $data = array(
                            'payment_inter_status'=> 1
                        );
                        $intership->update($data, 'inter_id = '. (int)($filter->filter($arrInput['inter_id'])));
                    }


                      /*insert log action*/
                      $this->auth = Zend_Auth::getInstance();
                      $this->identity = $this->auth->getIdentity();
                      $currentdate = new Zend_Date();
                      $useradminlog = new Default_Model_UserAdminLog();
                      $datalog = array(
                          'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                          'useradmin_username' => $this->identity->user_username,
                          'action' => 'Cập nhật biên nhận phí tập sự',
                          'page' => $this->_request->getControllerName(),
                          'useradmin_id' => $this->identity->user_id,
                          'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                          'access_object' => $filter->filter($arrInput['payment_lawyer_off_id'])
                      );
                      $useradminlog->insert($datalog);

                }
            }
        }

    }

    public function differentAction(){
        $categoryfreelawyer = new Default_Model_CategoryFeeLawyer();
        $data = $categoryfreelawyer->loadCategoryFeeLawyer('different');
        $this->view->categoryfee =  $data;

        $lawyer = new Default_Model_Lawyer();
        $data = $lawyer->loadLawyer();
        $this->view->lawyers = $data;

        $paymentdifferent = new Default_Model_PaymentOffline();
        $payment_different_off_code = $paymentdifferent->generationCode('PTT','');
        $this->view->payment_different_off_code = $payment_different_off_code;
    }

    /*create fee memberr*/
    public function createfeedifferentAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                //$request = $this->getRequest();
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;


                if($this->view->parError == ''){
                    $date = new Zend_Date();
                    // $law_num = new Default_Model_LawyerNumber();

                    // $data = array(
                    //     //'month'=>$filter->filter($arrInput['month']),
                    //     'law_fromdate' => $filter->filter($arrInput['regis_date']),
                    //     'law_enddate' => '',
                    //     'category_fee_lawyer_id' => $filter->filter($arrInput['category_fee_lawyer_id']),
                    //     'amount' =>  $filter->filter($arrInput['amount'])
                    // );

                    // $lawnum_id = $law_num->insert($data);

                    // $billoffline = new Default_Model_BillFeeOffline();

                    // $data_lawyernumber = array(
                    //     'lawnum_id' => $lawnum_id,
                    //     //'cus_id' => $filter->filter($arrInput['cus_id']),
                    //     'bill_feeoffline_seria' => 'test',
                    //     'community' => $filter->filter($arrInput['community'])

                    // );
                    // $bill_feeoffline_id = $billoffline ->insert($data_lawyernumber);

                    $law_id = null;
                    $community = null;
                    if($filter->filter($arrInput['community_type']) == 'person'){
                        $law_id = $filter->filter($arrInput['law_id']);
                    }else{
                        $community = $filter->filter($arrInput['community']);
                    }


                    $paymentoffline = new Default_Model_PaymentOffline();

                    $data_paymentoffline = array(
                        'payment_off_code' => $filter->filter($arrInput['payment_different_off_code']),
                        'type' => $filter->filter($arrInput['reason_method']),
                        'payment_off_status' => '0',
                        'payment_off_created_date' => $date->toString('YYYY-MM-dd HH:mm:ss'),
                        'amount' => $filter->filter($arrInput['amount']),
                        'reason' => $filter->filter($arrInput['reason']),
                        'community' => $community,
                        'law_id' => $law_id,
                        'payment_type' => 'offline'

                    );
                    $idpaymentdifferent = $paymentoffline ->insert($data_paymentoffline);

                        /*insert log action*/
                    $this->auth = Zend_Auth::getInstance();
                    $this->identity = $this->auth->getIdentity();
                    $currentdate = new Zend_Date();
                    $useradminlog = new Default_Model_UserAdminLog();
                    $datalog = array(
                        'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                        'useradmin_username' => $this->identity->user_username,
                        'action' => 'Tạo biên nhận phí khác',
                        'page' => $this->_request->getControllerName(),
                        'useradmin_id' => $this->identity->user_id,
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'access_object' => $idpaymentdifferent
                    );
                    $useradminlog->insert($datalog);

                    }
                }
            }
    }

    /**
     * In chứng chỉ luật sư
     */
    public function printlawyerfeeAction(){
         $this->_helper->layout('homelayout')->disableLayout();
        $paymentlawyertrainingoffline = new Default_Model_PaymentLawyerOffline();
        $payment_lawyer_off_id = $this->getRequest()->getParam('payment_lawyer_off_id');
        $data = $paymentlawyertrainingoffline->loadPaymentLawyerOfflineById($payment_lawyer_off_id);
        $valueWords = $this->convertNumberToWords( $data["amount"]);
        $data["amount_word"] = $valueWords; // works

        // $text = "+".($data['month']-1)." months";
        // $endMonth = date('m/Y', strtotime($text, strtotime($data['payment_lawyer_off_created_date'])));
        // $startedMonth = date("m/Y",strtotime($data['payment_lawyer_off_created_date']));

        // $data['endMonth'] = $endMonth;
        // $data['startedMonth'] = $startedMonth;

        $this->view->paymentlawyerofflinedata =  $data;

        /*insert log action*/
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
        $currentdate = new Zend_Date();
        $useradminlog = new Default_Model_UserAdminLog();
        $datalog = array(
            'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
            'useradmin_username' => $this->identity->user_username,
            'action' => 'In biên nhận phí thành viên',
            'page' => $this->_request->getControllerName(),
            'useradmin_id' => $this->identity->user_id,
            'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
            'access_object' => $payment_lawyer_off_id
        );
        $useradminlog->insert($datalog);


    }

    /**
     * In biên nhập khác
     */
    public function printdifferentfeeAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $payment_id = $this->getRequest()->getParam('payment_id');
        $model = new Default_Model_PaymentOffline();
        $data = $model->loadPaymentOfflineById($payment_id);
        $this->view->differentpayment = $data;
    }

    /**
     * In bieen nhan tap su
     */
    public function printjoiningfeeAction(){
       $this->_helper->layout('homelayout')->disableLayout();
       $paymentjoiningoffline = new Default_Model_PaymentJoiningOffline();
       $payment_joining_off_id = $this->getRequest()->getParam('payment_joining_off_id');
       $data = $paymentjoiningoffline->loadPaymentJoiningOfflineById($payment_joining_off_id);
       $valueWords = $this->convertNumberToWords( $data["amount"]);
       $data["amount_word"] = $valueWords; // works
       $this->view->paymentjoiningofflinedata =  $data;

        /*insert log action*/
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
        $currentdate = new Zend_Date();
        $useradminlog = new Default_Model_UserAdminLog();
        $datalog = array(
            'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
            'useradmin_username' => $this->identity->user_username,
            'action' => 'In biên nhận phí gia nhập',
            'page' => $this->_request->getControllerName(),
            'useradmin_id' => $this->identity->user_id,
            'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
            'access_object' => $payment_joining_off_id
        );
        $useradminlog->insert($datalog);


   }

   /**
    * In biên nhận phí bồi dưỡng
    */
   public function printintershipfeeAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $paymentintershipoffline = new Default_Model_PaymentIntershipOffline();
        $payment_inter_off_id = $this->getRequest()->getParam('payment_inter_off_id');
        $data = $paymentintershipoffline->loadPaymentIntershipOfflineById($payment_inter_off_id);


        $valueWords = $this->convertNumberToWords( $data["amount"]);
        $data["amount_word"] = $valueWords; // works

        $this->view->paymentintershipofflinedata =  $data;

        /*insert log action*/
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
        $currentdate = new Zend_Date();
        $useradminlog = new Default_Model_UserAdminLog();
        $datalog = array(
            'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
            'useradmin_username' => $this->identity->user_username,
            'action' => 'In biên nhận phí tập sự',
            'page' => $this->_request->getControllerName(),
            'useradmin_id' => $this->identity->user_id,
            'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
            'access_object' => $payment_inter_off_id
        );
        $useradminlog->insert($datalog);
    }

    public function cancellistAction(){
         //$this->_helper->layout('homelayout')->disableLayout();
         $paymentoffline = new Default_Model_PaymentLawyerOffline();
         $data = $paymentoffline->loadPaymentLawyerOffline();
         $this->view->paymentofflines =  $data;

         $paymentjoiningoffline = new Default_Model_PaymentJoiningOffline();
         $data = $paymentjoiningoffline->loadPaymentJoiningOffline();
         $this->view->paymentjoiningofflines =  $data;

         $paymentintershipoffline = new Default_Model_PaymentIntershipOffline();
         $data = $paymentintershipoffline->loadPaymentIntershipOffline();
         $this->view->paymentintershipofflines =  $data;

         $modelPaymentOffline = new Default_Model_PaymentOffline();
         $paymentOffline = $modelPaymentOffline->loadPaymentOffline();
         $this->view->paymentOfflines =  $paymentOffline;
    }

    /* function convert number to char*/
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
}