<?php

class ReportController extends Zend_Controller_Action
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

    /**
     * search customers
     */

    public function searchcustomerspageAction(){
        $modeLawStatus = new Default_Model_LawStatus();
        $this->view->lawStatus = $modeLawStatus->loadLawStatus();

        $modelLanguages = new Default_Model_Languages();
        $this->view->languages = $modelLanguages->loadLanguages();

        $city = new Default_Model_City();
        $data = $city->loadCity();
        $this->view->cities =  $data;

    }

    /**
     * export execl search
     */
    public function searchcustomerexportexcelAction(){
        $enddate = $this->getRequest()->getParam('enddate');
        $startdate = $this->getRequest()->getParam('startdate');
        $hovalot = $this->getRequest()->getParam('hovalot');
        $ten = $this->getRequest()->getParam('ten');
        $cmnd = $this->getRequest()->getParam('cmnd');
        $thanhvien = $this->getRequest()->getParam('thanhvien');
        $trinhdohv = $this->getRequest()->getParam('trinhdohv');
        $diachilienhe = $this->getRequest()->getParam('diachilienhe');
        $thuongtru = $this->getRequest()->getParam('thuongtru');
        $lamviec = $this->getRequest()->getParam('lamviec');
        $gioitinh = $this->getRequest()->getParam('gioitinh');
        $socchn = $this->getRequest()->getParam('socchn');
        $dotgianhap = $this->getRequest()->getParam('dotgianhap');
        $dottapsu = $this->getRequest()->getParam('dottapsu');
        $diachi = $this->getRequest()->getParam('diachi');
        $tinhtrang = $this->getRequest()->getParam('tinhtrang');
        $dangvien = $this->getRequest()->getParam('dangvien');
        $phanloai = $this->getRequest()->getParam('phanloai');
        $sapxep = $this->getRequest()->getParam('sapxep');
        $ngoaingu = $this->getRequest()->getParam('ngoaingu');
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $sothels = $this->getRequest()->getParam('sothels');
        $noisinh = $this->getRequest()->getParam('noisinh');
        $xoaten = $this->getRequest()->getParam('xoaten');

        $this->_helper->layout('layout')->disableLayout();

        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $model = new Default_Model_Customer();
        $data = $model->searchCustomersFilter($enddate,$startdate,$hovalot,$ten,$cmnd,$thanhvien,$trinhdohv,
        $diachilienhe,$thuongtru,$lamviec,$gioitinh,$socchn,$dotgianhap,$diachi,$tinhtrang,
        $dangvien,$phanloai,$sapxep,$ngoaingu,$start,$length,$sothels,$noisinh,$dottapsu,$xoaten);

        $index = 0;

        $modelCustomer = new Default_Model_Customer();
        $results = array(
        );

        //$dateEmpty = '1900-01-01 00:00:00';
        //$currentDate = new Zend_Date();
        if($data != null && sizeof($data) > 0){
            foreach($data as $cus){
                $index += 1;
                $endmonth = $modelCustomer->getEndMonthByCusId($cus['cus_id']);
                array_push($results,[
                    $index,
                    $cus['cus_firstname'].' '.$cus['cus_lastname'],
                    ($cus['cus_birthday'] != null && $cus['cus_birthday'] != '' && $cus['cus_birthday'] != '1900-01-01 00:00:00') ? date("d/m/Y", strtotime($cus['cus_birthday'])) : '',
                    $cus['cus_email'] != null ? $cus['cus_email'] : '' ,
                    $cus['cus_identity_card'] != null ? $cus['cus_identity_card'] : '',
                    $cus['cus_cellphone'] != null ? $cus['cus_cellphone'] : '',
                    $cus['cus_lawyer_number'] != null ? $cus['cus_lawyer_number'] : '',
                    ($cus['cus_date_lawyer_number'] != null && $cus['cus_date_lawyer_number'] != '' && $cus['cus_date_lawyer_number'] != '1900-01-01 00:00:00') ? date("d/m/Y", strtotime($cus['cus_date_lawyer_number'])) : '',
                    $cus['law_certfication_no'] != null ? $cus['law_certfication_no'] : '',
                    ($cus['law_certification_createdate'] != null && $cus['law_certification_createdate'] != '' && $cus['law_certification_createdate'] != '1900-01-01 00:00:00') ? date("d/m/Y", strtotime($cus['law_certification_createdate'])) : '',
                    $cus['law_joining_number'] != null ? $cus['law_joining_number'] : '',
                    $cus['cus_address_resident'] != null ? $cus['cus_address_resident'] : '',
                    $cus['cus_address_resident_now'] != null ? $cus['cus_address_resident_now'] : '',
                    $endmonth != null && $endmonth != '' ? $endmonth : ''
                ]);
            }
        }
            //Khởi tạo đối tượng
        $excel = new Default_Model_Excel();
            //Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
        $excel->getActiveSheet()->setTitle('Kết quả tìm kiếm');

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            //Xét in đậm cho khoảng cột
        $excel->getActiveSheet()->getStyle('A1:N1')->getFont()->setBold(true);

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
        $excel->getActiveSheet()->setCellValue('D1', 'Email');
        $excel->getActiveSheet()->setCellValue('E1', 'Số CMND');
        $excel->getActiveSheet()->setCellValue('F1', 'Số điện thoại');
        $excel->getActiveSheet()->setCellValue('G1', 'Số thẻ LS');
        $excel->getActiveSheet()->setCellValue('H1', 'Ngày cấp số thẻ');
        $excel->getActiveSheet()->setCellValue('I1', 'Số chứng chỉ hành nghề');
        $excel->getActiveSheet()->setCellValue('J1', 'Ngày cấp số chứng chỉ hành nghề');
        $excel->getActiveSheet()->setCellValue('K1', 'Đợt gia nhập');
        $excel->getActiveSheet()->setCellValue('L1', 'Địa chỉ thường trú');
        $excel->getActiveSheet()->setCellValue('M1', 'Nơi ở hiện tại');
        $excel->getActiveSheet()->setCellValue('N1', 'Đoàn phí');
        $excel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($styleArray);


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
                $excel->getActiveSheet()->setCellValue('I'.$numRow, $row[8]);
                $excel->getActiveSheet()->setCellValue('J'.$numRow, $row[9]);
                $excel->getActiveSheet()->setCellValue('K'.$numRow, $row[10]);
                $excel->getActiveSheet()->setCellValue('L'.$numRow, $row[11]);
                $excel->getActiveSheet()->setCellValue('M'.$numRow, $row[12]);
                $excel->getActiveSheet()->setCellValue('N'.$numRow, $row[13]);
                $numRow++;
            }
        }
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_KQ_TimKiemLuatSu.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;
    }
    /***
     * datatable search
     */
    public function listcustomerdatatableAction(){
        $enddate = $this->getRequest()->getParam('enddate');
        $startdate = $this->getRequest()->getParam('startdate');
        $hovalot = $this->getRequest()->getParam('hovalot');
        $ten = $this->getRequest()->getParam('ten');
        $cmnd = $this->getRequest()->getParam('cmnd');
        $thanhvien = $this->getRequest()->getParam('thanhvien');
        $trinhdohv = $this->getRequest()->getParam('trinhdohv');
        $diachilienhe = $this->getRequest()->getParam('diachilienhe');
        $thuongtru = $this->getRequest()->getParam('thuongtru');
        $lamviec = $this->getRequest()->getParam('lamviec');
        $gioitinh = $this->getRequest()->getParam('gioitinh');
        $socchn = $this->getRequest()->getParam('socchn');
        $dotgianhap = $this->getRequest()->getParam('dotgianhap');
        $dottapsu = $this->getRequest()->getParam('dottapsu');
        $diachi = $this->getRequest()->getParam('diachi');
        $tinhtrang = $this->getRequest()->getParam('tinhtrang');
        $dangvien = $this->getRequest()->getParam('dangvien');
        $phanloai = $this->getRequest()->getParam('phanloai');
        $sapxep = $this->getRequest()->getParam('sapxep');
        $ngoaingu = $this->getRequest()->getParam('ngoaingu');
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $sothels = $this->getRequest()->getParam('sothels');
        $noisinh = $this->getRequest()->getParam('noisinh');
        $xoaten = $this->getRequest()->getParam('xoaten');
        $results = array(
        );

        //$model
        //offline
        $model = new Default_Model_Customer();
        $data = $model->searchCustomersFilter($enddate,$startdate,$hovalot,$ten,$cmnd,$thanhvien,$trinhdohv,
        $diachilienhe,$thuongtru,$lamviec,$gioitinh,$socchn,$dotgianhap,$diachi,$tinhtrang,
        $dangvien,$phanloai,$sapxep,$ngoaingu,$start,$length,$sothels,$noisinh,$dottapsu,$xoaten);

        $index = 0;

        $modelCustomer = new Default_Model_Customer();

        if($data != null && sizeof($data) > 0){
            foreach($data as $cus){
                $index += 1;
                // $time=strtotime($cus['payment_lawyer_off_created_date']);
                // $month=date("m/Y",$time);

                // $duration = $cus['month'];
                // $text = "+".$cus['month']." months";
                // $effectiveMonth = date('m/Y', strtotime($text, strtotime($cus['payment_lawyer_off_created_date'])));
                $endmonth = $modelCustomer->getEndMonthByCusId($cus['cus_id']);
                $joiningInfo = $modelCustomer->getLastPaymentJoiningByCusId($cus['cus_id']);
                $intershipInfo = $modelCustomer->getLastPaymentIntershipByCusId($cus['cus_id']);
                array_push($results,[
                    $cus['cus_id'],
                    $cus['cus_firstname'].' '.$cus['cus_lastname'],
                    ($cus['cus_birthday'] != null && $cus['cus_birthday'] != '' && $cus['cus_birthday'] != '1900-01-01 00:00:00') ? date("d/m/Y", strtotime($cus['cus_birthday'])) : '',
                    $cus['cus_identity_card'],
                    $cus['inter_number_name'],
                    $cus['law_joining_number'],
                    $cus['cus_lawyer_number'],
                    ($cus['cus_date_lawyer_number'] != null && $cus['cus_date_lawyer_number'] != '' && $cus['cus_date_lawyer_number'] != '1900-01-01 00:00:00') ? date("d/m/Y", strtotime($cus['cus_date_lawyer_number'])) : '',
                    $cus['law_certfication_no'],
                    ($cus['law_certification_createdate'] != null && $cus['law_certification_createdate'] != '' && $cus['law_certification_createdate'] != '1900-01-01 00:00:00') ? date("d/m/Y", strtotime($cus['law_certification_createdate'])) : '',
                    $cus['cus_address_resident'],
                    $cus['cus_address_resident_now'],
                    $cus['cus_cellphone'],
                    $cus['cus_email'],
                    $intershipInfo,
                    $joiningInfo,
                    $endmonth != null && $endmonth != '' ? $endmonth : ''


                ]);
            }
        }

        $totalrecords = $model->searchCustomersFilter($enddate,$startdate,$hovalot,$ten,$cmnd,$thanhvien,$trinhdohv,
            $diachilienhe,$thuongtru,$lamviec,$gioitinh,$socchn,$dotgianhap,$diachi,$tinhtrang,
            $dangvien,$phanloai,$sapxep,$ngoaingu,'','',$sothels,$noisinh,$dottapsu,$xoaten);
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

    /**
     * Báo cáo tổng hơp văn phòng LS
     */
    public function finalexportexcelAction(){

        $this->_helper->layout('layout')->disableLayout();

        //$search = $this->getRequest()->getParam('search');


        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $lawyer = new Default_Model_OrganizationLawDetails();
        $data = $lawyer->loadOrganzations();
            //Khởi tạo đối tượng
        $excel = new Default_Model_Excel();
            //Chọn trang cần ghi (là số từ 0->n)
        $excel->setActiveSheetIndex(0);
            //Tạo tiêu đề cho trang. (có thể không cần)
        //$excel->getActiveSheet()->setTitle('Luật sư đang hoạt động');

            //Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            //Xét in đậm cho khoảng cột
        //    $sheet->mergeCells("G".($row_count+1).":I".($row_count+1));
        $excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A2:I2')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A3:I3')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A6:I6')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A8:I8')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A9:I9')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A10:I10')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A11:I11')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A12:I12')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A13:I13')->getFont()->setBold(true);

        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->mergeCells('A1:C1');
        $excel->getActiveSheet()
        ->getCell('A1')
        ->setValue('Biểu số:30/BTP/BTTP/LSTN');

        // $excel->getActiveSheet()->getStyle('A1')->getAlignment()->applyFromArray(
        //     array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        // );

        $excel->getActiveSheet()->mergeCells('E1:J1');
        $excel->getActiveSheet()
        ->getCell('E1')
        ->setValue('BÁO CÁO TỔNG TÌNH HÌNH TỔ CHỨC VÀ HOẠT ĐỘNG CỦA VP LUẬT SƯ');

        $excel->getActiveSheet()->getStyle('E1')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('L1:O1');
        $excel->getActiveSheet()
        ->getCell('L1')
        ->setValue('Đơn vị báo cáo:');

        $excel->getActiveSheet()->getStyle('L1')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('A2:C2');
        $excel->getActiveSheet()
        ->getCell('A2')
        ->setValue('Ban hành kèm theo Thông tư số 04/2016/TT-BTP');

        // $excel->getActiveSheet()->getStyle('A2')->getAlignment()->applyFromArray(
        //     array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        // );

        $excel->getActiveSheet()->mergeCells('G2:H2');
        $excel->getActiveSheet()
        ->getCell('G2')
        ->setValue('(6 tháng, năm');

        $excel->getActiveSheet()->getStyle('G2')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('L2:O2');
        $excel->getActiveSheet()
        ->getCell('L2')
        ->setValue('-Tên đơn vị báo cáo:');

        $excel->getActiveSheet()->getStyle('G2')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('A3:C3');
        $excel->getActiveSheet()
        ->getCell('A3')
        ->setValue('ngày 03/03/2016');

        // $excel->getActiveSheet()->getStyle('A3')->getAlignment()->applyFromArray(
        //     array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        // );

        $excel->getActiveSheet()->mergeCells('L3:O3');
        $excel->getActiveSheet()
        ->getCell('L3')
        ->setValue('-Số điện thoại');

        $excel->getActiveSheet()->mergeCells('A4:C4');
        $excel->getActiveSheet()
        ->getCell('A4')
        ->setValue('Ngày nhận báo cáo(BC):');

        // $excel->getActiveSheet()->getStyle('A4')->getAlignment()->applyFromArray(
        //     array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        // );

        $excel->getActiveSheet()->mergeCells('L4:O4');
        $excel->getActiveSheet()
        ->getCell('L4')
        ->setValue('-Email:');

        $excel->getActiveSheet()->mergeCells('A5:C5');
        $excel->getActiveSheet()
        ->getCell('A5')
        ->setValue('Sở Tư pháp nhận');

        // $excel->getActiveSheet()->getStyle('A5')->getAlignment()->applyFromArray(
        //     array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        // );

        $excel->getActiveSheet()->mergeCells('F5:I5');
        $excel->getActiveSheet()
        ->getCell('F5')
        ->setValue('Kỳ báo cáo');

        $excel->getActiveSheet()->getStyle('F5')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('L5:O5');
        $excel->getActiveSheet()
        ->getCell('L5')
        ->setValue('Đơn vị nhận báo cáo');

        $excel->getActiveSheet()->getStyle('L5')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('A6:C6');
        $excel->getActiveSheet()
        ->getCell('A6')
        ->setValue('Báo cáo 6 tháng : ngày 06 tháng 06 hàng năm');

        $excel->getActiveSheet()->mergeCells('F6:I6');
        $excel->getActiveSheet()
        ->getCell('F6')
        ->setValue('Từ ngày.. tháng.. năm..');

        $excel->getActiveSheet()->getStyle('F6')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('L6:O6');
        $excel->getActiveSheet()
        ->getCell('L6')
        ->setValue('....................');

        $excel->getActiveSheet()->getStyle('L6')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        //row 7
        $excel->getActiveSheet()->mergeCells('A7:C7');
        $excel->getActiveSheet()
        ->getCell('A7')
        ->setValue('Báo cáo năm lần 1: ngày 07 tháng 11 hàng năm');

        $excel->getActiveSheet()->mergeCells('F7:I7');
        $excel->getActiveSheet()
        ->getCell('F7')
        ->setValue('đến ngày.. tháng.. năm.....)');

        $excel->getActiveSheet()->getStyle('F7')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('L7:O7');
        $excel->getActiveSheet()
        ->getCell('L7')
        ->setValue('-....................');

        $excel->getActiveSheet()->getStyle('L7')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        //row 8

        $excel->getActiveSheet()->mergeCells('A8:C8');
        $excel->getActiveSheet()
        ->getCell('A8')
        ->setValue('Báo cáo năm chính thức : ngày 15 tháng 02 năm sau');


        $excel->getActiveSheet()->mergeCells('A11:C11');
        $excel->getActiveSheet()
        ->getCell('A11')
        ->setValue('Số luật sư(LS) làm việc tại tổ chức hành nghề luật sư(TCNHNLS)(người)');

        $excel->getActiveSheet()->getStyle('A11')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('D11:K11');
        $excel->getActiveSheet()
        ->getCell('D11')
        ->setValue('Số việc thực hiện xong(việc)');

        $excel->getActiveSheet()->getStyle('D11')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('L11:O11');
        $excel->getActiveSheet()
        ->getCell('L11')
        ->setValue('Doanh thu(đồng)');

        $excel->getActiveSheet()->getStyle('L11')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        //

        $excel->getActiveSheet()->mergeCells('A12:A14');
        $excel->getActiveSheet()
        ->getCell('A12')
        ->setValue('Tổng số');

        $excel->getActiveSheet()->getStyle('A12')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('B12:C12');
        $excel->getActiveSheet()
        ->getCell('B12')
        ->setValue('Chia ra');

        $excel->getActiveSheet()->getStyle('B12')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('B13:B14');
        $excel->getActiveSheet()
        ->getCell('B13')
        ->setValue('Số LS trong nước làm việc tại TCHNLS');

        $excel->getActiveSheet()->getStyle('B13')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('C13:C14');
        $excel->getActiveSheet()
        ->getCell('C13')
        ->setValue('Số LS nước ngoài làm việc tại TCHNLS');

        $excel->getActiveSheet()->getStyle('C13')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('D12:D14');
        $excel->getActiveSheet()
        ->getCell('D12')
        ->setValue('Tổng số');

        $excel->getActiveSheet()->getStyle('D12')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('E12:K12');
        $excel->getActiveSheet()
        ->getCell('E12')
        ->setValue('Chia ra');

        $excel->getActiveSheet()->getStyle('E12')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('E13:G13');
        $excel->getActiveSheet()
        ->getCell('E13')
        ->setValue('Số việc tố tụng');

        $excel->getActiveSheet()->getStyle('E13')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        //$excel->getActiveSheet()->mergeCells('E13:G13');
        $excel->getActiveSheet()
        ->getCell('E14')
        ->setValue('Tổng số');

        $excel->getActiveSheet()->getStyle('E14')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('F14:G14');
        $excel->getActiveSheet()
        ->getCell('F14')
        ->setValue('Trong đó: số việc về hình sự');

        $excel->getActiveSheet()->getStyle('F14')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('H13:I14');

        $excel->getActiveSheet()
        ->getCell('H13')
        ->setValue('Số việc tư vấn pháp luật và dịch vụ pháp lý khác');

        $excel->getActiveSheet()->mergeCells('J13:K14');

        $excel->getActiveSheet()
        ->getCell('J13')
        ->setValue('Trợ giúp pháp lý');

        $excel->getActiveSheet()->mergeCells('L12:M14');

        $excel->getActiveSheet()
        ->getCell('L12')
        ->setValue('Tổng số́');

        $excel->getActiveSheet()->mergeCells('N12:O14');

        $excel->getActiveSheet()
        ->getCell('N12')
        ->setValue('Nộp thuế́́');





        // $excel->getActiveSheet()->getStyle('L11')->getAlignment()->applyFromArray(
        //     array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        // );

        // $excel->getActiveSheet()->setCellValue('A13', 'STT');
        // $excel->getActiveSheet()->setCellValue('B13', 'Họ Tên');
        // $excel->getActiveSheet()->setCellValue('C13', 'Năm sinh');
        // $excel->getActiveSheet()->setCellValue('D13', 'Số thẻ');
        // $excel->getActiveSheet()->setCellValue('E13', 'Số tháng');
        // $excel->getActiveSheet()->setCellValue('F13', 'Tỷ lệ trích nộp 50%');
        // $excel->getActiveSheet()->setCellValue('G13', 'Số tiền');
        // $excel->getActiveSheet()->setCellValue('H13', 'Từ');
        // $excel->getActiveSheet()->setCellValue('I13', 'Đến');

        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        // $numRow = 14;
        // $index = 0;
        // foreach($data as $row){

        //     $index += 1;
        //     $time=strtotime($row['payment_lawyer_off_created_date']);
        //     $month=date("m/Y",$time);

        //     $duration = $row['month'];
        //     $text = "+".$row['month']." months";
        //     $effectiveMonth = date('m/Y', strtotime($text, strtotime($row['payment_lawyer_off_created_date'])));

            $total_law_native = 0;
            $total_law_foreign = 0;
            $total_procedure = 0;
            $total_criminal = 0;
            $total_support_service = 0;
            $total_support = 0;
            $amount = 0;
            $amount_charge = 0;
            foreach($data as $row){
                $total_law_native += $row['total_law_native'];
                $total_law_foreign += $row['total_law_foreign'];
                $total_procedure += $row['total_procedure'];
                $total_criminal += $row['total_criminal'];
                $total_support_service += $row['total_support_service'];
                $total_support += $row['total_support'];
                $amount += $row['amount'];
                $amount_charge += $row['amount_charge'];
            }

            $excel->getActiveSheet()->setCellValue('A16', $total_law_native+$total_law_foreign);
            $excel->getActiveSheet()->setCellValue('B16',$total_law_native);
            $excel->getActiveSheet()->setCellValue('C16', $total_law_foreign);
            $excel->getActiveSheet()->setCellValue('D16', $total_procedure+$total_criminal);
            $excel->getActiveSheet()->setCellValue('E16',$total_procedure);
            $excel->getActiveSheet()->mergeCells('F16:G16');
            $excel->getActiveSheet()->setCellValue('F16',$total_criminal);
            $excel->getActiveSheet()->mergeCells('H16:I16');
            $excel->getActiveSheet()->setCellValue('H16',$total_support_service);
            $excel->getActiveSheet()->mergeCells('J16:K16');
            $excel->getActiveSheet()->setCellValue('J16',$total_support);
            $excel->getActiveSheet()->mergeCells('L16:M16');
            $excel->getActiveSheet()->setCellValue('L16',$amount);
            $excel->getActiveSheet()->mergeCells('N16:O16');
            $excel->getActiveSheet()->setCellValue('N16',$amount_charge);

            $excel->getActiveSheet()->mergeCells('A18:J18');
            $excel->getActiveSheet()->setCellValue('A18',"- Văn phòng luật sư, công ty luật báo cáo từ cột (1) tới cột (10) của biểu này");
            $excel->getActiveSheet()->mergeCells('A19:J19');
            $excel->getActiveSheet()->setCellValue('A19',"- Số lượng ước tính ( số liệu ước tính 01 tháng đối với báo cáo 6 tháng; 02 tháng với báo cáo năm lần 1)");
            $excel->getActiveSheet()->mergeCells('A20:J20');
            $excel->getActiveSheet()->setCellValue('A20',"-Số liệu ước tính cột(4) .........; cột(9)..........;cột(10)..........");

            $excel->getActiveSheet()->setCellValue('B23',"Người lập biểu");
            $excel->getActiveSheet()->setCellValue('B24',"(Ký, ghi rõ họ, tên)");

            $excel->getActiveSheet()->mergeCells('J23:M23');
            $excel->getActiveSheet()->setCellValue('J23',".........ngày.....tháng..... năm.....");
            $excel->getActiveSheet()->mergeCells('J24:M24');
            $excel->getActiveSheet()->setCellValue('J24',"THỦ TRƯỞNG ĐƠN VỊ");


            //     $numRow++;
        // }
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Baocao_VPLS.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;

    }


    /**
     * report báo cáo tổng hợp
     */

    public function reportfinalexcelAction(){

        $this->_helper->layout('layout')->disableLayout();

        $year = $this->getRequest()->getParam('year');

        // $modelOnline = new Default_Model_PaymentLawyerOnline();
        // $modelOffline = new Default_Model_PaymentLawyerOffline();
        $modelPaymentLayerOffline = new Default_Model_PaymentLawyerOffline();
        $modelPaymentJoiningOffline = new Default_Model_PaymentJoiningOffline();
        $modelPaymentOffline = new Default_Model_PaymentOffline();
        $modelPaymentIntershipOffline = new Default_Model_PaymentIntershipOffline();



        date_default_timezone_set('Asia/Ho_Chi_Minh');

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
        //    $sheet->mergeCells("G".($row_count+1).":I".($row_count+1));
        $excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A2:I2')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A3:I3')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A6:I6')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A8:I8')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A9:I9')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A10:I10')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A11:I11')->getFont()->setBold(true);

        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->mergeCells('A1:C1');
        $excel->getActiveSheet()
        ->getCell('A1')
        ->setValue('ỦY BAN NHÂN DÂN THÀNH PHỐ HỒ CHÍ MINH');

        $excel->getActiveSheet()->getStyle('A1')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('A2:C2');
        $excel->getActiveSheet()
        ->getCell('A2')
        ->setValue('ĐOÀN LUẬT SƯ');

        $excel->getActiveSheet()->getStyle('A2')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('A3:C3');
        $excel->getActiveSheet()
        ->getCell('A3')
        ->setValue('THÀNH PHỐ HỒ CHÍ MINH');

        $excel->getActiveSheet()->getStyle('A3')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('E1:J1');
        $excel->getActiveSheet()
        ->getCell('E1')
        ->setValue('CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM');

        $excel->getActiveSheet()->getStyle('E1')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('F2:J2');
        $excel->getActiveSheet()
        ->getCell('F2')
        ->setValue('Độc lập - Tự do - Hạnh phúc');

        $excel->getActiveSheet()->getStyle('F2')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('A6:C6');
        $excel->getActiveSheet()
        ->getCell('A6')
        ->setValue('Số : 03/BCTC'.$year.'-ĐLS');

        $excel->getActiveSheet()->getStyle('A6')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('E6:K6');
        $excel->getActiveSheet()
        ->getCell('E6')
        ->setValue('Thành phố Hồ Chí Minh, ngày... tháng ... năm '.$year);

        $excel->getActiveSheet()->getStyle('F6')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('B8:H8');
        $excel->getActiveSheet()
        ->getCell('B8')
        ->setValue('BÁO CÁO NĂM '.$year);

        $excel->getActiveSheet()->getStyle('B8')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('C9:F9');
        $excel->getActiveSheet()
        ->getCell('C9')
        ->setValue('(Từ ngày 01/01/'.$year.' '.'đến ngày 31/12/'.$year);

        $excel->getActiveSheet()->getStyle('D9')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('B11:D11');
        $excel->getActiveSheet()
        ->getCell('A11')
        ->setValue('1.');
        $excel->getActiveSheet()
        ->getCell('B11')
        ->setValue('CÁC KHOẢN THU THƯỜNG XUYÊN');

        $excel->getActiveSheet()->getStyle('C10')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('B12:D12');
        $excel->getActiveSheet()
        ->getCell('A12')
        ->setValue('1.1');
        $excel->getActiveSheet()
        ->getCell('B12')
        ->setValue('Thu phí khác');

        $excel->getActiveSheet()->mergeCells('B13:D13');
        $excel->getActiveSheet()
        ->getCell('A13')
        ->setValue('1.2');
        $excel->getActiveSheet()
        ->getCell('B13')
        ->setValue('Thu phí thành viên của luật sư');


        $excel->getActiveSheet()->mergeCells('B14:D14');
        $excel->getActiveSheet()
        ->getCell('A14')
        ->setValue('1.3');
        $excel->getActiveSheet()
        ->getCell('B14')
        ->setValue('Thu phí tập sự của người hành nghề luật sư');


        $excel->getActiveSheet()->mergeCells('B15:D15');
        $excel->getActiveSheet()
        ->getCell('A15')
        ->setValue('1.4');
        $excel->getActiveSheet()
        ->getCell('B15')
        ->setValue('Thu phí gia nhập');


        $excel->getActiveSheet()
        ->getCell('B18')
        ->setValue('Người lập báo cáo');

        $excel->getActiveSheet()
        ->getCell('B21')
        ->setValue('Lê Thị Hòa');

        $excel->getActiveSheet()->getStyle('B18')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()
        ->getCell('B19')
        ->setValue('KẾ TOÁN');

        $excel->getActiveSheet()->getStyle('B19')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()
        ->getCell('G19')
        ->setValue('THỦ QUỸ');

        $excel->getActiveSheet()
        ->getCell('G22')
        ->setValue('LS.Kỷ Minh Đức');

        $excel->getActiveSheet()->getStyle('G19')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );


        $excel->getActiveSheet()
        ->getCell('B25')
        ->setValue('PHÓ CHỦ NHIỆM');
        $excel->getActiveSheet()
        ->getCell('B26')
        ->setValue('Phụ trách tài chính');
        $excel->getActiveSheet()
        ->getCell('B29')
        ->setValue('LS. Nguyễn Thị Hồng Liên');

        $excel->getActiveSheet()
        ->getCell('G25')
        ->setValue('CHỦ NHIỆM');
        $excel->getActiveSheet()
        ->getCell('B29')
        ->setValue('LS. Nguyễn Văn Trung');


        $modelPaymentOffline = new Default_Model_PaymentOffline();


        $moonetIntership = $modelPaymentIntershipOffline->countMooneyInYear($year);
        $mooneyLaywer = $modelPaymentLayerOffline->countMooneyInYear($year);
        $mooneyJoining = $modelPaymentJoiningOffline->countMooneyInYear($year);
        $mooneyPaymentOff = $modelPaymentLayerOffline->countMooneyInYear($year);

        $excel->getActiveSheet()->setCellValue('E12', $mooneyPaymentOff);
        $excel->getActiveSheet()->setCellValue('E13', $mooneyLaywer);
        $excel->getActiveSheet()->setCellValue('E14', $moonetIntership);
        $excel->getActiveSheet()->setCellValue('E15', $mooneyJoining);

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => '#000000'),
                ),
            ),
        );

        $excel->getActiveSheet()->getStyle('A11:E15')->applyFromArray($styleArray);
        //$excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        // $excel->getActiveSheet()->setCellValue('C13', 'Năm sinh');
        // $excel->getActiveSheet()->setCellValue('D13', 'Số thẻ');
        // $excel->getActiveSheet()->setCellValue('E13', 'Số tháng');
        // $excel->getActiveSheet()->setCellValue('F13', 'Tỷ lệ trích nộp 50%');
        // $excel->getActiveSheet()->setCellValue('G13', 'Số tiền');
        // $excel->getActiveSheet()->setCellValue('H13', 'Từ');
        // $excel->getActiveSheet()->setCellValue('I13', 'Đến');

        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DoanLSHCM_BaoCaoNam.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;

    }

    /**
     * page báo cáo tổng hợp
     */

    public function reportfinalAction(){

    }

    /**
     * page office
     */
    public function officeAction(){
         $model = new Default_Model_OrganizationLawDetails();
         $data = $model->loadOrganzationsLaw();
         $this->view->organizations = $data;
    }

    /**
     * Export excel file with office
     */
    public function officedataexcelAction(){
        $this->_helper->layout('layout')->disableLayout();

        $search = $this->getRequest()->getParam('search');


        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $lawyer = new Default_Model_OrganizationLawDetails();
        $data = $lawyer->loadOrganzationsLawById($search);
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
        //    $sheet->mergeCells("G".($row_count+1).":I".($row_count+1));
        $excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A2:I2')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A3:I3')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A6:I6')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A8:I8')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A9:I9')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A10:I10')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A11:I11')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A12:I12')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A13:I13')->getFont()->setBold(true);

        //Tạo tiêu đề cho từng cột
        //Vị trí có dạng như sau:
        /**
         * |A1|B1|C1|..|n1|
         * |A2|B2|C2|..|n1|
         * |..|..|..|..|..|
         * |An|Bn|Cn|..|nn|
         */
        $excel->getActiveSheet()->mergeCells('A1:C1');
        $excel->getActiveSheet()
        ->getCell('A1')
        ->setValue('Biểu số:30/BTP/BTTP/LSTN');

        // $excel->getActiveSheet()->getStyle('A1')->getAlignment()->applyFromArray(
        //     array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        // );

        $excel->getActiveSheet()->mergeCells('E1:J1');
        $excel->getActiveSheet()
        ->getCell('E1')
        ->setValue('TÌNH HÌNH TỔ CHỨC VÀ HOẠT ĐỘNG CỦA LUẬT SƯ');

        $excel->getActiveSheet()->getStyle('E1')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('L1:O1');
        $excel->getActiveSheet()
        ->getCell('L1')
        ->setValue('Đơn vị báo cáo:');

        $excel->getActiveSheet()->getStyle('L1')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('A2:C2');
        $excel->getActiveSheet()
        ->getCell('A2')
        ->setValue('Ban hành kèm theo Thông tư số 04/2016/TT-BTP');

        // $excel->getActiveSheet()->getStyle('A2')->getAlignment()->applyFromArray(
        //     array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        // );

        $excel->getActiveSheet()->mergeCells('G2:H2');
        $excel->getActiveSheet()
        ->getCell('G2')
        ->setValue('(6 tháng, năm');

        $excel->getActiveSheet()->getStyle('G2')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('L2:O2');
        $excel->getActiveSheet()
        ->getCell('L2')
        ->setValue('-Tên đơn vị báo cáo:');

        $excel->getActiveSheet()->getStyle('G2')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('A3:C3');
        $excel->getActiveSheet()
        ->getCell('A3')
        ->setValue('ngày 03/03/2016');

        // $excel->getActiveSheet()->getStyle('A3')->getAlignment()->applyFromArray(
        //     array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        // );

        $excel->getActiveSheet()->mergeCells('L3:O3');
        $excel->getActiveSheet()
        ->getCell('L3')
        ->setValue('-Số điện thoại');

        $excel->getActiveSheet()->mergeCells('A4:C4');
        $excel->getActiveSheet()
        ->getCell('A4')
        ->setValue('Ngày nhận báo cáo(BC):');

        // $excel->getActiveSheet()->getStyle('A4')->getAlignment()->applyFromArray(
        //     array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        // );

        $excel->getActiveSheet()->mergeCells('L4:O4');
        $excel->getActiveSheet()
        ->getCell('L4')
        ->setValue('-Email:');

        $excel->getActiveSheet()->mergeCells('A5:C5');
        $excel->getActiveSheet()
        ->getCell('A5')
        ->setValue('Sở Tư pháp nhận');

        // $excel->getActiveSheet()->getStyle('A5')->getAlignment()->applyFromArray(
        //     array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        // );

        $excel->getActiveSheet()->mergeCells('F5:I5');
        $excel->getActiveSheet()
        ->getCell('F5')
        ->setValue('Kỳ báo cáo');

        $excel->getActiveSheet()->getStyle('F5')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('L5:O5');
        $excel->getActiveSheet()
        ->getCell('L5')
        ->setValue('Đơn vị nhận báo cáo');

        $excel->getActiveSheet()->getStyle('L5')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('A6:C6');
        $excel->getActiveSheet()
        ->getCell('A6')
        ->setValue('Báo cáo 6 tháng : ngày 06 tháng 06 hàng năm');

        $excel->getActiveSheet()->mergeCells('F6:I6');
        $excel->getActiveSheet()
        ->getCell('F6')
        ->setValue('Từ ngày.. tháng.. năm..');

        $excel->getActiveSheet()->getStyle('F6')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('L6:O6');
        $excel->getActiveSheet()
        ->getCell('L6')
        ->setValue('....................');

        $excel->getActiveSheet()->getStyle('L6')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        //row 7
        $excel->getActiveSheet()->mergeCells('A7:C7');
        $excel->getActiveSheet()
        ->getCell('A7')
        ->setValue('Báo cáo năm lần 1: ngày 07 tháng 11 hàng năm');

        $excel->getActiveSheet()->mergeCells('F7:I7');
        $excel->getActiveSheet()
        ->getCell('F7')
        ->setValue('đến ngày.. tháng.. năm.....)');

        $excel->getActiveSheet()->getStyle('F7')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('L7:O7');
        $excel->getActiveSheet()
        ->getCell('L7')
        ->setValue('-....................');

        $excel->getActiveSheet()->getStyle('L7')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        //row 8

        $excel->getActiveSheet()->mergeCells('A8:C8');
        $excel->getActiveSheet()
        ->getCell('A8')
        ->setValue('Báo cáo năm chính thức : ngày 15 tháng 02 năm sau');


        $excel->getActiveSheet()->mergeCells('A11:C11');
        $excel->getActiveSheet()
        ->getCell('A11')
        ->setValue('Số luật sư(LS) làm việc tại tổ chức hành nghề luật sư(TCNHNLS)(người)');

        $excel->getActiveSheet()->getStyle('A11')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('D11:K11');
        $excel->getActiveSheet()
        ->getCell('D11')
        ->setValue('Số việc thực hiện xong(việc)');

        $excel->getActiveSheet()->getStyle('D11')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('L11:O11');
        $excel->getActiveSheet()
        ->getCell('L11')
        ->setValue('Doanh thu(đồng)');

        $excel->getActiveSheet()->getStyle('L11')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        //

        $excel->getActiveSheet()->mergeCells('A12:A14');
        $excel->getActiveSheet()
        ->getCell('A12')
        ->setValue('Tổng số');

        $excel->getActiveSheet()->getStyle('A12')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('B12:C12');
        $excel->getActiveSheet()
        ->getCell('B12')
        ->setValue('Chia ra');

        $excel->getActiveSheet()->getStyle('B12')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('B13:B14');
        $excel->getActiveSheet()
        ->getCell('B13')
        ->setValue('Số LS trong nước làm việc tại TCHNLS');

        $excel->getActiveSheet()->getStyle('B13')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('C13:C14');
        $excel->getActiveSheet()
        ->getCell('C13')
        ->setValue('Số LS nước ngoài làm việc tại TCHNLS');

        $excel->getActiveSheet()->getStyle('C13')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('D12:D14');
        $excel->getActiveSheet()
        ->getCell('D12')
        ->setValue('Tổng số');

        $excel->getActiveSheet()->getStyle('D12')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('E12:K12');
        $excel->getActiveSheet()
        ->getCell('E12')
        ->setValue('Chia ra');

        $excel->getActiveSheet()->getStyle('E12')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('E13:G13');
        $excel->getActiveSheet()
        ->getCell('E13')
        ->setValue('Số việc tố tụng');

        $excel->getActiveSheet()->getStyle('E13')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        //$excel->getActiveSheet()->mergeCells('E13:G13');
        $excel->getActiveSheet()
        ->getCell('E14')
        ->setValue('Tổng số');

        $excel->getActiveSheet()->getStyle('E14')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('F14:G14');
        $excel->getActiveSheet()
        ->getCell('F14')
        ->setValue('Trong đó: số việc về hình sự');

        $excel->getActiveSheet()->getStyle('F14')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('H13:I14');

        $excel->getActiveSheet()
        ->getCell('H13')
        ->setValue('Số việc tư vấn pháp luật và dịch vụ pháp lý khác');

        $excel->getActiveSheet()->mergeCells('J13:K14');

        $excel->getActiveSheet()
        ->getCell('J13')
        ->setValue('Trợ giúp pháp lý');

        $excel->getActiveSheet()->mergeCells('L12:M14');

        $excel->getActiveSheet()
        ->getCell('L12')
        ->setValue('Tổng số́');

        $excel->getActiveSheet()->mergeCells('N12:O14');

        $excel->getActiveSheet()
        ->getCell('N12')
        ->setValue('Nộp thuế́́');





        // $excel->getActiveSheet()->getStyle('L11')->getAlignment()->applyFromArray(
        //     array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        // );

        // $excel->getActiveSheet()->setCellValue('A13', 'STT');
        // $excel->getActiveSheet()->setCellValue('B13', 'Họ Tên');
        // $excel->getActiveSheet()->setCellValue('C13', 'Năm sinh');
        // $excel->getActiveSheet()->setCellValue('D13', 'Số thẻ');
        // $excel->getActiveSheet()->setCellValue('E13', 'Số tháng');
        // $excel->getActiveSheet()->setCellValue('F13', 'Tỷ lệ trích nộp 50%');
        // $excel->getActiveSheet()->setCellValue('G13', 'Số tiền');
        // $excel->getActiveSheet()->setCellValue('H13', 'Từ');
        // $excel->getActiveSheet()->setCellValue('I13', 'Đến');

        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        // $numRow = 14;
        // $index = 0;
        // foreach($data as $row){

        //     $index += 1;
        //     $time=strtotime($row['payment_lawyer_off_created_date']);
        //     $month=date("m/Y",$time);

        //     $duration = $row['month'];
        //     $text = "+".$row['month']." months";
        //     $effectiveMonth = date('m/Y', strtotime($text, strtotime($row['payment_lawyer_off_created_date'])));

            $excel->getActiveSheet()->setCellValue('A16', $data['total_law_native']+$data['total_law_foreign']);
            $excel->getActiveSheet()->setCellValue('B16',$data['total_law_native']);
            $excel->getActiveSheet()->setCellValue('C16', $data['total_law_foreign']);
            $excel->getActiveSheet()->setCellValue('D16', $data['total_procedure']+$data['total_criminal']);
            $excel->getActiveSheet()->setCellValue('E16',$data['total_procedure']);
            $excel->getActiveSheet()->mergeCells('F16:G16');
            $excel->getActiveSheet()->setCellValue('F16',$data['total_criminal']);
            $excel->getActiveSheet()->mergeCells('H16:I16');
            $excel->getActiveSheet()->setCellValue('H16',$data['total_support_service']);
            $excel->getActiveSheet()->mergeCells('J16:K16');
            $excel->getActiveSheet()->setCellValue('J16',$data['total_support']);
            $excel->getActiveSheet()->mergeCells('L16:M16');
            $excel->getActiveSheet()->setCellValue('L16',$data['amount']);
            $excel->getActiveSheet()->mergeCells('N16:O16');
            $excel->getActiveSheet()->setCellValue('N16',$data['amount_charge']);

            $excel->getActiveSheet()->mergeCells('A18:J18');
            $excel->getActiveSheet()->setCellValue('A18',"- Văn phòng luật sư, công ty luật báo cáo từ cột (1) tới cột (10) của biểu này");
            $excel->getActiveSheet()->mergeCells('A19:J19');
            $excel->getActiveSheet()->setCellValue('A19',"- Số lượng ước tính ( số liệu ước tính 01 tháng đối với báo cáo 6 tháng; 02 tháng với báo cáo năm lần 1)");
            $excel->getActiveSheet()->mergeCells('A20:J20');
            $excel->getActiveSheet()->setCellValue('A20',"-Số liệu ước tính cột(4) .........; cột(9)..........;cột(10)..........");

            $excel->getActiveSheet()->setCellValue('B23',"Người lập biểu");
            $excel->getActiveSheet()->setCellValue('B24',"(Ký, ghi rõ họ, tên)");

            $excel->getActiveSheet()->mergeCells('J23:M23');
            $excel->getActiveSheet()->setCellValue('J23',".........ngày.....tháng..... năm.....");
            $excel->getActiveSheet()->mergeCells('J24:M24');
            $excel->getActiveSheet()->setCellValue('J24',"THỦ TRƯỞNG ĐƠN VỊ");


         //     $numRow++;
        // }
        // Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
        // ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Baocao_VPLS.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;
    }

    /**
     * datatable office
     */
    public function officedatatableAction(){

        // $start = $this->getRequest()->getParam('start');
        // $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        // $quarter = $this->getRequest()->getParam('quarter');
        // $year = $this->getRequest()->getParam('year');

        $model = new Default_Model_OrganizationLawDetails();
        $data = $model->loadOrganzationsLawById($search);

        $results = array(
        );

        if($data != null){

            array_push($results,[
            $data['organ_name'],
            $data['total_law_native'],
            $data['total_law_foreign'],
            $data['total_criminal'],
            $data['total_support_service'],
            $data['total_support'],
            number_format($data['amount']),
            number_format($data['amount_charge'])
            ]);
        }




        //$totalrecords = $model->countPaymentLawyerByFilter($start,$length,$search,$year);

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(1),
            "recordsFiltered" => intval(1),
            "data"            => $results
        );
       //$cattraining = new Default_Model_CategoryTraining();

        echo json_encode($json_data);
        exit;

    }

    public function indexAction(){

    }

    // xuat phi thanh vien
    public function reportlawyerfeeexportexcelAction() {
        $this->_helper->layout('layout')->disableLayout();

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $year = $this->getRequest()->getParam('year');
        $type = $this->getRequest()->getParam('type');

        //$modelOnline = new Default_Model_PaymentLawyerOnline();
        $modelOffline = new Default_Model_PaymentLawyerOffline();


        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $excel = new Default_Model_Excel();

        $excel->setActiveSheetIndex(0);

        $excel->getActiveSheet()->setTitle('Luật sư đóng phí thành viên');


        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

        //    $sheet->mergeCells("G".($row_count+1).":I".($row_count+1));
        $excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A2:I2')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A3:I3')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A6:I6')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A8:I8')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A9:I9')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A10:I10')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A11:I11')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A13:I13')->getFont()->setBold(true);

        $excel->getActiveSheet()->mergeCells('A1:C1');
        $excel->getActiveSheet()
        ->getCell('A1')
        ->setValue('ỦY BAN NHÂN DÂN THÀNH PHỐ HỒ CHÍ MINH');

        $excel->getActiveSheet()->getStyle('A1')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('A2:C2');
        $excel->getActiveSheet()
        ->getCell('A2')
        ->setValue('ĐOÀN LUẬT SƯ');

        $excel->getActiveSheet()->getStyle('A2')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('A3:C3');
        $excel->getActiveSheet()
        ->getCell('A3')
        ->setValue('THÀNH PHỐ HỒ CHÍ MINH');

        $excel->getActiveSheet()->getStyle('A3')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('E1:J1');
        $excel->getActiveSheet()
        ->getCell('E1')
        ->setValue('CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM');

        $excel->getActiveSheet()->getStyle('E1')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('F2:J2');
        $excel->getActiveSheet()
        ->getCell('F2')
        ->setValue('Độc lập - Tự do - Hạnh phúc');

        $excel->getActiveSheet()->getStyle('F2')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('A6:C6');
        $excel->getActiveSheet()
        ->getCell('A6')
        ->setValue('Số : 01/2018/PTV-ĐLSTPHCM');

        $excel->getActiveSheet()->getStyle('A6')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('F6:J6');
        $excel->getActiveSheet()
        ->getCell('F6')
        ->setValue('Thành phố Hồ Chí Minh, ngày 09 tháng 4 năm 2018');

        $excel->getActiveSheet()->getStyle('F6')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('B8:H8');
        $excel->getActiveSheet()
        ->getCell('B8')
        ->setValue('DANH SÁCH LUẬT SƯ TRÍCH NỘP PHÍ THÀNH VIÊN');

        $excel->getActiveSheet()->getStyle('B8')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('D9:F9');
        $excel->getActiveSheet()
        ->getCell('D9')
        ->setValue('QUÝ 1 NĂM 2018');

        $excel->getActiveSheet()->getStyle('D9')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('C10:G10');
        $excel->getActiveSheet()
        ->getCell('C10')
        ->setValue('Từ ngày : 01/01/2018 Đến ngày:31/03/2018');

        $excel->getActiveSheet()->getStyle('C10')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->mergeCells('B11:H11');
        $excel->getActiveSheet()
        ->getCell('B11')
        ->setValue('Kèm theo Công văn số : ĐLSTPHCM ngày 09/04/2018');

        $excel->getActiveSheet()->getStyle('B11')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );

        $excel->getActiveSheet()->setCellValue('A13', 'STT');
        $excel->getActiveSheet()->setCellValue('B13', 'Họ Tên');
        $excel->getActiveSheet()->setCellValue('C13', 'Năm sinh');
        $excel->getActiveSheet()->setCellValue('D13', 'Số thẻ');
        $excel->getActiveSheet()->setCellValue('E13', 'Số tháng');
        $excel->getActiveSheet()->setCellValue('F13', 'Tỷ lệ trích nộp 50%');
        $excel->getActiveSheet()->setCellValue('G13', 'Số tiền');
        $excel->getActiveSheet()->setCellValue('H13', 'Từ');
        $excel->getActiveSheet()->setCellValue('I13', 'Đến');

        // thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
        // dòng bắt đầu = 2
        $numRow = 14;
        $index = 0;

        $results = array(
        );

        // if($type == 'online'){
        $data = $modelOffline->loadPaymentLawyerByFilter('','',$search,$year,$type);
        if($data != null && sizeof($data)){
            //$index = 0;
            foreach($data as $pay){
                $index += 1;
                // $time=strtotime($pay['payment_lawyer_off_created_date']);
                // $month=date("m/Y",$time);

                // $duration = $pay['month'];
                // $text = "+".$pay['month']." months";
                // $effectiveMonth = date('m/Y', strtotime($text, strtotime($pay['payment_lawyer_off_created_date'])));

                $typeShow = 'Đóng trực tuyến';
                if($pay["payment_type"] == "offline"){
                    $typeShow = "Đóng tại đoàn";
                }

                array_push($results,[
                    $index,
                    $pay['cus_firstname'].' '.$pay['cus_lastname'],
                    date("d/m/Y", strtotime($pay['cus_birthday'])),
                    $pay['cus_lawyer_number'],
                    $pay['month'],
                    $pay['month'] != null ? round($pay['amount']/$pay['month']) : $pay['amount'] ,
                    number_format($pay['amount']),
                    $pay['startedmonth'] != null ? $pay['startedmonth'] : '',
                    $pay['endmonth'] != null ? $pay['endmonth'] : '',
                    $typeShow
                ]);
            }
        }


        foreach($results as $row){
            $excel->getActiveSheet()->setCellValue('A'.$numRow, $row[0]);
            $excel->getActiveSheet()->setCellValue('B'.$numRow, $row[1]);
            $excel->getActiveSheet()->setCellValue('C'.$numRow, $row[2]);
            $excel->getActiveSheet()->setCellValue('D'.$numRow, $row[3]);
            $excel->getActiveSheet()->setCellValue('E'.$numRow, $row[4]);
            $excel->getActiveSheet()->setCellValue('F'.$numRow, $row[5]);
            $excel->getActiveSheet()->setCellValue('G'.$numRow, $row[6]);
            $excel->getActiveSheet()->setCellValue('H'.$numRow, $row[7]);
            $excel->getActiveSheet()->setCellValue('I'.$numRow, $row[8]);
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

        $excel->getActiveSheet()->getStyle('A13:I'.$numRow)->applyFromArray($styleArray);
        $excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_DongPhiThanhVien.xls"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;
    }

    //xuat phi lien doan
    public function reportlawyerfeeeliendoanxportexcelAction() {
        $this->_helper->layout('layout')->disableLayout();

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $year = $this->getRequest()->getParam('year');
        $type = $this->getRequest()->getParam('type');

        //$modelOnline = new Default_Model_PaymentLawyerOnline();
        $modelOffline = new Default_Model_PaymentLawyerOffline();


        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $excel = new Default_Model_Excel();

        $excel->setActiveSheetIndex(0);

        $excel->getActiveSheet()->setTitle('Luật sư đóng phí liên đoàn');


        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

        //    $sheet->mergeCells("G".($row_count+1).":I".($row_count+1));
        $excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        // $excel->getActiveSheet()->getStyle('A2:I2')->getFont()->setBold(true);
        // $excel->getActiveSheet()->getStyle('A3:I3')->getFont()->setBold(true);
        // $excel->getActiveSheet()->getStyle('A6:I6')->getFont()->setBold(true);
        // $excel->getActiveSheet()->getStyle('A8:I8')->getFont()->setBold(true);
        // $excel->getActiveSheet()->getStyle('A9:I9')->getFont()->setBold(true);
        // $excel->getActiveSheet()->getStyle('A10:I10')->getFont()->setBold(true);
        // $excel->getActiveSheet()->getStyle('A11:I11')->getFont()->setBold(true);
        // $excel->getActiveSheet()->getStyle('A13:I13')->getFont()->setBold(true);



        $excel->getActiveSheet()->setCellValue('A1', 'STT');
        $excel->getActiveSheet()->setCellValue('B1', 'Họ Tên');
        $excel->getActiveSheet()->setCellValue('C1', 'Năm sinh');
        $excel->getActiveSheet()->setCellValue('D1', 'Số thẻ');
        $excel->getActiveSheet()->setCellValue('E1', 'Số tháng');
        $excel->getActiveSheet()->setCellValue('F1', 'Tỷ lệ trích nộp 50%');
        $excel->getActiveSheet()->setCellValue('G1', 'Số tiền');
        $excel->getActiveSheet()->setCellValue('H1', 'Từ');
        $excel->getActiveSheet()->setCellValue('I1', 'Đến');


        $numRow = 2;
        $index = 0;

        $results = array(
        );

        // if($type == 'online'){
        $data = $modelOffline->loadPaymentLawyerByFilter('','',$search,$year,$type);
        if($data != null && sizeof($data)){
            //$index = 0;
            foreach($data as $pay){
                $index += 1;
                $time=strtotime($pay['payment_lawyer_off_created_date']);
                $month=date("m/Y",$time);

                $duration = $pay['month'];
                $text = "+".$pay['month']." months";
                $effectiveMonth = date('m/Y', strtotime($text, strtotime($pay['payment_lawyer_off_created_date'])));

                $typeShow = 'Đóng trực tuyến';
                if($pay["payment_type"] == "offline"){
                    $typeShow = "Đóng tại đoàn";
                }

                array_push($results,[
                    $index,
                    $pay['cus_firstname'].' '.$pay['cus_lastname'],
                    date("d/m/Y", strtotime($pay['cus_birthday'])),
                    $pay['cus_lawyer_number'],
                    $pay['month'],
                    $pay['month'] != null ? round($pay['amount']/$pay['month']) : $pay['amount'] ,
                    number_format($pay['amount']),
                    $month,
                    $effectiveMonth,
                    $typeShow
                ]);
            }
        }

        foreach($results as $row){
            $excel->getActiveSheet()->setCellValue('A'.$numRow, $row[0]);
            $excel->getActiveSheet()->setCellValue('B'.$numRow, $row[1]);
            $excel->getActiveSheet()->setCellValue('C'.$numRow, $row[2]);
            $excel->getActiveSheet()->setCellValue('D'.$numRow, $row[3]);
            $excel->getActiveSheet()->setCellValue('E'.$numRow, $row[4]);
            $excel->getActiveSheet()->setCellValue('F'.$numRow, $row[5]);
            $excel->getActiveSheet()->setCellValue('G'.$numRow, $row[6]);
            $excel->getActiveSheet()->setCellValue('H'.$numRow, $row[7]);
            $excel->getActiveSheet()->setCellValue('I'.$numRow, $row[8]);
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

        $excel->getActiveSheet()->getStyle('A1:I'.$numRow)->applyFromArray($styleArray);
        $excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_DongPhiLienDoan.xls"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;
    }

    //function load danh sách luật sư đóng phí thành viên
    public function reportlawyerfeedatatableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $year = $this->getRequest()->getParam('year');
        $type = $this->getRequest()->getParam('type');

        $results = array(
        );

        //offline
        $model = new Default_Model_PaymentLawyerOffline();
        $index = 0;
        if($type == 'offline'){
            $data = $model->loadPaymentLawyerByFilter( $start,$length,$search,$year,$type);
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
                        $pay['month'] != null ? number_format(round($pay['amount']/$pay['month'])) : number_format($pay['amount']) ,
                        number_format($pay['amount']),
                        $pay['startedmonth'] != null ? $pay['startedmonth'] : '',
                        $pay['endmonth'] != null ? $pay['endmonth'] : '',
                        'Đóng tại đoàn'
                    ]);
                }
            }
        }


        //online
        if($type == 'online'){
            //$model = new Default_Model_PaymentLawyerOffline();
            $data = $model->loadPaymentLawyerByFilter($start,$length,$search,$year,$type);
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
                        $pay['month'] != null ? round($pay['amount']/$pay['month']) : $pay['amount'] ,
                        number_format($pay['amount']),
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
            $model = new Default_Model_PaymentLawyerOffline();
            $data = $model->loadPaymentLawyerByFilter($start,$length,$search,$year,$type);
            if($data != null && sizeof($data)){
                foreach($data as $pay){
                    $index += 1;
                    // $time=strtotime($pay['payment_lawyer_off_created_date']);
                    // $month=date("m/Y",$time);

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
                        $pay['month'] != null ? round($pay['amount']/$pay['month']) : $pay['amount'] ,
                        number_format($pay['amount']),
                        $pay['startedmonth'] != null ? $pay['startedmonth'] : '',
                        $pay['endmonth'] != null ? $pay['endmonth'] : '',
                        $text
                    ]);
                }
            }
        }
        $totalrecords = $model->countPaymentLawyerByFilter($search,$year,$type);

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

    // function load danh sách luật sư không tham gia bồi dưỡng
    public function reportlawyerwithouttrainingdatatableAction(){

        $this->_helper->layout('layout')->disableLayout();

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');
        $year = $this->getRequest()->getParam('year');

        $model = new Default_Model_PaymentTrainingOffline();

        $dataIds = $model->loadListLawyerIdTrained($year);

        $results = array(
        );
        if($dataIds != null && sizeof($dataIds)>0){
            foreach($dataIds as $data){
                array_push($results,$data['cus_id']);
            }
        }

        $data = $model->loadLawyerWithoutTrainingByFilter($start,$length,$results);

        $resultsFinal = array(
        );

        $dateEmpty = '1900-01-01 00:00:00';
        if($data != null && sizeof($data)){
            $index = 0;
            foreach($data as $pay){
                $index += 1;
                array_push($resultsFinal,[
                $index,
                $pay['cus_firstname'].' '.$pay['cus_lastname'],
                ($pay['cus_birthday'] != null && $pay['cus_birthday'] != '' && $pay['cus_birthday'] != $dateEmpty) ? date("d/m/Y", strtotime($pay['cus_birthday'])) : '',
                $pay['cus_identity_card'],
                $pay['law_joining_number'],
                ($pay['createddate'] != null && $pay['createddate'] != '' && $pay['createddate'] != $dateEmpty) ? date("d/m/Y", strtotime($pay['createddate'])) : '',
                $pay['cus_cellphone'],
                $pay['law_certfication_no'],
                ($pay['law_certification_createdate'] != null && $pay['law_certification_createdate'] != '' && $pay['law_certification_createdate'] != $dateEmpty) ? date("d/m/Y", strtotime($pay['law_certification_createdate'])) : '',
                $pay['cus_lawyer_number'],
                ($pay['cus_date_lawyer_number'] != null && $pay['cus_date_lawyer_number'] != '' && $pay['cus_date_lawyer_number'] != $dateEmpty) ? date("d/m/Y", strtotime($pay['cus_date_lawyer_number'])) : ''
                ]);
            }
        }

        $totalrecords = $model->loadLawyerWithoutTrainingByFilterTotals($results);

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval($totalrecords),
            "recordsFiltered" => intval($totalrecords),
            "data"            => $resultsFinal
        );

        echo json_encode($json_data);
        exit;
    }

    public function reportlawyerwithouttrainingexcelAction(){
        $this->_helper->layout('layout')->disableLayout();

        $year = $this->getRequest()->getParam('year');

        $model = new Default_Model_PaymentTrainingOffline();

        $dataIds = $model->loadListLawyerIdTrained($year);

        $results = array(
        );
        if($dataIds != null && sizeof($dataIds)>0){
            foreach($dataIds as $data){
                array_push($results,$data['cus_id']);
            }
        }

        $data = $model->loadLawyerWithoutTrainingByFilter('','',$results);

        $resultsFinal = array(
        );
        if($data != null && sizeof($data)){
            $index = 0;
            foreach($data as $pay){
                $index += 1;
                array_push($resultsFinal,[
                $index,
                $pay['cus_firstname'].' '.$pay['cus_lastname'],
                date("d/m/Y", strtotime($pay['cus_birthday'])),
                $pay['cus_identity_card'],
                $pay['law_joining_number'],
                date("d/m/Y", strtotime($pay['createddate'])),
                $pay['cus_cellphone'],
                $pay['law_certfication_no'],
                date("d/m/Y", strtotime($pay['law_certification_createdate'])),
                $pay['cus_lawyer_number'],
                date("d/m/Y", strtotime($pay['law_code_createdate']))
                ]);
            }
        }

        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $excel = new Default_Model_Excel();

        $excel->setActiveSheetIndex(0);

        $excel->getActiveSheet()->setTitle('LS không tham gia ĐTBD');


        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

        $excel->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold(true);

        $excel->getActiveSheet()->setCellValue('A1', 'STT');
        $excel->getActiveSheet()->setCellValue('B1', 'Họ Tên');
        $excel->getActiveSheet()->setCellValue('C1', 'Năm sinh');
        $excel->getActiveSheet()->setCellValue('D1', 'CMND');
        $excel->getActiveSheet()->setCellValue('E1', 'Đợt');
        $excel->getActiveSheet()->setCellValue('F1', 'Ngày gia nhập');
        $excel->getActiveSheet()->setCellValue('G1', 'Số ĐTDĐ');
        $excel->getActiveSheet()->setCellValue('H1', 'CCHN');
        $excel->getActiveSheet()->setCellValue('I1', 'Ngày Cấp');
        $excel->getActiveSheet()->setCellValue('J1', 'Số thẻ LS');
        $excel->getActiveSheet()->setCellValue('K1', 'Ngày Cấp');


        $numRow = 2;
        foreach($resultsFinal as $row){
            $excel->getActiveSheet()->setCellValue('A'.$numRow, $row[0]);
            $excel->getActiveSheet()->setCellValue('B'.$numRow, $row[1]);
            $excel->getActiveSheet()->setCellValue('C'.$numRow, $row[2]);
            $excel->getActiveSheet()->setCellValue('D'.$numRow, $row[3]);
            $excel->getActiveSheet()->setCellValue('E'.$numRow, $row[4]);
            $excel->getActiveSheet()->setCellValue('F'.$numRow, $row[5]);
            $excel->getActiveSheet()->setCellValue('G'.$numRow, $row[6]);
            $excel->getActiveSheet()->setCellValue('H'.$numRow, $row[7]);
            $excel->getActiveSheet()->setCellValue('I'.$numRow, $row[8]);
            $excel->getActiveSheet()->setCellValue('J'.$numRow, $row[8]);
            $excel->getActiveSheet()->setCellValue('K'.$numRow, $row[8]);
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

        $excel->getActiveSheet()->getStyle('A1:K'.$numRow)->applyFromArray($styleArray);
        foreach(range('A','K') as $columnID) {
            $excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        //$excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_KhongThamGiaDaoTao.xls"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;
    }

    // function load danh sách luật sư khen thưởng kỷ luật
    public function reportcustomersrewardtableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $year = $this->getRequest()->getParam('year');

        $model = new Default_Model_CustomersRewardDiscipline();
        $data = $model->loadCustomersRewardByFilter($start,$length,$year);

        $results = array(
        );

        if($data != null && sizeof($data)){
            $index = 0;
            foreach($data as $pay){
                $index += 1;

                array_push($results,[
                $index,
                $pay['people_problem'] != null ? $pay['people_problem'] : '' ,
                ($pay['discipline_date'] != null && $pay['discipline_date'] != '' && $pay['discipline_date'] != '1900-01-01 00:00:00' ) ? date("d/m/Y", strtotime($pay['discipline_date'])) : '',
                $pay['cus_firstname'].' '.$pay['cus_lastname'],
                $pay['discipline_reason'] != null ? $pay['discipline_reason'] : '' ,
                $pay['law_help'] != null ? $pay['law_help'] : ''
                ]);
            }
        }


        $totalrecords = $model->loadCustomersRewardByFilter($start,$length,$year);

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($totalrecords)),
            "recordsFiltered" => intval(count($totalrecords)),
            "data"            => $results
        );

        echo json_encode($json_data);
        exit;
    }

    // load danh sach luat su no tien phi thanh vien

    public function reportlawyerdebtAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $year = $this->getRequest()->getParam('year');

        $model = new Default_Model_Lawyer();
        $modelCustomer = new Default_Model_Customer();
        $data = $model->loadLawyerDebt($start,$length);

        $results = array(
        );

        $index = 0;
        if($data != null && sizeof($data)){
            foreach($data as $pay){
               if($pay['cus_id'] != null){
                    $endmonth = $modelCustomer->getEndMonthByCusId($pay['cus_id']);

                    if($endmonth != null && $endmonth != ''){
                        $index += 1;
                        array_push($results,[
                            $pay['law_id'],
                            $pay['cus_firstname'].' '.$pay['cus_lastname'],
                            $pay['cus_lawyer_number'] != null ? $pay['cus_lawyer_number'] : '' ,
                            $pay['law_certfication_no'] != null ? $pay['law_certfication_no'] : '' ,
                            ($pay['law_certification_createdate'] != null && $pay['law_certification_createdate'] != '' && $pay['law_certification_createdate'] != '1900-01-01 00:00:00' ) ?
                            date("d/m/Y", strtotime($pay['law_certification_createdate'])) : '',
                            $endmonth
                        ]);
                    }
               }
            }
        }


        $totalrecords = $model->loadLawyerDebt('','');

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($totalrecords)),
            "recordsFiltered" => intval(count($totalrecords)),
            "data"            => $results
        );

        echo json_encode($json_data);
        exit;
    }

}
