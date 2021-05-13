<?php
// Customers controller
class CustomerStatusController extends Zend_Controller_Action
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
        //$this->_helper->layout()->disableLayout();
    }

    public function loan18excelAction(){
        $this->_helper->layout('layout')->disableLayout();

        $model = new Default_Model_Lawyer();
        $modelCustomer = new Default_Model_Customer();
        $data = $model->loadLawyerDebt('','');

        $results = array(
        );

        if($data != null && sizeof($data)){
            $index = 0;
            foreach($data as $pay){
                $index += 1;
                $endmonth = $modelCustomer->getEndMonthByCusId($pay['cus_id']);
                array_push($results,[
                $pay['law_id'],
                $pay['cus_firstname'].' '.$pay['cus_lastname'],
                $pay['cus_lawyer_number'] != null ? $pay['cus_lawyer_number'] : '' ,
                $pay['law_certfication_no'] != null ? $pay['law_certfication_no'] : '' ,
                ($pay['law_certification_createdate'] != null && $pay['law_certification_createdate'] != '' && $pay['law_certification_createdate'] != '1900-01-01 00:00:00' ) ?
                 date("d/m/Y", strtotime($pay['law_certification_createdate'])) : '',
                 $endmonth
                // $pay['debt'] != null ? $pay['debt'] : 0,
                // $pay['month'] != null ? $pay['month'] : 0
                ]);
            }
        }

        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $excel = new Default_Model_Excel();

        $excel->setActiveSheetIndex(0);

        $excel->getActiveSheet()->setTitle('LS nợ PTV hơn 18 tháng');


        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        // $excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);

        $excel->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);

        //$excel->getActiveSheet()->setCellValue('A1', 'STT');
        $excel->getActiveSheet()->setCellValue('A1', 'Họ Tên');
        $excel->getActiveSheet()->setCellValue('B1', 'Số thẻ LS');
        $excel->getActiveSheet()->setCellValue('C1', 'CCHN');
        $excel->getActiveSheet()->setCellValue('D1', 'Ngày cấp CCHN');
        $excel->getActiveSheet()->setCellValue('E1', 'Đóng tới');


        $numRow = 2;
        foreach($results as $row){
            $excel->getActiveSheet()->setCellValue('A'.$numRow, $row[1]);
            $excel->getActiveSheet()->setCellValue('B'.$numRow, $row[2]);
            $excel->getActiveSheet()->setCellValue('C'.$numRow, $row[3]);
            $excel->getActiveSheet()->setCellValue('D'.$numRow, $row[4]);
            $excel->getActiveSheet()->setCellValue('E'.$numRow, $row[5]);
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

        $excel->getActiveSheet()->getStyle('A1:E'.$numRow)->applyFromArray($styleArray);
        foreach(range('A','E') as $columnID) {
            $excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        //$excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="DS_LuatSu_No18Thang.xls"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        return;
    }
}