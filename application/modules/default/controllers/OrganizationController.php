<?php

class OrganizationController extends Zend_Controller_Action
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


    public function lawyerAction(){
        $model = new Default_Model_OrganizationLawDetails();
        $data = $model->loadOrganzationsLaw();
        $this->view->organizations = $data;
    }

    public function addlawyerAction(){
        $this->_helper->layout('layout')->disableLayout();

        $currentdate = new Zend_Date();
        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $filter = new Zend_Filter();
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;


                $model = new Default_Model_OrganizationLawyer();


                $check = $model->checkLawyerExistInOrgan($filter->filter($arrInput['law_name']),$filter->filter($arrInput['organ_detail_id']));
                if( $check != null && count($check) >0){
                    $this->view->parError = 'Luật sư đã được thêm vào TCHN';
                }
                if($this->view->parError == ''){
                    $data = array(
                        // 'law_organ' => $filter->filter($arrInput['law_name']),
                        // 'law_birthday' => $filter->filter($arrInput['law_birthday']),
                        // 'law_nation' => $filter->filter($arrInput['law_nation']),
                        'organ_law_id' => $filter->filter($arrInput['organ_detail_id']),
                        'law_id' => $filter->filter($arrInput['law_name']),
                        'note' => $filter->filter($arrInput['law_note']),
                        // 'law_certification' => $filter->filter($arrInput['law_certification']),
                        // 'law_certification_date' => $filter->filter($arrInput['law_certification_date']),
                        // 'law_joining_organ_date' => $filter->filter($arrInput['law_joining_organ_date'])
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss')
                    );

                    $model->insert($data);

                }
            }
        }
    }

    public function indexAction(){
            //load list lawyer
            $lawyer = new Default_Model_Lawyer();
            $data = $lawyer->loadLawyer();
            $this->view->lawyers = $data;

            $model = new Default_Model_OrganizationLawDetails();
            $data = $model->loadOrganzationsLaw();
            $this->view->organizations = $data;

            $modelCustomers = new Default_Model_Customer();
            $customers = $modelCustomers->fetchAll();
            $this->view->customers = $customers;
    }

    public function createAction(){

        $this->_helper->layout('layout')->disableLayout();

        $customers = $this->getRequest()->getParam('customers');

        $myArray = '';
         //role with action
        if($customers != null && sizeof($customers)){
            foreach($customers as $cus){
                $myArray += $cus +',';
            }
        }

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $filter = new Zend_Filter();
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                $currentdate = new Zend_Date();
                $model = new Default_Model_OrganizationLawDetails();
                $checkemail = $model->validateEmail($filter->filter($arrInput['organ_email']));
                if( $checkemail != null &&  $checkemail >0){
                    $this->view->parError = 'Email đã được sử dụng';
                }

                if(!Zend_Validate::is($arrInput['law_id'],'NotEmpty')){
                    $this->view->parError = 'Bạn phải chọn luật sư để tạo quản lý!';
                }

                if($this->view->parError == ''){

                    $data = array(
                        'organ_name' => $filter->filter($arrInput['organ_name']),
                        'organ_type' => $filter->filter($arrInput['organ_type']),
                        'organ_mobile' => $filter->filter($arrInput['organ_name']),
                        'organ_email' => $filter->filter($arrInput['organ_email']),
                        'organ_fax' => $filter->filter($arrInput['organ_fax']),
                        'organ_certification' => $filter->filter($arrInput['organ_certification']),
                        'organ_note' => $filter->filter($arrInput['organ_note']),
                        'law_id'=> $filter->filter($arrInput['law_id']),
                        'law_organ_address' => $filter->filter($arrInput['law_organ_address']),
                        'law_organ_address_hktt' => $filter->filter($arrInput['law_organ_address_hktt']),
                        'district'  => $filter->filter($arrInput['district']),
                        'customers' => $myArray,
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss')
                    );
                    $model->insert($data);

                }

            }
        }
    }

    // load danh sach tchn
    public function listorgandatatablefilterAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $type = $this->getRequest()->getParam('type');

        $model = new Default_Model_OrganizationLaw();
        $data = $model->loadOrganzationLawByFilter($start,$length,$search,$type);

        $results = array(
        );

        if($data != null && sizeof($data)){
            $index = 0;
            foreach($data as $pay){
                // $index += 1;
                array_push($results,[
                'organ_detail_id' => $pay['organ_detail_id'],
                'organ_name' => $pay['organ_name'] != null ? $pay['organ_name'] : '' ,
                //date("d/m/Y", strtotime($pay['discipline_date'])),
                'organ_certification' => $pay['organ_certification'],
                'organ_certification_date' => $pay['organ_certification_date'] != null ? $pay['organ_certification_date'] : '' ,
                'organ_mobile' => $pay['organ_mobile'] != null ? $pay['organ_mobile'] : '' ,
                'law_organ_address' => $pay['law_organ_address'] != null ? $pay['law_organ_address'] : '',
                'law_organ_address_hktt' => $pay['law_organ_address_hktt'] != null ? $pay['law_organ_address_hktt'] : '',
                'organ_email' =>$pay['organ_email'] != null ? $pay['organ_email'] : '',
                'district' => $pay['district'] != null ? $pay['district'] : '',
                'cus_fullname' => $pay['cus_firstname'].' '.$pay['cus_lastname'],
                'cus_lawyer_number' => $pay['cus_lawyer_number'],
                'law_certfication_no' => $pay['law_certfication_no'],
                'thongtin_thaydoi' => $pay['thongtin_thaydoi'],
                'ngaycapnhat' => $pay['ngaycapnhat']
                ]);
            }
        }


        $totalrecords = $model->loadOrganzationLawByFilterTotals($search,$type);

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval($totalrecords),
            "recordsFiltered" => intval($totalrecords),
            "data"            => $results
        );

        echo json_encode($json_data);
        exit;
    }

    // load detail cho organization law detail
    public function detailAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $organDetailModel = new Default_Model_OrganizationLawDetails();
        $organ_detail_id = $this->getRequest()->getParam('organ_detail_id');
        $data = $organDetailModel->loadOrganzationsLawById($organ_detail_id);

        $this->view->organdetail =  $data;
    }

    public function addinformationAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                $currentdate = new Zend_Date();
                if($this->view->parError == ''){
                    $payment = new Default_Model_OrganizationLawDetails();
                    $data = array(
                        'total_law'=> $filter->filter($arrInput['total_law']),
                        'total_law_native'=> $filter->filter($arrInput['total_law_native']),
                        'total_law_foreign'=> $filter->filter($arrInput['total_law_foreign']),
                        'total_job_done'=> $filter->filter($arrInput['total_job_done']),
                        'total_procedure'=> $filter->filter($arrInput['total_procedure']),
                        'total_criminal' => $filter->filter($arrInput['total_criminal']),
                        'total_support_service'=> $filter->filter($arrInput['total_support_service']),
                        'total_support'=> $filter->filter($arrInput['total_support'])  ,
                        'amount'=> $filter->filter($arrInput['amount'])  ,
                        'amount_charge'=> $filter->filter($arrInput['amount_charge'])
                    );
                    $payment->update($data, 'organ_detail_id = '. (int)($filter->filter($arrInput['organ_detail_id'])));

                     /*insert log action*/
                    //  $this->auth = Zend_Auth::getInstance();
                    //  $this->identity = $this->auth->getIdentity();
                    //  $currentdate = new Zend_Date();
                    //  $useradminlog = new Default_Model_UserAdminLog();
                    //  $datalog = array(
                    //      'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                    //      'useradmin_username' => $this->identity->user_username,
                    //      'action' => 'Cập nhật biên nhận phí thành viên',
                    //      'page' => $this->_request->getControllerName(),
                    //      'useradmin_id' => $this->identity->user_id,
                    //      'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                    //      'access_object' => $filter->filter($arrInput['payment_lawyer_off_id'])
                    //  );
                    //  $useradminlog->insert($datalog);


                }
            }
        }

    }

    public function updateAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                $currentdate = new Zend_Date();
                if($this->view->parError == ''){
                    $payment = new Default_Model_OrganizationLawDetails();
                    $data = array(
                        'organ_name'=> $filter->filter($arrInput['organ_name']),
                        'organ_certification'=> $filter->filter($arrInput['organ_certification']),
                        'organ_mobile'=> $filter->filter($arrInput['organ_mobile']),
                        'law_organ_address'=> $filter->filter($arrInput['law_organ_address']),
                        'law_organ_address_hktt'=> $filter->filter($arrInput['law_organ_address_hktt']),
                        'modified_date' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'thongtin_thaydoi'=> $filter->filter($arrInput['thongtin_thaydoi']),
                        'ngaycapnhat'=> $filter->filter($arrInput['ngaycapnhat'])
                    );
                    $payment->update($data, 'organ_detail_id = '. (int)($filter->filter($arrInput['organ_detail_id'])));

                     /*insert log action*/
                    //  $this->auth = Zend_Auth::getInstance();
                    //  $this->identity = $this->auth->getIdentity();
                    //  $currentdate = new Zend_Date();
                    //  $useradminlog = new Default_Model_UserAdminLog();
                    //  $datalog = array(
                    //      'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                    //      'useradmin_username' => $this->identity->user_username,
                    //      'action' => 'Cập nhật biên nhận phí thành viên',
                    //      'page' => $this->_request->getControllerName(),
                    //      'useradmin_id' => $this->identity->user_id,
                    //      'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                    //      'access_object' => $filter->filter($arrInput['payment_lawyer_off_id'])
                    //  );
                    //  $useradminlog->insert($datalog);


                }
            }
        }
    }

    public function exportexcelorganizationdetailsAction(){

        $this->_helper->layout('layout')->disableLayout();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $modelOrganizationDetails = new Default_Model_OrganizationLawDetails();
        $data = $modelOrganizationDetails->loadOrganzationDetails();

        $excel = new Default_Model_Excel();

        $excel->setActiveSheetIndex(0);

        $excel->getActiveSheet()->setTitle('TC HNLS');


        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('S')->setWidth(30);


        $excel->getActiveSheet()->getStyle('A1:S1')->getFont()->setBold(true);

        $excel->getActiveSheet()->setCellValue('A1', 'TÊN TỔ CHỨC HNLS');
        $excel->getActiveSheet()->setCellValue('B1', 'TRƯỞNG VĂN PHÒNG');
        $excel->getActiveSheet()->setCellValue('C1', 'SỐ CCHN');
        $excel->getActiveSheet()->setCellValue('D1', 'SỐ THẺ');
        $excel->getActiveSheet()->setCellValue('E1', 'N.SINH');
        $excel->getActiveSheet()->setCellValue('F1', 'DI ĐỘNG');
        $excel->getActiveSheet()->setCellValue('G1', 'THÀNH VIÊN');
        $excel->getActiveSheet()->setCellValue('H1', 'ĐỊA CHỈ VP');
        $excel->getActiveSheet()->setCellValue('I1', 'ĐỊA CHỈ HKTT');
        $excel->getActiveSheet()->setCellValue('J1', 'QUẬN');
        $excel->getActiveSheet()->setCellValue('K1', 'Đ THOẠI');
        $excel->getActiveSheet()->setCellValue('L1', 'FAX');
        $excel->getActiveSheet()->setCellValue('M1', 'EMAIL');
        $excel->getActiveSheet()->setCellValue('N1', 'SỐ GIẤY PHÉP');
        $excel->getActiveSheet()->setCellValue('O1', 'NGÀY CẤP');
        $excel->getActiveSheet()->setCellValue('P1', 'THÔNG TIN THAY ĐỔI');
        $excel->getActiveSheet()->setCellValue('Q1', 'NGÀY CẬP NHẬT');
        $excel->getActiveSheet()->setCellValue('R1', '');
        $excel->getActiveSheet()->setCellValue('S1', 'CHỈ ĐỊNH');

        $numRow = 2;
        foreach($data as $row){
            $excel->getActiveSheet()->setCellValue('A'.$numRow, $row['organ_name']);
            $excel->getActiveSheet()->setCellValue('B'.$numRow, $row['cus_firstname'].' '.$row['cus_lastname']);
            $excel->getActiveSheet()->setCellValue('C'.$numRow, $row['cus_lawyer_number']);
            $excel->getActiveSheet()->setCellValue('D'.$numRow, $row['law_certfication_no']);
            $excel->getActiveSheet()->setCellValue('E'.$numRow, date("d/m/Y", strtotime($row['cus_birthday'])));
            $excel->getActiveSheet()->setCellValue('F'.$numRow, $row['cus_cellphone']);
            $excel->getActiveSheet()->setCellValue('G'.$numRow, '');
            $excel->getActiveSheet()->setCellValue('H'.$numRow, $row['law_organ_address']);
            $excel->getActiveSheet()->setCellValue('I'.$numRow, $row['law_organ_address_hktt']);
            $excel->getActiveSheet()->setCellValue('J'.$numRow, $row['district']);
            $excel->getActiveSheet()->setCellValue('K'.$numRow, $row['organ_mobile']);
            $excel->getActiveSheet()->setCellValue('L'.$numRow, $row['organ_fax']);
            $excel->getActiveSheet()->setCellValue('M'.$numRow, $row['organ_email']);
            $excel->getActiveSheet()->setCellValue('N'.$numRow, $row['organ_certification']);
            $excel->getActiveSheet()->setCellValue('O'.$numRow, $row['organ_certification_date']);
            $excel->getActiveSheet()->setCellValue('P'.$numRow, $row['thongtin_thaydoi']);
            $excel->getActiveSheet()->setCellValue('Q'.$numRow, $row['ngaycapnhat']);
            $excel->getActiveSheet()->setCellValue('R'.$numRow, '');
            $excel->getActiveSheet()->setCellValue('S'.$numRow, $row['chidinh']);
            $numRow++;
        }

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => '#000000'),
                ),
            ),
        );

        $excel->getActiveSheet()->getStyle('A1:S'.$numRow)->applyFromArray($styleArray);

        $excel->getActiveSheet()->getStyle('A1:S'.$numRow)->applyFromArray($styleArray);
        foreach(range('A','K') as $columnID) {
            $excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Danhsach_TCHN.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;

    }
}