<?php
// Customers controller 
class StatisticController extends Zend_Controller_Action
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

    public function listpaymentdifferentdatatableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        // $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $model = new Default_Model_PaymentOffline();
        $data = $model->loadPaymentOfflineFilter($start,$length,$startdate,$enddate);

        $results = array(            
        );

        $dateEmpty = '1900-01-01 00:00:00';
        $index = 0;
        //$currentDate = new Zend_Date();
        if($data != null && sizeof($data)){
            foreach($data as $law){ 
                
                    //$age = (int)date('Y') - (int)date('Y',strtotime($law['cus_birthday']));         
                    
                    //if($search!= null && $search != '' && $age >= (int)$search){
                        // $dateCertification = ($law['law_certification_createdate'] != null && 
                        // $law['law_certification_createdate'] != '' && $law['law_certification_createdate'] != $dateEmpty) ? 
                        // date('d/m/Y',strtotime($law['law_certification_createdate'])) : '';
                        
                        array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                        $law['cus_identity_card'],
                        $law['cus_cellphone'],
                        // $law['law_joining_number'],
                        $law['cus_lawyer_number'],
                        $law['cus_date_lawyer_number'] != null &&  $law['cus_date_lawyer_number'] != $dateEmpty ? date('d/m/Y',strtotime($law['cus_date_lawyer_number'])) : '' ,
                        $law['cus_address_resident'],
                        number_format($law['amount']),
                        $law['payment_off_code'],
                        $law['payment_off_created_date'] != null &&  $law['payment_off_created_date'] != $dateEmpty ? date('d/m/Y',strtotime($law['payment_off_created_date'])) : '' 
                        // $age                    
                        ]);
                        //$index +=1;
                    //}                    
                                
            }
        }

        $total = $model->loadPaymentOfflineFilter('','',$startdate,$enddate);
        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($total)),
            "recordsFiltered" => intval(count($total)),
            "data"            => ($results)
        );
           
        echo json_encode($json_data);
        exit;
    }

          //reportlistloanpaymentintershipexportexcel
    public function reportlistloanpaymentjoiningexportexcelAction(){
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $this->_helper->layout('layout')->disableLayout();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $model = new Default_Model_Lawyer();
        $data = $model->loadListLoanPaymentJoining('','',$startdate,$enddate);

        $results = array(            
        );

        $dateEmpty = '1900-01-01 00:00:00';
        //$currentDate = new Zend_Date();
        if($data != null && sizeof($data) > 0){
            foreach($data as $law){
                //if($law['cus_birthday'] != $dateEmpty && $law['cus_birthday'] != null && $law['cus_birthday'] != ''){
                    //$age = (int)date('Y') - (int)date('Y',strtotime($law['cus_birthday']));         
                    
                    //if($search != null && $search !='' && $age >= (int)$search){
                        // $dateCertification = ($law['law_certification_createdate'] != null && 
                        // $law['law_certification_createdate'] != '' && $law['law_certification_createdate'] != $dateEmpty) ? 
                        // date('d/m/Y',strtotime($law['law_certification_createdate'])) : '';
                        
                        array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                        $law['cus_identity_card'],
                        // $law['cus_birthday'] != '' && $law['cus_birthday'] != $dateEmpty ? date("d/m/Y", strtotime($law['cus_birthday'])) : '',
                        $law['cus_cellphone'],
                        $law['cus_lawyer_number'],
                        $law['cus_date_lawyer_number'] != null && $law['cus_date_lawyer_number'] != $dateEmpty ? date('d/m/Y',strtotime($law['cus_date_lawyer_number'])) : '' ,
                        $law['cus_address_resident'],
                        $law['cus_address_resident_now']                    
                        ]);
                    //}                    
                //}                
            }
        }
            //Khởi tạo đối tượng
        $excel = new Default_Model_Excel();
            //Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Nợ phí gia nhập');

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            //Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => '#000000'),
                ),
            ),
        );        
        
        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Tên');
        $excel->getActiveSheet()->setCellValue('B1', 'CMND');
        // $excel->getActiveSheet()->setCellValue('C1', 'Ngày sinh');
        $excel->getActiveSheet()->setCellValue('C1', 'Số điện thoại');
        $excel->getActiveSheet()->setCellValue('D1', 'Số thẻ LS');        
        $excel->getActiveSheet()->setCellValue('E1', 'Ngày cấp số thẻ LS'); 
        $excel->getActiveSheet()->setCellValue('F1', 'Địa chỉ thường trú');
        $excel->getActiveSheet()->setCellValue('G1', 'Nơi ở hiện tại');
        $excel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);
              

        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        $numRow = 2;
        if($results != null && sizeof($results) >0 ){   
            foreach($results as $row){
                $excel->getActiveSheet()->setCellValue('A'.$numRow, $row[0]);
                $excel->getActiveSheet()->setCellValue('B'.$numRow, $row[1]);
                $excel->getActiveSheet()->setCellValue('C'.$numRow, $row[2]);
                $excel->getActiveSheet()->setCellValue('D'.$numRow, $row[3]);
                $excel->getActiveSheet()->setCellValue('E'.$numRow, $row[4]);
                $excel->getActiveSheet()->setCellValue('F'.$numRow, $row[5]);
                $excel->getActiveSheet()->setCellValue('G'.$numRow, $row[6]);
                // $excel->getActiveSheet()->setCellValue('F'.$numRow, $row[7]);
                
                $numRow++;
            }     
        }   
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_NoPhiGiaNhap.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;   

    }

     //reportlistloanpaymentintershipexportexcel
    public function reportlistloanpaymentintershipexportexcelAction(){
        $this->_helper->layout('layout')->disableLayout();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $model = new Default_Model_Intership();
        $data = $model->loadListLoanPaymentIntership('','');

        $results = array(            
        );

        $dateEmpty = '1900-01-01 00:00:00';
        //$currentDate = new Zend_Date();
        if($data != null && sizeof($data)){
            foreach($data as $law){ 
                //if($law['cus_birthday'] != $dateEmpty && $law['cus_birthday'] != null && $law['cus_birthday'] != ''){
                    //$age = (int)date('Y') - (int)date('Y',strtotime($law['cus_birthday']));         
                    
                    //if($search != null && $search !='' && $age >= (int)$search){
                        // $dateCertification = ($law['law_certification_createdate'] != null && 
                        // $law['law_certification_createdate'] != '' && $law['law_certification_createdate'] != $dateEmpty) ? 
                        // date('d/m/Y',strtotime($law['law_certification_createdate'])) : '';
                        
                        array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                        $law['cus_identity_card'],
                        $law['cus_birthday'] != '' && $law['cus_birthday'] != $dateEmpty ? date("d/m/Y", strtotime($law['cus_birthday'])) : '',
                        $law['cus_cellphone'],
                        $law['cus_lawyer_number'],
                        $law['cus_date_lawyer_number'] != null && $law['cus_date_lawyer_number'] != $dateEmpty ? date('d/m/Y',strtotime($law['cus_date_lawyer_number'])) : '' ,
                        $law['cus_address_resident'],
                        $law['cus_address_resident_now']                    
                        ]);
                    //}                    
                //}                
            }
        }
            //Khởi tạo đối tượng
        $excel = new Default_Model_Excel();
            //Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Nợ phí tập sự');

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            //Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => '#000000'),
                ),
            ),
        );        
        
        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Tên');
        $excel->getActiveSheet()->setCellValue('B1', 'CMND');
        $excel->getActiveSheet()->setCellValue('C1', 'Ngày sinh');
        $excel->getActiveSheet()->setCellValue('D1', 'Số điện thoại');
        // $excel->getActiveSheet()->setCellValue('E1', 'Số thẻ LS');        
        // $excel->getActiveSheet()->setCellValue('F1', 'Ngày cấp số thẻ LS'); 
        $excel->getActiveSheet()->setCellValue('E1', 'Địa chỉ thường trú');
        $excel->getActiveSheet()->setCellValue('F1', 'Nơi ở hiện tại');
        $excel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray);
              

        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        $numRow = 2;
        if($results != null && sizeof($results) >0 ){   
            foreach($results as $row){
                $excel->getActiveSheet()->setCellValue('A'.$numRow, $row[0]);
                $excel->getActiveSheet()->setCellValue('B'.$numRow, $row[1]);
                $excel->getActiveSheet()->setCellValue('C'.$numRow, $row[2]);
                $excel->getActiveSheet()->setCellValue('D'.$numRow, $row[3]);
                // $excel->getActiveSheet()->setCellValue('E'.$numRow, $row[4]);
                // $excel->getActiveSheet()->setCellValue('F'.$numRow, $row[5]);
                $excel->getActiveSheet()->setCellValue('E'.$numRow, $row[6]);
                $excel->getActiveSheet()->setCellValue('F'.$numRow, $row[7]);
                
                $numRow++;
            }     
        }   
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_NoPhiTapSu.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;   

    }

          //load list loan intership
    public function listloanpaymentjoiningdatatableAction(){
        
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        // $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $model = new Default_Model_Lawyer();
        $data = $model->loadListLoanPaymentJoining($start,$length,$startdate,$enddate);

        $results = array(            
        );

        $dateEmpty = '1900-01-01 00:00:00';
        $index = 0;
        //$currentDate = new Zend_Date();
        if($data != null && sizeof($data)){
            foreach($data as $law){ 
                
                    //$age = (int)date('Y') - (int)date('Y',strtotime($law['cus_birthday']));         
                    
                    //if($search!= null && $search != '' && $age >= (int)$search){
                        // $dateCertification = ($law['law_certification_createdate'] != null && 
                        // $law['law_certification_createdate'] != '' && $law['law_certification_createdate'] != $dateEmpty) ? 
                        // date('d/m/Y',strtotime($law['law_certification_createdate'])) : '';
                        
                        array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                        $law['cus_identity_card'],
                        $law['cus_cellphone'],
                        $law['law_joining_number'],
                        $law['cus_lawyer_number'],
                        $law['cus_date_lawyer_number'] != null &&  $law['cus_date_lawyer_number'] != $dateEmpty ? date('d/m/Y',strtotime($law['cus_date_lawyer_number'])) : '' ,
                        $law['cus_address_resident'],
                        $law['cus_address_resident_now']
                        // $age                    
                        ]);
                        //$index +=1;
                    //}                    
                                
            }
        }

        $total = $model->loadListLoanPaymentJoining('','',$startdate,$enddate);
        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($total)),
            "recordsFiltered" => intval(count($total)),
            "data"            => ($results)
        );
           
        echo json_encode($json_data);
        exit;
    }

     //load list loan intership
    public function listloanpaymentintershipdatatableAction(){
        
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        // $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $modelIntership = new Default_Model_Intership();
        $dataIntership = $modelIntership->loadListLoanPaymentIntership($start,$length,$startdate,$enddate);

        $results = array(            
        );

        $dateEmpty = '1900-01-01 00:00:00';
        $index = 0;
        //$currentDate = new Zend_Date();
        if($dataIntership != null && sizeof($dataIntership)){
            foreach($dataIntership as $law){ 
                
                    //$age = (int)date('Y') - (int)date('Y',strtotime($law['cus_birthday']));         
                    
                    //if($search!= null && $search != '' && $age >= (int)$search){
                        // $dateCertification = ($law['law_certification_createdate'] != null && 
                        // $law['law_certification_createdate'] != '' && $law['law_certification_createdate'] != $dateEmpty) ? 
                        // date('d/m/Y',strtotime($law['law_certification_createdate'])) : '';
                        
                        array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                        $law['cus_identity_card'],
                        $law['cus_cellphone'],
                        $law['inter_number_name'],
                        $law['cus_address_resident'],
                        $law['cus_address_resident_now']
                        // $dateCertification,
                        // $law['law_joining_number'],
                        // $age                    
                        ]);
                        //$index +=1;
                    //}                    
                                
            }
        }

        $total = $modelIntership->loadListLoanPaymentIntership('','',$startdate,$enddate);

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($total)),
            "recordsFiltered" => intval(count($total)),
            "data"            => $results 
        );
           
        echo json_encode($json_data);
        exit;
    }


    //  dánh sach luat su tren 75 tuoi

    public function listlawyer75excelAction(){
        $search = $this->getRequest()->getParam('search');
        $this->_helper->layout('layout')->disableLayout();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $modelLawyer = new Default_Model_Lawyer();
        $dataLawyer75 = $modelLawyer->loadLawyer75YearOldByFilter();

        $results = array(            
        );

        $dateEmpty = '1900-01-01 00:00:00';
        //$currentDate = new Zend_Date();
        if($dataLawyer75 != null && sizeof($dataLawyer75)){
            foreach($dataLawyer75 as $law){ 
                if($law['cus_birthday'] != $dateEmpty && $law['cus_birthday'] != null && $law['cus_birthday'] != ''){
                    $age = (int)date('Y') - (int)date('Y',strtotime($law['cus_birthday']));         
                    
                    if($search != null && $search !='' && $age >= (int)$search){
                        $dateCertification = ($law['law_certification_createdate'] != null && 
                        $law['law_certification_createdate'] != '' && $law['law_certification_createdate'] != $dateEmpty) ? 
                        date('d/m/Y',strtotime($law['law_certification_createdate'])) : '';
                        
                        array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                        $law['cus_identity_card'],
                        $law['cus_birthday'] != '' && $law['cus_birthday'] != $dateEmpty ? date("d/m/Y", strtotime($law['cus_birthday'])) : '',
                        $law['cus_cellphone'],
                        $law['cus_lawyer_number'],
                        $law['law_certfication_no'],
                        $dateCertification,
                        $law['law_joining_number'],
                        $age,
                        $law['cus_address_resident'],
                        $law['cus_address_resident_now']                    
                        ]);
                    }                    
                }                
            }
        }
            //Khởi tạo đối tượng
        $excel = new Default_Model_Excel();
            //Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Luật sư tập sự');

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            //Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => '#000000'),
                ),
            ),
        );        
        
        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Tên');
        $excel->getActiveSheet()->setCellValue('B1', 'CMND');
        $excel->getActiveSheet()->setCellValue('C1', 'Ngày sinh');
        $excel->getActiveSheet()->setCellValue('D1', 'Số điện thoại');
        $excel->getActiveSheet()->setCellValue('E1', 'Số thẻ LS');
        $excel->getActiveSheet()->setCellValue('F1', 'Số chứng chỉ hành nghề');
        $excel->getActiveSheet()->setCellValue('G1', 'Ngày cấp số chứng chỉ hành nghề');
        $excel->getActiveSheet()->setCellValue('H1', 'Đợt gia nhập');
        $excel->getActiveSheet()->setCellValue('I1', 'Địa chỉ thường trú');
        $excel->getActiveSheet()->setCellValue('J1', 'Nơi ở hiện tại');
        $excel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleArray);
              

        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        $numRow = 2;
        if($results != null && sizeof($results) >0 ){   
            foreach($results as $row){
                $excel->getActiveSheet()->setCellValue('A'.$numRow, $row[0]);
                $excel->getActiveSheet()->setCellValue('B'.$numRow, $row[1]);
                $excel->getActiveSheet()->setCellValue('C'.$numRow, $row[2]);
                $excel->getActiveSheet()->setCellValue('D'.$numRow, $row[3]);
                $excel->getActiveSheet()->setCellValue('E'.$numRow, $row[4]);
                $excel->getActiveSheet()->setCellValue('F'.$numRow, $row[5]);
                $excel->getActiveSheet()->setCellValue('G'.$numRow, $row[6]);
                $excel->getActiveSheet()->setCellValue('H'.$numRow, $row[7]);
                $excel->getActiveSheet()->setCellValue('I'.$numRow, $row[9]);
                $excel->getActiveSheet()->setCellValue('I'.$numRow, $row[10]);

                $numRow++;
            }     
        }   
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_Hon75Tuoi.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;   

    }

    public function listlaywer75datatableAction(){

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');

        $modelLawyer = new Default_Model_Lawyer();
        $dataLawyer75 = $modelLawyer->loadLawyer75YearOldByFilter();

        $results = array(            
        );

        $dateEmpty = '1900-01-01 00:00:00';
        $index = 0;
        //$currentDate = new Zend_Date();
        if($dataLawyer75 != null && sizeof($dataLawyer75)){
            foreach($dataLawyer75 as $law){ 
                if($law['cus_birthday'] != $dateEmpty && $law['cus_birthday'] != null && $law['cus_birthday'] != ''){
                    $age = (int)date('Y') - (int)date('Y',strtotime($law['cus_birthday']));         
                    
                    if($search!= null && $search != '' && $age >= (int)$search){
                        $dateCertification = ($law['law_certification_createdate'] != null && 
                        $law['law_certification_createdate'] != '' && $law['law_certification_createdate'] != $dateEmpty) ? 
                        date('d/m/Y',strtotime($law['law_certification_createdate'])) : '';
                        
                        array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                        $law['cus_identity_card'],
                        ($law['cus_birthday'] != null && $law['cus_birthday'] != '' && 
                        $law['cus_birthday'] != $dateEmpty) ? date('d/m/Y', strtotime($law['cus_birthday'])) : '',
                        $law['cus_cellphone'],
                        $law['cus_lawyer_number'],
                        ($law['cus_date_lawyer_number'] != null && $law['cus_date_lawyer_number'] != '' && 
                        $law['cus_date_lawyer_number'] != $dateEmpty) ? date('d/m/Y', strtotime($law['cus_date_lawyer_number'])) : '',
                        $law['law_certfication_no'],
                        $dateCertification,
                        $law['law_joining_number'],
                        $age                    
                        ]);
                        $index +=1;
                    }                    
                }                
            }
        }

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval($index),
            "recordsFiltered" => intval($index),
            "data"            => ($results != null && sizeof($results) > 0) ? array_slice($results,$start,$length) : 0
        );
           
        echo json_encode($json_data);
        exit;
    }

    public function listlawyer75Action(){
       
    }

    //danh sach luat su hanh nghe hon 15 nam
    public function listlawyer15excelAction(){

        $search = $this->getRequest()->getParam('search');

        $this->_helper->layout('layout')->disableLayout();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $modelCustomer = new Default_Model_Customers();
        $modelLawyer = new Default_Model_Lawyer();
        $dataLawyer15 = $modelLawyer->loadLawyer15YearCertificationByFilter();

        $results = array(            
        );

        $dateEmpty = '1900-01-01 00:00:00';
        //$currentDate = new Zend_Date();
        if($dataLawyer15 != null && sizeof($dataLawyer15)){
            foreach($dataLawyer15 as $law){ 
                if($law['cus_date_lawyer_number'] != $dateEmpty && $law['cus_date_lawyer_number'] != null && $law['cus_date_lawyer_number'] != ''){
                    $age = (int)date('Y') - (int)date('Y',strtotime($law['cus_date_lawyer_number']));         
                    
                    if($search != null && $search !='' && $age >= (int)$search){
                        $endmonth = $modelCustomer->getEndMonthByCusId($law['cus_id']);

                        $dateCertification = ($law['law_certification_createdate'] != null && 
                        $law['law_certification_createdate'] != '' && $law['law_certification_createdate'] != $dateEmpty) ? 
                        date('d/m/Y',strtotime($law['law_certification_createdate'])) : '';
                        
                        array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                        $law['cus_identity_card'],
                        $law['cus_cellphone'],
                        $law['cus_lawyer_number'],
                        $law['law_certfication_no'],
                        $dateCertification,
                        $law['law_joining_number'],
                        $age,
                        $endmonth
                        ]);
                    }                    
                }                
            }
        }
            //Khởi tạo đối tượng
        $excel = new Default_Model_Excel();
            //Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Luật sư tập sự');

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            //Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => '#000000'),
                ),
            ),
        );        
        
        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Tên');
        $excel->getActiveSheet()->setCellValue('B1', 'CMND');
        $excel->getActiveSheet()->setCellValue('C1', 'Số điện thoại');
        $excel->getActiveSheet()->setCellValue('D1', 'Số thẻ LS');
        $excel->getActiveSheet()->setCellValue('E1', 'Số chứng chỉ hành nghề');
        $excel->getActiveSheet()->setCellValue('F1', 'Ngày cấp số chứng chỉ hành nghề');
        $excel->getActiveSheet()->setCellValue('G1', 'Đợt gia nhập');
        $excel->getActiveSheet()->setCellValue('H1', 'Đóng phí đến');
        $excel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleArray);
              

        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        $numRow = 2;
        if($results != null && sizeof($results) >0 ){       
            foreach($results as $row){
                $excel->getActiveSheet()->setCellValue('A'.$numRow, $row[0]);
                $excel->getActiveSheet()->setCellValue('B'.$numRow, $row[1]);
                $excel->getActiveSheet()->setCellValue('C'.$numRow, $row[2]);
                $excel->getActiveSheet()->setCellValue('D'.$numRow, $row[3]);
                $excel->getActiveSheet()->setCellValue('E'.$numRow, $row[4]);
                $excel->getActiveSheet()->setCellValue('F'.$numRow, $row[5]);
                $excel->getActiveSheet()->setCellValue('G'.$numRow, $row[6]);
                $excel->getActiveSheet()->setCellValue('H'.$numRow, $row[8]);

                $numRow++;
            }  
        }      
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_Hon15NamHanhNghe.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;   

    }

    public function listlawyer15datatableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');

        $modelLawyer = new Default_Model_Lawyer();
        $dataLawyer15 = $modelLawyer->loadLawyer15YearCertificationByFilter();

        $results = array(            
        );

        $dateEmpty = '1900-01-01 00:00:00';
        $index = 0;
        //$currentDate = new Zend_Date();
        if($dataLawyer15 != null && sizeof($dataLawyer15)){
            foreach($dataLawyer15 as $law){ 
                if($law['cus_date_lawyer_number'] != $dateEmpty && $law['cus_date_lawyer_number'] != null && $law['cus_date_lawyer_number'] != ''){
                    $age = (int)date('Y') - (int)date('Y',strtotime($law['cus_date_lawyer_number']));         
                    
                    if($search!= null && $search != '' && $age >= (int)$search){
                        $dateCertification = ($law['law_certification_createdate'] != null && 
                        $law['law_certification_createdate'] != '' && $law['law_certification_createdate'] != $dateEmpty) ? 
                        date('d/m/Y',strtotime($law['law_certification_createdate'])) : '';
                        
                        array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                        $law['cus_identity_card'],
                        $law['cus_cellphone'],
                        $law['cus_lawyer_number'],
                        ($law['cus_date_lawyer_number'] != null && $law['cus_date_lawyer_number'] != '' && 
                        $law['cus_date_lawyer_number'] != $dateEmpty) ? date('d/m/Y', strtotime($law['cus_date_lawyer_number'])) : '',
                        $law['law_certfication_no'],
                        $dateCertification,
                        $law['law_joining_number'],
                        $age                    
                        ]);
                        $index +=1;
                    }                    
                }                
            }
        }
        // $this->view->dataLawyer75 = $results;
        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval($index),
            "recordsFiltered" => intval($index),
            "data"            => ($results != null && sizeof($results) > 0) ? array_slice($results,$start,$length) : 0
        );
           
        echo json_encode($json_data);
        exit;
    }
   
    public function listlawyer15Action(){ 
    }




     //danh sach luật sư tham gia bồi dưỡng 
    public function listpersontrainedAction(){

    }

    //function load danh sách luật sư đóng phí thành viên
    public function reportlawyerfeedatatableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');        
        //$year = $this->getRequest()->getParam('year');
        $type = $this->getRequest()->getParam('type');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $results = array(            
        );

        //offline
        $model = new Default_Model_PaymentLawyerOffline();
        
        if($type == 'offline'){  
            $index = 0;         
            $data = $model->loadPaymentLawyerByFilterStatistic($start, $length,$startdate,$enddate,'offline');
            if($data != null && sizeof($data)){            
                foreach($data as $pay){ 
                    $index += 1;
                    //$time=strtotime($pay['payment_lawyer_off_created_date']);
                    //$month=date("m/Y",$time);
                    
                    // $duration = $pay['month'];
                    // $text = "+".$pay['month']." months";
                    //$effectiveMonth = date('m/Y', strtotime($text, strtotime($pay['payment_lawyer_off_created_date'])));
                    
                    array_push($results,[
                        $index,
                        $pay['cus_firstname'].' '.$pay['cus_lastname'],                
                        date("d/m/Y", strtotime($pay['cus_birthday'])),
                        $pay['cus_lawyer_number'],
                        $pay['month'],
                        number_format($pay['amount']*0.5),
                        //$pay['month'] != null ? round($pay['amount']/$pay['month']) : $pay['amount'] ,
                        $pay['payment_lawyer_off_code'],
                        number_format($pay['amount']),
                        $pay['startedmonth'] != null ? $pay['startedmonth'] : '',
                        $pay['endmonth'] != null ? $pay['endmonth'] : '',
                        //$month,
                        //$effectiveMonth,
                        'Đóng tại đoàn'     
                    ]);                 
                }
            }
        }


        //online
        if($type == 'online'){
            $index = 0;
            //$model = new Default_Model_PaymentLawyerOffline();
            $data = $model->loadPaymentLawyerByFilterStatistic($start, $length,$startdate,$enddate,'online');
            if($data != null && sizeof($data)){            
                foreach($data as $pay){ 
                    $index += 1;
                    // $time=strtotime($pay['payment_lawyer_off_created_date']);
                    // $month=date("m/Y",$time);
                    
                    // $duration = $pay['month'];
                    // $text = "+".$pay['month']." months";
                    // $effectiveMonth = date('m/Y', strtotime($text, strtotime($pay['payment_lawyer_off_created_date'])));
                    
                    array_push($results,[
                        $index,
                        $pay['cus_firstname'].' '.$pay['cus_lastname'],                
                        date("d/m/Y", strtotime($pay['cus_birthday'])),
                        $pay['cus_lawyer_number'],
                        $pay['month'],
                        number_format($pay['amount']*0.5),
                        //$pay['month'] != null ? round($pay['amount']/$pay['month']) : $pay['amount'] ,
                        $pay['payment_lawyer_off_code'],
                        number_format($pay['amount']),
                        // $month,
                        // $effectiveMonth,
                        $pay['startedmonth'] != null ? $pay['startedmonth'] : '',
                        $pay['endmonth'] != null ? $pay['endmonth'] : '',
                        'Đóng qua mạng'     
                    ]);                 
                }
            }
        }

        //cả 2
        if($type == 'all'){
            //offline
            //$model = new Default_Model_PaymentLawyerOffline();
            $index = 0;
            $data = $model->loadPaymentLawyerByFilterStatistic($start, $length,$startdate,$enddate,'all');
            if($data != null && sizeof($data)){            
                foreach($data as $pay){ 
                    $index += 1;
                    // $time=strtotime($pay['payment_lawyer_off_created_date']);
                    // $month=date("m/Y",$time);
                    // //$month=date("m/Y",$pay['startedmonth']);
                    // $duration = $pay['month'];
                    // $text = "+".$pay['month']." months";
                    // $effectiveMonth = date('m/Y', strtotime($text, strtotime($pay['payment_lawyer_off_created_date'])));
                    
                    $text = '';
                    if($pay['payment_type'] == 'online'){
                        $text = 'Đóng qua mạng';
                    }else{
                        $text = 'Đóng tại đoàn';
                    }
                    array_push($results,[
                        $index,
                        $pay['cus_firstname'].' '.$pay['cus_lastname'],                
                        date("d/m/Y", strtotime($pay['cus_birthday'])),
                        $pay['cus_lawyer_number'],
                        $pay['month'],
                        number_format($pay['amount']*0.5),
                        //$pay['month'] != null ? round($pay['amount']/$pay['month']) : $pay['amount'] ,
                        $pay['payment_lawyer_off_code'],
                        number_format($pay['amount']),
                        $pay['startedmonth'] != null ? $pay['startedmonth'] : '',
                        $pay['endmonth'] != null ? $pay['endmonth'] : '',
                        // $pay['startedmonth'],//$month,
                        // $pay['endmonth'],//$effectiveMonth,
                        $text     
                    ]);                 
                }
            }
        }
        //$totalrecords = $model->countPaymentLawyerByFilter($start,$length,$search,$year);
        // echo "<prev>";
        //     print_r($data);
        // echo "</prev>";
        // exit;

        $total = 0;
        if($type == 'online'){
            $total = $model->loadPaymentLawyerByFilterStatisticTotals($startdate,$enddate,'online');            
        }else if($type == 'offline'){
            $total = $model->loadPaymentLawyerByFilterStatisticTotals($startdate,$enddate,'offline');
        }else{
            $total = $model->loadPaymentLawyerByFilterStatisticTotals($startdate,$enddate,'all');
        }

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval($total),
            "recordsFiltered" => intval($total),
            "data"            => $results
        );
        //$cattraining = new Default_Model_CategoryTraining();
        
        echo json_encode($json_data);
        exit;
    }

    function countHoursForLeaner($data,$hours){
        //prapre data
        $results = array();
        $resultsTime = array();
        if($data != null && sizeof($data)){                
            foreach($data as $d){ 
                array_push($results,                              
                        $d['cus_id']                  
                );
            }
        }
      
        if($results != null && sizeof($results)){
            //$array = array(5, 5, 2, 1);
            $counts = array_count_values($results); // Array(5 => 2, 2 => 1, 1 => 1)
            foreach ($counts as $key => $val) {
                if($hours != null){
                    if($hours == '8'){
                        if($val == 1){
                            array_push($resultsTime,                               
                                $key                  
                            );
                        }
                    
                    }else{
                        if($val >= 2){
                            array_push($resultsTime,                               
                                $key                  
                            );
                        }
                    }
                }else{
                    array_push($resultsTime,                               
                        $key                  
                    );
                }
                    
            }
        }

        return $resultsTime;
    }


    //load danh sách luật sư bồi dưỡng theo giờ và theo năm

    public function reportlawyertrainingbyhoursdatatableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');  
        $hours = $this->getRequest()->getParam('hours'); 

        $model = new Default_Model_PaymentTrainingOffline();
        //$modelOnline = new Default_Model_PaymentTrainingOnline();
        $data = $model->loadLawyerTrainedByHoursFilter($start,$length,$search,null);   
        $resultsFinal = array(            
        );

        $idsFinal = array(            
        );
        $idsFinal = $this->countHoursForLeaner($data,$hours);
        $index = 0;
        $dateEmpty = '1900-01-01 00:00:00';
        if($hours == '8'){
            $data = $model->loadLawyerTrainedByHoursFilter($start,$length,$search,$idsFinal);   
            if($data != null && sizeof($data)){
                
                foreach($data as $pay){ 
                    $index += 1;  
                                  
                    array_push($resultsFinal,[
                    $index,
                    $pay['cus_firstname'].' '.$pay['cus_lastname'],              
                    $pay['cus_identity_card'], 
                    $pay['cus_cellphone'],  
                    $pay['cus_birthday'] != $dateEmpty ? date("d/m/Y", strtotime($pay['cus_birthday'])) : '',      
                    $pay['cus_lawyer_number'] == null ? '' :$pay['cus_lawyer_number'],
                    $pay['cus_date_lawyer_number'] != $dateEmpty ? date("d/m/Y", strtotime($pay['cus_date_lawyer_number'])) : '',
                    $pay['cus_address_resident'],
                    $pay['cus_address_resident_now']                                         
                    ]);                 
                }
            }

        }
       

        if($hours == '16'){
            $data = $model->loadLawyerTrainedByHoursFilter($start,$length,$search,$idsFinal);       
            if($data != null && sizeof($data)){
                $index = 0;
                foreach($data as $pay){ 
                    $index += 1;  
                                  
                    array_push($resultsFinal,[
                    $index,
                    $pay['cus_firstname'].' '.$pay['cus_lastname'],              
                    $pay['cus_identity_card'], 
                    $pay['cus_cellphone'],  
                    date("d/m/Y", strtotime($pay['cus_birthday'])),      
                    $pay['cus_lawyer_number'] == null ? '' :$pay['cus_lawyer_number']
                           
                    ]);                 
                }
            }

        }
        
        if($hours == 'all'){
            //online    
            $data = $model->loadLawyerTrainedByHoursFilter($start,$length,$search,$idsFinal);       
            if($data != null && sizeof($data)){
                $index = 0;
                foreach($data as $pay){ 
                    $index += 1;  

                    $type = '';
                    if($pay['payment_type'] == 'offline'){
                        $type = 'Đóng tại đoàn';
                    }else{
                        $type = 'Đóng qua mạng';
                    }
                                  
                    array_push($resultsFinal,[
                    $index,
                    $pay['cus_firstname'].' '.$pay['cus_lastname'],              
                    $pay['cus_identity_card'], 
                    $pay['cus_cellphone'],  
                    date("d/m/Y", strtotime($pay['cus_birthday'])),      
                    $pay['cus_lawyer_number'] == null ? '' :$pay['cus_lawyer_number']
                    ]);                 
                }
            }
       }

        //$totalrecords = $model->loadLawyerWithoutTrainingByFilter($start,$length,$results);

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($resultsFinal)),
            "recordsFiltered" => intval(count($resultsFinal)),
            "data"            => $resultsFinal
        );
     
        echo json_encode($json_data);
        exit;
    }

    // function load danh sách luật sư không tham gia bồi dưỡng
    //works thông kê theo năm
    public function reportlawyertrainingdatatableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');  
        $type = $this->getRequest()->getParam('type');  
 

        $model = new Default_Model_PaymentTrainingOffline();
        //$modelOnline = new Default_Model_PaymentTrainingOnline();
        $resultsFinal = array(            
        );
        $index = 0;
        $dateEmpty = '1900-01-01 00:00:00';
        if($type == 'offline'){
            $data = $model->loadLawyerTrainedByFilter($start,$length,$search,'offline');       
            if($data != null && sizeof($data)){
                
                foreach($data as $pay){ 
                    $index += 1;  
                                  
                    array_push($resultsFinal,[
                    $index,
                    $pay['cus_firstname'].' '.$pay['cus_lastname'],              
                    $pay['cus_identity_card'], 
                    $pay['cus_cellphone'],  
                    date("d/m/Y", strtotime($pay['cus_birthday'])),      
                    $pay['cus_lawyer_number'],
                    $pay['cus_date_lawyer_number'] != $dateEmpty ? date("d/m/Y", strtotime($pay['cus_date_lawyer_number'])) : '',           
                    $pay['cus_address_resident'],
                    $pay['cus_address_resident_now'],
                    date("d/m/Y", strtotime($pay['payment_training_off_created_date'])),
                    number_format($pay['amount']),
                    'Đóng tại đoàn'          
                    ]);                 
                }
            }

        }
       

        if($type == 'online'){
            $data = $model->loadLawyerTrainedByFilter($start,$length,$search,'online');       
            if($data != null && sizeof($data)){
                $index = 0;
                foreach($data as $pay){ 
                    $index += 1;  
                                  
                    array_push($resultsFinal,[
                    $index,
                    $pay['cus_firstname'].' '.$pay['cus_lastname'],              
                    $pay['cus_identity_card'], 
                    $pay['cus_cellphone'],  
                    date("d/m/Y", strtotime($pay['cus_birthday'])),      
                    $pay['cus_lawyer_number'],
                    $pay['cus_date_lawyer_number'] != $dateEmpty ? date("d/m/Y", strtotime($pay['cus_date_lawyer_number'])) : '',          
                    $pay['cus_address_resident'],
                    $pay['cus_address_resident_now'],
                    date("d/m/Y", strtotime($pay['payment_training_off_created_date'])),
                    number_format($pay['amount']),
                    'Đóng online'          
                    ]);                 
                }
            }

        }
        
        if($type == 'all'){
            //online    
            $data = $model->loadLawyerTrainedByFilter($start,$length,$search,'');       
           
            if($data != null && sizeof($data)){
                $index = 0;
                foreach($data as $pay){ 
                    $index += 1;  

                    $type = '';
                    if($pay['payment_type'] == 'offline'){
                        $type = 'Đóng tại đoàn';
                    }else{
                        $type = 'Đóng qua mạng';
                    }
                                  
                    array_push($resultsFinal,[
                    $index,
                    $pay['cus_firstname'].' '.$pay['cus_lastname'],              
                    $pay['cus_identity_card'], 
                    $pay['cus_cellphone'],  
                    date("d/m/Y", strtotime($pay['cus_birthday'])),      
                    $pay['cus_lawyer_number'],
                    $pay['cus_address_resident'],
                    $pay['cus_address_resident_now'],
                    $pay['cus_date_lawyer_number'] != $dateEmpty ? date("d/m/Y", strtotime($pay['cus_date_lawyer_number'])) : '',          
                    date("d/m/Y", strtotime($pay['payment_training_off_created_date'])),
                    number_format($pay['amount']),
                    $type       
                    ]);                 
                }
            }

            // $dataOffline = $model->loadLawyerTrainedByFilter($start,$length,$search);       
            // if($dataOffline != null && sizeof($dataOffline)){
                
            //     foreach($dataOffline as $pay){ 
            //         $index += 1;  
                                  
            //         array_push($resultsFinal,[
            //         $index,
            //         $pay['cus_firstname'].' '.$pay['cus_lastname'],              
            //         $pay['cus_identity_card'], 
            //         $pay['cus_cellphone'],  
            //         date("d/m/Y", strtotime($pay['cus_birthday'])),      
            //         //$pay['law_joining_number'],
            //         //$pay['law_code'],           
            //         date("d/m/Y", strtotime($pay['payment_training_off_created_date'])),
            //         $pay['amount'],
            //         'Đóng tại đoàn'          
            //         ]);                 
            //     }
            // }

        }

        //$totalrecords = $model->loadLawyerWithoutTrainingByFilter($start,$length,$results);

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($resultsFinal)),
            "recordsFiltered" => intval(count($resultsFinal)),
            "data"            => $resultsFinal
        );
     
        echo json_encode($json_data);
        exit;
    }

    // function load danh sách luật sư không tham gia bồi dưỡng
    //works thông kê theo  ngày bắt đầu ngày kết thúc
    public function reportlawyertrainingintimedatatableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');  
        $type = $this->getRequest()->getParam('type');  
        $startdate = $this->getRequest()->getParam('startdate');  
        $enddate = $this->getRequest()->getParam('enddate');        
        

        $model = new Default_Model_PaymentTrainingOffline();
        //$modelOnline = new Default_Model_PaymentTrainingOnline();
        $resultsFinal = array(            
        );
        $index = 0;

        if($type == 'offline'){
            $data = $model->loadLawyerTrainingByFilter($start,$length,'offline',$startdate,$enddate);       
            if($data != null && sizeof($data)){
                
                foreach($data as $pay){ 
                    $index += 1;  
                                  
                    array_push($resultsFinal,[
                        $index,
                        $pay['cus_firstname'].' '.$pay['cus_lastname'],  
                        date("d/m/Y", strtotime($pay['cus_birthday'])),             
                        $pay['cus_identity_card'], 
                        $pay['law_joining_number'] != null ? $pay['law_joining_number'] : "",
                        '',
                        $pay['cus_cellphone'],                   
                        $pay['cus_lawyer_number'],          
                        date("d/m/Y", strtotime($pay['payment_training_off_created_date'])),
                        number_format($pay['amount']),
                        'Đóng tại đoàn'          
                    ]);                 
                }
            }

        }
       

        if($type == 'online'){
            $data = $model->loadLawyerTrainingByFilter($start,$length,'online',$startdate,$enddate);
            if($data != null && sizeof($data)){
                $index = 0;
                foreach($data as $pay){ 
                    $index += 1;  
                                  
                    array_push($resultsFinal,[
                    $index,
                    $pay['cus_firstname'].' '.$pay['cus_lastname'],  
                    date("d/m/Y", strtotime($pay['cus_birthday'])),             
                    $pay['cus_identity_card'], 
                    $pay['law_joining_number'] != null ? $pay['law_joining_number'] : "",
                    '',
                    $pay['cus_cellphone'],                   
                    $pay['cus_lawyer_number'],          
                    date("d/m/Y", strtotime($pay['payment_training_off_created_date'])),
                    number_format($pay['amount']),
                    'Đóng online'          
                    ]);                 
                }
            }

        }
        
        if($type == 'all'){
            //online    
            $data = $model->loadLawyerTrainingByFilter($start,$length,'all',$startdate,$enddate);
            if($data != null && sizeof($data)){
                $index = 0;
                foreach($data as $pay){ 
                    $index += 1;  

                    $type = '';
                    if($pay['payment_type'] == 'offline'){
                        $type = 'Đóng tại đoàn';
                    }else{
                        $type = 'Đóng qua mạng';
                    }
                                  
                    array_push($resultsFinal,[
                    $index,
                    $pay['cus_firstname'].' '.$pay['cus_lastname'],  
                    date("d/m/Y", strtotime($pay['cus_birthday'])),             
                    $pay['cus_identity_card'], 
                    $pay['law_joining_number'] != null ? $pay['law_joining_number'] : "",
                    '',
                    $pay['cus_cellphone'],                   
                    $pay['cus_lawyer_number'],          
                    date("d/m/Y", strtotime($pay['payment_training_off_created_date'])),
                    number_format($pay['amount']),
                    $type       
                    ]);                 
                }
            }

        }

        $totalrecords = $model->loadLawyerTrainingByFilterTotals($type, $startdate, $enddate);

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($totalrecords)),
            "recordsFiltered" => intval(count($totalrecords)),
            "data"            => $resultsFinal
        );
     
        echo json_encode($json_data);
        exit;
    }

     //dách sách luật sư gia nhập
     public function listjoiningAction(){
         
     }

     public function listjoiningdatatableAction(){

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');        

        $model = new Default_Model_PaymentJoiningOffline();
        $data = $model->loadPaymentJoiningOfflineByFilter($start,$length,$search);

        $results = array(            
        );
        $dateEmpty = '1900-01-01 00:00:00';
        if($data != null && sizeof($data)){
            foreach($data as $pay){ 
                array_push($results,[
      
                $pay['cus_firstname'].' '.$pay['cus_lastname'],
                $pay['cus_identity_card'],
                $pay['payment_joining_off_code'],
                $pay['cus_lawyer_number'],
                ($pay['cus_date_lawyer_number'] != null && $pay['cus_date_lawyer_number'] !='' &&
                $pay['cus_date_lawyer_number'] != $dateEmpty) ? date('d/m/Y', strtotime($pay['cus_date_lawyer_number'])) : '',
                $pay['law_certfication_no'],
                ($pay['law_certification_createdate'] != null && $pay['law_certification_createdate'] !='' &&
                $pay['law_certification_createdate'] != $dateEmpty) ? date('d/m/Y', strtotime($pay['law_certification_createdate'])) : '',
                $pay['cus_address_resident'],
                $pay['cus_address_resident_now'],
                ($pay['payment_joining_off_created_date'] != null && $pay['payment_joining_off_created_date'] !='' &&
                $pay['payment_joining_off_created_date'] != $dateEmpty) ? date('d/m/Y', strtotime($pay['payment_joining_off_created_date'])) : '',
                number_format($pay['amount'])              
                ]);                 
            }
        }

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($results)),
            "recordsFiltered" => intval(count($results)),
            "data"            => $results
        );  
     
        echo json_encode($json_data);
        exit;

     }

    public function listpaymentjoiningdatatableAction(){

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $type = $this->getRequest()->getParam('type');  
        $startdate = $this->getRequest()->getParam('startdate');  
        $enddate = $this->getRequest()->getParam('enddate'); 

        $model = new Default_Model_PaymentJoiningOffline();
        $data = $model->loadPaymentJoiningOfflineStatisticByFilter($start,
        $length,$type,$startdate,$enddate);

        $results = array(            
        );

        $dateEmpty = '1900-01-01 00:00:00';

        if($data != null && sizeof($data)){
            $index = 0;
            foreach($data as $pay){        
                $index += 1;              
                $dateToShow = ($pay['payment_joining_off_created_date'] != null && $pay['payment_joining_off_created_date'] != ''
                && $pay['payment_joining_off_created_date'] != $dateEmpty) ? date('d/m/Y',strtotime($pay['payment_joining_off_created_date'])) : '';           
                array_push($results,[
                    $index,
                $pay['cus_firstname'].' '.$pay['cus_lastname'],
                $pay['cus_identity_card'],
                $pay['cus_cellphone'],
                $pay['payment_joining_off_code'],
                $dateToShow,
                number_format($pay['amount']),              
                $pay['payment_type'] == 'offline' ? 'Đóng tại đoàn' : 'Đóng qua mạng'
                ]); 
           
            }
        }

        $total = $model->loadPaymentJoiningOfflineStatisticByFilter('','',$type,$startdate,$enddate);
        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($total)),
            "recordsFiltered" => intval(count($total)),
            "data"            => $results
        );  
     
        echo json_encode($json_data);
        exit;

     }

     //danh sách luật sư của tổ chức
     public function listlawyeroforganAction(){
        $model = new Default_Model_OrganizationLawDetails();
        $data = $model->loadOrganzationsLaw();
        $this->view->organizations = $data;
     }

     public function deletedslsAction(){
        $ids = $this->getRequest()->getParam('dsls');
        $model = new Default_Model_OrganizationLawyer();
        if($ids != null && sizeof($ids) > 0 ){
            foreach($ids as $i){
                $where = $model->getAdapter()->quoteInto('organ_lawyer_id = ?',$i);
                $model->delete($where);               
            }
        }
        
     }

     //danh sách luật sư tổ chức datatable
     public function listlawyeroforgandatatableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');        

        $model = new Default_Model_OrganizationLawyer();
        $data = $model->loadOrganizationLawyerByFilter($start,$length,$search);

        $results = array(            
        );
        
        $dateEmpty = '1900-01-01 00:00:00';
        if($data != null && sizeof($data)){
            foreach($data as $pay){ 
                array_push($results,[  
                $pay['organ_lawyer_id'],                      
                $pay['cus_firstname'].' '.$pay['cus_lastname'],
                $pay['cus_identity_card'],
                $pay['cus_lawyer_number'],
                ($pay['cus_date_lawyer_number'] != null && $pay['cus_date_lawyer_number'] != '' &&
                $pay['cus_date_lawyer_number'] != $dateEmpty) ? date('d/m/Y', strtotime($pay['cus_date_lawyer_number'])) : '',
                $pay['law_certfication_no'],
                ($pay['law_certification_createdate'] != null && $pay['law_certification_createdate'] != '' &&
                $pay['law_certification_createdate'] != $dateEmpty) ? date('d/m/Y', strtotime($pay['law_certification_createdate'])) : '',
                $pay['cus_address_resident'],
                $pay['cus_address_resident_now'],
                ($pay['cus_birthday'] != '' && $pay['cus_birthday'] != null && 
                 $pay['cus_birthday'] != $dateEmpty) ? date('d/m/Y', strtotime($pay['cus_birthday'])) : '',
                'Việt Nam',
                $pay['organ_name']
                // date('d/m/Y', strtotime($pay['law_joining_organ_date']))               
                //''
                ]);                 
            }
        }


        $totalrecords = $model->countLawyerActiveByFilter($start,$length,$search);

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

    // danh sashc luật sư không tham gia traning
     public function listtraineddatatableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');        

        $payment = new Default_Model_PaymentTrainingOffline();
        $data = $payment->loadPaymentLawyerOfflineByFilter($start,$length,$search);

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

                $values  = $pay['payment_training_off_code'];
                $value = explode("_", $values);

                array_push($results,[
                    //$pay['payment_training_off_id']
                $pay['cus_firstname'].' '.$pay['cus_lastname'],
                $pay['cus_identity_card'],
                $pay['cus_lawyer_number'],
                $pay['cus_date_lawyer_number'] != $dateEmpty ? date('d/m/Y', strtotime($pay['cus_date_lawyer_number'])) : '',
                $pay['cus_address_resident'],
                $pay['cus_address_resident_now'],
                $pay['payment_training_off_code'],
                $pay['cat_train_name'],
                $pay['cat_train_number']
                //,
                // $value[0]
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

     // danh sashc luật sư không tham gia traning
     public function listtrainedAction(){
        $categorytraining = new Default_Model_CategoryTraining();         
        $data = $categorytraining->loadCategoryTrainingActive();
        $this->view->categoriestrainings =  $data;
     }
   

     public function activelawyerandintershipAction(){
         
     }

     public function listactivelawyerAction(){
       
     }


     /*load dữ liệu cho table trong page list active lawyer*/
     public function listactivelawyerdatatableAction(){

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $lawyer = new Default_Model_Lawyer();
        $dataactivelawyer = $lawyer->loadLawyerActiveByFilter($search,$start,$length,$startdate,$enddate,'1');

        $results = array(            
        );
        $dateEmpty = '1900-01-01 00:00:00';
        if($dataactivelawyer != null && sizeof($dataactivelawyer)){
            foreach($dataactivelawyer as $law){ 
                // if($law['cus_sex'] == 'Nam'){
                //     $text = 'Nam';
                //  }else{
                //     $text = 'Nữ';  
                //  }                   
                array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                $law['cus_sex'],
                $law['cus_identity_card'],
                $law['cus_cellphone'],
                $law['cus_lawyer_number'],
                ($law['cus_date_lawyer_number'] != null && $law['cus_date_lawyer_number'] != '' && 
                $law['cus_date_lawyer_number'] != $dateEmpty) ? date('d/m/Y', strtotime($law['cus_date_lawyer_number'])) : '',
                $law['law_certfication_no'],
                ($law['law_certification_createdate'] != null && $law['law_certification_createdate'] != '' && 
                $law['law_certification_createdate'] != $dateEmpty) ? date('d/m/Y', strtotime($law['law_certification_createdate'])) : '',
                $law['cus_address_resident'],
                $law['cus_address_resident_now'],
                $law['organ_name'],
                $law['law_type'],
                'Đang hoạt động' 
                ]); 
            }
        }


        $totalrecords = $lawyer->countLawyerActiveByFilter('1');

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


     /* xuat excel ds luat su hoat dong*/
     public function activelawyerAction() {
        
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');
        
        $this->_helper->layout('layout')->disableLayout();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $lawyer = new Default_Model_Lawyer();
        //$data = $lawyer->loadLawyerActive();
        $data = $lawyer->loadLawyerActiveByFilter($search,$start,$length,$startdate,$enddate,'1');
            //Khởi tạo đối tượng
        $excel = new Default_Model_Excel();
            //Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Luật sư đang hoạt động');

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            //Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Tên');
        $excel->getActiveSheet()->setCellValue('B1', 'Giới Tính');
        $excel->getActiveSheet()->setCellValue('C1', 'CMND');
        $excel->getActiveSheet()->setCellValue('D1', 'Số Điện Thoại');
        $excel->getActiveSheet()->setCellValue('E1', 'Mã số luật sư');
        $excel->getActiveSheet()->setCellValue('F1', 'Số chứng chỉ hành nghề');
        $excel->getActiveSheet()->setCellValue('G1', 'Tổ chức hành nghề');
        $excel->getActiveSheet()->setCellValue('H1', 'Loại luật sư');
        $excel->getActiveSheet()->setCellValue('I1', 'Tình trạng luật sư');
        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        $numRow = 2;
        foreach($data as $row){
            $excel->getActiveSheet()->setCellValue('A'.$numRow, $row['cus_firstname'].' '.$row['cus_lastname']);
            //if($row['cus_sex'] == 'nam'){
                $excel->getActiveSheet()->setCellValue('B'.$numRow, $row['cus_sex']);    
            //}else{
            //    $excel->getActiveSheet()->setCellValue('B'.$numRow, 'Nữ');    
            //}
                
            $excel->getActiveSheet()->setCellValue('C'.$numRow, $row['cus_identity_card']);
            $excel->getActiveSheet()->setCellValue('D'.$numRow, $row['cus_cellphone']);
            $excel->getActiveSheet()->setCellValue('E'.$numRow, $row['cus_lawyer_number']);
            $excel->getActiveSheet()->setCellValue('F'.$numRow, $row['law_certfication_no']);
            $excel->getActiveSheet()->setCellValue('G'.$numRow, $row['organ_name']);
            $excel->getActiveSheet()->setCellValue('H'.$numRow, $row['law_type']);
            $excel->getActiveSheet()->setCellValue('I'.$numRow, 'Đang hoạt động');
            $numRow++;
        }
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_DangHoatDong.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;
     }

     public function listmoveorgAction(){     

     }

    /*load dữ liệu cho table trong page list active lawyer*/
    public function listmoveorgdatatableAction(){

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $lawyer = new Default_Model_Lawyer();
        $dataactivelawyer = $data = $lawyer->loadLawyerActiveByFilter($search,$start,$length,$startdate,$enddate,'3');

        $results = array(            
        );
        $dateEmpty = '1900-01-01 00:00:00';
        if($dataactivelawyer != null && sizeof($dataactivelawyer)){
            foreach($dataactivelawyer as $law){ 
                // if($law['cus_sex'] == 'nam'){
                //     $text = 'Nam';
                //     }else{
                //     $text = 'Nữ';  
                //     }                   
                array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                $law['cus_sex'],
                $law['cus_identity_card'],
                $law['cus_cellphone'],
                $law['cus_lawyer_number'],
                ($law['cus_date_lawyer_number'] != null && $law['cus_date_lawyer_number'] != '' && 
                $law['cus_date_lawyer_number'] != $dateEmpty) ? date('d/m/Y', strtotime($law['cus_date_lawyer_number'])) : '',
                $law['law_certfication_no'],
                ($law['law_certification_createdate'] != null && $law['law_certification_createdate'] != '' && 
                $law['law_certification_createdate'] != $dateEmpty) ? date('d/m/Y', strtotime($law['law_certification_createdate'])) : '',
                $law['cus_address_resident'],
                $law['cus_address_resident_now'],
                $law['organ_name'],
                $law['law_type'],
                'Chuyển đoàn' 
                ]); 
            }
        }


        $totalrecords = $lawyer->countLawyerActiveByFilter('3');

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

     //danh sách luật sư chuyển đoàn xuất excel
     public function moveorgAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $this->_helper->layout('layout')->disableLayout();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $lawyer = new Default_Model_Lawyer();
        $data = $lawyer->loadLawyerActiveByFilter($search,$start,$length,$startdate,$enddate,'3');
            //Khởi tạo đối tượng
        $excel = new Default_Model_Excel();
            //Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Luật sư chuyển đoàn');

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            //Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        
        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Tên');
        $excel->getActiveSheet()->setCellValue('B1', 'Giới Tính');
        $excel->getActiveSheet()->setCellValue('C1', 'CMND');
        $excel->getActiveSheet()->setCellValue('D1', 'Số Điện Thoại');
        $excel->getActiveSheet()->setCellValue('E1', 'Mã số luật sư');
        $excel->getActiveSheet()->setCellValue('F1', 'Số chứng chỉ hành nghề');
        $excel->getActiveSheet()->setCellValue('G1', 'Tổ chức hành nghề');
        $excel->getActiveSheet()->setCellValue('H1', 'Loại luật sư');
        $excel->getActiveSheet()->setCellValue('I1', 'Tình trạng luật sư');

        

        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        $numRow = 2;
        foreach($data as $row){
            $excel->getActiveSheet()->setCellValue('A'.$numRow, $row['cus_firstname'].' '.$row['cus_lastname']);
            // if($row['cus_sex'] == 'nam'){
            //     $excel->getActiveSheet()->setCellValue('B'.$numRow, 'Nam');    
            // }else{
            //     $excel->getActiveSheet()->setCellValue('B'.$numRow, 'Nữ');    
            // }
            $excel->getActiveSheet()->setCellValue('B'.$numRow, $row['cus_sex']);    
            $excel->getActiveSheet()->setCellValue('C'.$numRow, $row['cus_identity_card']);
            $excel->getActiveSheet()->setCellValue('D'.$numRow, $row['cus_cellphone']);
            $excel->getActiveSheet()->setCellValue('E'.$numRow, $row['cus_lawyer_number']);
            $excel->getActiveSheet()->setCellValue('F'.$numRow, $row['law_certfication_no']);
            $excel->getActiveSheet()->setCellValue('G'.$numRow, $row['organ_name']);
            $excel->getActiveSheet()->setCellValue('H'.$numRow, $row['law_type']);
            $excel->getActiveSheet()->setCellValue('I'.$numRow, 'Chuyển đoàn');
            $numRow++;
        }
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_ChuyenDoan.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;
     }

    public function listremovelawyernameAction(){

    }

    /*load dữ liệu cho table trong page list remove name lawyer*/
    public function listremovelawyernamedatatableAction(){

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $lawyer = new Default_Model_Lawyer();
        $dataactivelawyer = $lawyer->loadLawyerActiveByFilter($search,$start,$length,$startdate,$enddate,'4');

        $results = array(            
        );
        $dateEmpty = '1900-01-01 00:00:00';
        if($dataactivelawyer != null && sizeof($dataactivelawyer)){
            foreach($dataactivelawyer as $law){ 
                // if($law['cus_sex'] == 'nam'){
                //     $text = 'Nam';
                //     }else{
                //     $text = 'Nữ';  
                //     }                   
                array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                $law['cus_sex'],
                $law['cus_identity_card'],
                $law['cus_cellphone'],
                $law['cus_lawyer_number'],
                ($law['cus_date_lawyer_number'] != null && $law['cus_date_lawyer_number'] != '' && 
                $law['cus_date_lawyer_number'] != $dateEmpty) ? date('d/m/Y', strtotime($law['cus_date_lawyer_number'])) : '',
                $law['law_certfication_no'],
                ($law['law_certification_createdate'] != null && $law['law_certification_createdate'] != '' && 
                $law['law_certification_createdate'] != $dateEmpty) ? date('d/m/Y', strtotime($law['law_certification_createdate'])) : '',
                $law['cus_address_resident'],
                $law['cus_address_resident_now'],
                $law['organ_name'],
                $law['law_type'],
                'Rút tên' 
                ]); 
            }
        }


        $totalrecords = $lawyer->countLawyerActiveByFilter('4');

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

     //danh sách luật sư rút tên xuất execel
     public function removelawyernameAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $this->_helper->layout('layout')->disableLayout();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $lawyer = new Default_Model_Lawyer();
        $data = $lawyer->loadLawyerActiveByFilter($search,$start,$length,$startdate,$enddate,'5');
            //Khởi tạo đối tượng
        $excel = new Default_Model_Excel();
            //Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Luật sư rút tên');

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            //Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        
        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Tên');
        $excel->getActiveSheet()->setCellValue('B1', 'Giới Tính');
        $excel->getActiveSheet()->setCellValue('C1', 'CMND');
        $excel->getActiveSheet()->setCellValue('D1', 'Số Điện Thoại');
        $excel->getActiveSheet()->setCellValue('E1', 'Mã số luật sư');
        $excel->getActiveSheet()->setCellValue('F1', 'Số chứng chỉ hành nghề');
        $excel->getActiveSheet()->setCellValue('G1', 'Tổ chức hành nghề');
        $excel->getActiveSheet()->setCellValue('H1', 'Loại luật sư');
        $excel->getActiveSheet()->setCellValue('I1', 'Tình trạng luật sư');

        

        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        $numRow = 2;
        foreach($data as $row){
            $excel->getActiveSheet()->setCellValue('A'.$numRow, $row['cus_firstname'].' '.$row['cus_lastname']);
            // if($row['cus_sex'] == 'nam'){
            //     $excel->getActiveSheet()->setCellValue('B'.$numRow, 'Nam');    
            // }else{
            //     $excel->getActiveSheet()->setCellValue('B'.$numRow, 'Nữ');    
            // }
            $excel->getActiveSheet()->setCellValue('B'.$numRow, $row['cus_sex']);    
            $excel->getActiveSheet()->setCellValue('C'.$numRow, $row['cus_identity_card']);
            $excel->getActiveSheet()->setCellValue('D'.$numRow, $row['cus_cellphone']);
            $excel->getActiveSheet()->setCellValue('E'.$numRow, $row['cus_lawyer_numbers']);
            $excel->getActiveSheet()->setCellValue('F'.$numRow, $row['law_certfication_no']);
            $excel->getActiveSheet()->setCellValue('G'.$numRow, $row['organ_name']);
            $excel->getActiveSheet()->setCellValue('H'.$numRow, $row['law_type']);
            $excel->getActiveSheet()->setCellValue('I'.$numRow, 'Rút tên');
            $numRow++;
        }
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_RutTen.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;
     }

     public function listdielawyerAction(){
        // $lawyer = new Default_Model_Lawyer();
        // $dataactivelawyer = $lawyer->loadLawyerByLawStatus(2);
        // $this->view->dataactivelawyer = $dataactivelawyer;
     }

     
    /*load dữ liệu cho table trong page listdielawyer lawyer*/
    public function listdielawyerdatatableAction(){

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $lawyer = new Default_Model_Lawyer();
        $dataactivelawyer = $lawyer->loadLawyerActiveByFilter($search,$start,$length,$startdate,$enddate,'2');

        $results = array(            
        );
        $dateEmpty = '1900-01-01 00:00:00';
        if($dataactivelawyer != null && sizeof($dataactivelawyer)){
            foreach($dataactivelawyer as $law){ 
                // if($law['cus_sex'] == 'nam'){
                //     $text = 'Nam';
                //     }else{
                //     $text = 'Nữ';  
                //     }                   
                array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                $law['cus_sex'],
                $law['cus_identity_card'],
                $law['cus_cellphone'],
                $law['cus_lawyer_number'],
                ($law['cus_date_lawyer_number'] != null && $law['cus_date_lawyer_number'] != '' && 
                $law['cus_date_lawyer_number'] != $dateEmpty) ? date('d/m/Y', strtotime($law['cus_date_lawyer_number'])) : '',
                $law['law_certfication_no'],
                ($law['law_certification_createdate'] != null && $law['law_certification_createdate'] != '' && 
                $law['law_certification_createdate'] != $dateEmpty) ? date('d/m/Y', strtotime($law['law_certification_createdate'])) : '',
                $law['cus_address_resident'],
                $law['cus_address_resident_now'],
                $law['organ_name'],
                $law['law_type'],
                'Đã chết' 
                ]); 
            }
        }


        $totalrecords = $lawyer->countLawyerActiveByFilter('2');

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


     public function dielawyerAction(){
        $this->_helper->layout('layout')->disableLayout();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $lawyer = new Default_Model_Lawyer();
        $data = $lawyer->loadLawyerByLawStatus(2);
            //Khởi tạo đối tượng
        $excel = new Default_Model_Excel();
            //Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Luật sư đã chết');

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            //Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        
        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Tên');
        $excel->getActiveSheet()->setCellValue('B1', 'Giới Tính');
        $excel->getActiveSheet()->setCellValue('C1', 'CMND');
        $excel->getActiveSheet()->setCellValue('D1', 'Số Điện Thoại');
        $excel->getActiveSheet()->setCellValue('E1', 'Mã số luật sư');
        $excel->getActiveSheet()->setCellValue('F1', 'Số chứng chỉ hành nghề');
        $excel->getActiveSheet()->setCellValue('G1', 'Tổ chức hành nghề');
        $excel->getActiveSheet()->setCellValue('H1', 'Loại luật sư');
        $excel->getActiveSheet()->setCellValue('I1', 'Tình trạng luật sư');

        

        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        $numRow = 2;
        foreach($data as $row){
            $excel->getActiveSheet()->setCellValue('A'.$numRow, $row['cus_firstname'].' '.$row['cus_lastname']);
            // if($row['cus_sex'] == 'nam'){
            //     $excel->getActiveSheet()->setCellValue('B'.$numRow, 'Nam');    
            // }else{
            //     $excel->getActiveSheet()->setCellValue('B'.$numRow, 'Nữ');    
            // }
            $excel->getActiveSheet()->setCellValue('B'.$numRow, $row['cus_sex']);       
            $excel->getActiveSheet()->setCellValue('C'.$numRow, $row['cus_identity_card']);
            $excel->getActiveSheet()->setCellValue('D'.$numRow, $row['cus_cellphone']);
            $excel->getActiveSheet()->setCellValue('E'.$numRow, $row['law_code']);
            $excel->getActiveSheet()->setCellValue('F'.$numRow, $row['law_certfication_no']);
            $excel->getActiveSheet()->setCellValue('G'.$numRow, $row['organ_name']);
            $excel->getActiveSheet()->setCellValue('H'.$numRow, $row['law_type']);
            $excel->getActiveSheet()->setCellValue('I'.$numRow, 'Đã chết');
            $numRow++;
        }
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_DaChet.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;   
     }

     public function disciplineAction(){

     }


     public function listintershipAction(){
        $intershipNumber = new Default_Model_IntershipNumber();
        $data = $intershipNumber->loadInterhipNumber();
        $this->view->intershipnumbers = $data;
     }

    /*load dữ liệu cho table trong page listintership*/
    public function listintershipdatatableAction(){

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');
        $intershipnumberid = $this->getRequest()->getParam('intershipnumberid');

        $intership = new Default_Model_Intership();
        $dataactivelawyer = $intership->loadIntershipByFilter($search,$intershipnumberid,$start,$length,$startdate,$enddate);

        $results = array(            
        );

        if($dataactivelawyer != null && sizeof($dataactivelawyer)){
            foreach($dataactivelawyer as $law){ 
                // if($law['cus_sex'] == 'nam'){
                //     $text = 'Nam';
                //     }else{
                //     $text = 'Nữ';  
                //     }                   
                array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                $law['cus_sex'],
                $law['cus_identity_card'],
                $law['cus_cellphone'],
                $law['inter_number_name'],
                $law['cus_address_resident'],
                $law['cus_address_resident_now']
                // $law['law_code'],
                // $law['law_certfication_no'],
                // $law['organ_name'],
                // $law['law_type'],
                // 'Không xét' 
                ]); 
            }
        }


        $totalrecords = $intership->loadIntershipByFilter($search,$intershipnumberid,'','',$startdate,$enddate);

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

    /*load dữ liệu cho table trong page listintership*/
    public function listpaymentintershipdatatableAction(){

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $type = $this->getRequest()->getParam('type');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $intership = new Default_Model_PaymentIntershipOffline();
        $dataactivelawyer = $intership->loadPaymentIntershipOfflineFilter($start,$length,$type,$startdate,$enddate);

        $results = array(            
        );

        $dateEmpty = '1900-01-01 00:00:00';
        if($dataactivelawyer != null && sizeof($dataactivelawyer)){
            $index = 0;
            foreach($dataactivelawyer as $law){    
                $index += 1;            
                $dateToShow = ($law['payment_inter_off_created_date'] != null && $law['payment_inter_off_created_date'] != ''
                && $law['payment_inter_off_created_date'] != $dateEmpty) ? date('d/m/Y',strtotime($law['payment_inter_off_created_date'])) : '';           
                array_push($results,[
                    $index,
                $law['cus_firstname'].' '.$law['cus_lastname'],
                $law['cus_identity_card'],
                $law['cus_cellphone'],
                $law['payment_inter_off_code'],
                $law['inter_number_name'],
                $dateToShow,
                number_format($law['amount']),              
                $law['payment_type'] == 'offline' ? 'Đóng tại đoàn' : 'Đóng qua mạng'
                ]);           
            }
        }


        $totalrecords = $intership->loadPaymentIntershipOfflineFilter('','',$type,$startdate,$enddate);

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
     

    public function intershipAction(){

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');
        $intershipnumberid = $this->getRequest()->getParam('intershipnumberid');

        $this->_helper->layout('layout')->disableLayout();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $intership = new Default_Model_Intership();
        $data = $intership->loadIntershipByFilter($search,$intershipnumberid,$start,$length,$startdate,$enddate);
            //Khởi tạo đối tượng
        $excel = new Default_Model_Excel();
            //Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Luật sư tập sự');

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            //Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
        
        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Tên');
        $excel->getActiveSheet()->setCellValue('B1', 'Giới Tính');
        $excel->getActiveSheet()->setCellValue('C1', 'CMND');
        $excel->getActiveSheet()->setCellValue('D1', 'Số Điện Thoại');
        $excel->getActiveSheet()->setCellValue('E1', 'Đợt tập sự');
        

        

        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        $numRow = 2;
        foreach($data as $row){
            $excel->getActiveSheet()->setCellValue('A'.$numRow, $row['cus_firstname'].' '.$row['cus_lastname']);
            // if($row['cus_sex'] == 'nam'){
            //     $excel->getActiveSheet()->setCellValue('B'.$numRow, 'Nam');    
            // }else{
            //     $excel->getActiveSheet()->setCellValue('B'.$numRow, 'Nữ');    
            // }
            $excel->getActiveSheet()->setCellValue('B'.$numRow, $row['cus_sex']);
            $excel->getActiveSheet()->setCellValue('C'.$numRow, $row['cus_identity_card']);
            $excel->getActiveSheet()->setCellValue('D'.$numRow, $row['cus_cellphone']);
            $excel->getActiveSheet()->setCellValue('E'.$numRow, $row['inter_number_name']);

            $numRow++;
        }
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_TapSu.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;   
    }

    public function listnotchecklawyerAction(){
    }

         /*load dữ liệu cho table trong page listdielawyer lawyer*/
    public function listnotchecklawyerdatatableAction(){

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $lawyer = new Default_Model_Lawyer();
        $dataactivelawyer = $lawyer->loadLawyerActiveByFilter($search,$start,$length,$startdate,$enddate,'6');

        $results = array(            
        );
        $dateEmpty = '1900-01-01 00:00:00';
        if($dataactivelawyer != null && sizeof($dataactivelawyer)){
            foreach($dataactivelawyer as $law){ 
                // if($law['cus_sex'] == 'nam'){
                //     $text = 'Nam';
                //     }else{
                //     $text = 'Nữ';  
                //     }                   
                array_push($results,[$law['cus_firstname'].' '.$law['cus_lastname'],
                $law['cus_sex'],
                $law['cus_identity_card'],
                $law['cus_cellphone'],
                $law['cus_lawyer_number'],
                ($law['cus_date_lawyer_number'] != null && $law['cus_date_lawyer_number'] != '' && 
                $law['cus_date_lawyer_number'] != $dateEmpty) ? date('d/m/Y', strtotime($law['cus_date_lawyer_number'])) : '',
                $law['law_certfication_no'],
                ($law['law_certification_createdate'] != null && $law['law_certification_createdate'] != '' && 
                $law['law_certification_createdate'] != $dateEmpty) ? date('d/m/Y', strtotime($law['law_certification_createdate'])) : '',
                $law['cus_address_resident'],
                $law['cus_address_resident_now'],
                $law['organ_name'],
                $law['law_type'],
                'Không xét' 
                ]); 
            }
        }


        $totalrecords = $lawyer->countLawyerActiveByFilter('6');

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

     public function notchecklawyerAction(){
        
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');
        
        $this->_helper->layout('layout')->disableLayout();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $lawyer = new Default_Model_Lawyer();
        $data = $lawyer->loadLawyerActiveByFilter($search,$start,$length,$startdate,$enddate,'6');
            //Khởi tạo đối tượng
        $excel = new Default_Model_Excel();
            //Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Luật sư không xét');

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            //Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        
        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->setCellValue('A1', 'Tên');
        $excel->getActiveSheet()->setCellValue('B1', 'Giới Tính');
        $excel->getActiveSheet()->setCellValue('C1', 'CMND');
        $excel->getActiveSheet()->setCellValue('D1', 'Số Điện Thoại');
        $excel->getActiveSheet()->setCellValue('E1', 'Mã số luật sư');
        $excel->getActiveSheet()->setCellValue('F1', 'Số chứng chỉ hành nghề');
        $excel->getActiveSheet()->setCellValue('G1', 'Tổ chức hành nghề');
        $excel->getActiveSheet()->setCellValue('H1', 'Loại luật sư');
        $excel->getActiveSheet()->setCellValue('I1', 'Tình trạng luật sư');

        

        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        $numRow = 2;
        foreach($data as $row){
            $excel->getActiveSheet()->setCellValue('A'.$numRow, $row['cus_firstname'].' '.$row['cus_lastname']);
            // if($row['cus_sex'] == 'nam'){
            //     $excel->getActiveSheet()->setCellValue('B'.$numRow, 'Nam');    
            // }else{
            //     $excel->getActiveSheet()->setCellValue('B'.$numRow, 'Nữ');    
            // }
                
            $excel->getActiveSheet()->setCellValue('B'.$numRow, $row['cus_sex']); 
            $excel->getActiveSheet()->setCellValue('C'.$numRow, $row['cus_identity_card']);
            $excel->getActiveSheet()->setCellValue('D'.$numRow, $row['cus_cellphone']);
            $excel->getActiveSheet()->setCellValue('E'.$numRow, $row['cus_lawyer_number']);
            $excel->getActiveSheet()->setCellValue('F'.$numRow, $row['law_certfication_no']);
            $excel->getActiveSheet()->setCellValue('G'.$numRow, $row['organ_name']);
            $excel->getActiveSheet()->setCellValue('H'.$numRow, $row['law_type']);
            $excel->getActiveSheet()->setCellValue('I'.$numRow, 'Không xét');
            $numRow++;
        }
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_KhongXet.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;   
     }

     public function exportallAction() {
        // $this->_helper->layout('layout')->disableLayout();
        // date_default_timezone_set('Asia/Ho_Chi_Minh');
        // $data = [
        // ['Nguyễn Khánh Linh', 'Nữ', '500k'], 
        // ['Ngọc Trinh', 'Nữ', '700k'], 
        // ['Tùng Sơn', 'Không xác định', 'Miễn phí'], 
        // ['Kenny Sang', 'Không xác định', 'Miễn phí']
        // ];
        //     //Khởi tạo đối tượng
        // $excel = new Default_Model_Excel();
        //     //Chọn trang cần ghi (là số từ 0->n)
        // $excel->setActiveSheetIndex(0);
        //     //Tạo tiêu đề cho trang. (có thể không cần)
        // $excel->getActiveSheet()->setTitle('demo ghi dữ liệu');

        //     //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        // $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        // $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        // $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

        //     //Xét in đậm cho khoảng cột
        // $excel->getActiveSheet()->getStyle('A1:C1')->getFont()->setBold(true);
        // //Tạo tiêu đề cho từng cột
        // //Vị trí có dạng như sau:
        // /**
        //  * |A1|B1|C1|..|n1|
        //  * |A2|B2|C2|..|n1|
        //  * |..|..|..|..|..|
        //  * |An|Bn|Cn|..|nn|
        //  */
        // $excel->getActiveSheet()->setCellValue('A1', 'Tên');
        // $excel->getActiveSheet()->setCellValue('B1', 'Giới Tính');
        // $excel->getActiveSheet()->setCellValue('C1', 'Đơn giá(/shoot)');
        // // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // // dòng bắt đầu = 2
        // $numRow = 2;
        // foreach($data as $row){
        //     $excel->getActiveSheet()->setCellValue('A'.$numRow, $row[0]);
        //     $excel->getActiveSheet()->setCellValue('B'.$numRow, $row[1]);
        //     $excel->getActiveSheet()->setCellValue('C'.$numRow, $row[2]);
        //     $numRow++;
        // }
        // // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment; filename="data.xlsx"');
        // PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        // return;
	
	}
}
