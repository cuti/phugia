<?php

class Default_Model_PaymentLawyerOffline  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'payment_lawyer_offline';

    protected $_primary = 'payment_lawyer_off_id'; 

    protected $_sequence = true;

    /**count monney in year */

    public function countMooneyInYear($year){

        $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';

        $checkquery = $this->select()
        ->from($this, array("SUM(amount) as total"))
        ->where("payment_lawyer_off_status = ?", '1')
        ->where("payment_lawyer_off_created_date >= ?",  $start_date_formatted)
        ->where("payment_lawyer_off_created_date <= ?",  $end_date_formatted);                
        

        $checkrequest = $this->fetchRow($checkquery);

        return $checkrequest["total"];

    }

    //load phí thành viên bằng cus_id
    public function countPaymentLawyerOfflineByCusId($cus_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->from('payment_lawyer_offline',array('payment_lawyer_off_id'))
        // ->joinInner(
        //     'bill_fee_offline',
        //     'bill_fee_offline.bill_feeoffline_id = payment_lawyer_offline.bill_feeoffline_id',
        //     array())
        ->where('payment_lawyer_offline.cus_id = ?', $cus_id)
        ->where('payment_lawyer_offline.payment_lawyer_off_status = ?','1');
         
        $row = $db->fetchAll($query);
        return $row;
    }

    
    //tạo mã code
    public function generationCode($paymentType,$paymentOption){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $currentdate = new Zend_Date();
        $monthYear = $currentdate->toString('MMYYYY');
        $checkquery = $db->select()
        ->from("payment_lawyer_offline", array('payment_lawyer_off_id'))
        ->order('payment_lawyer_off_id desc')
        ->limit(1);

        $checkrequest = $db->fetchRow($checkquery);      

        $text = '';
        $length = strlen($checkrequest["payment_lawyer_off_id"]);
        if($length > 0){
            $id = $checkrequest["payment_lawyer_off_id"] + 1;
            $lengthid = strlen($id);
            if($lengthid == 1){
                $text = '000'.$id.'-'.$monthYear.'-'.$paymentType;
            }else if($lengthid == 2){
                $text = '00'.$id.'-'.$monthYear.'-'.$paymentType;    
            }else if($lengthid == 3){
                $text = '0'.$id.'-'.$monthYear.'-'.$paymentType;
            }else if($lengthid == 4){
                $text = $id.'-'.$monthYear.'-'.$paymentType;
            }else{
                $text = $id.'-'.$monthYear.'-'.$paymentType;
            }           
        }else{
            $text = '0001'.'-'.$monthYear.'-'.$paymentType;
        }
        return $text;
    }


    // load phí thành viên by id
    public function loadPaymentLawyerOfflinegById($id){
        $row = $this->fetchRow('payment_lawyer_off_id = ' .(int) $id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    // load phí thanh viên bằng cus_id
    public function loadPaymentLawyerOfflineByCusId($cus_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_lawyer_offline', array('payment_lawyer_off_id'
            ,'payment_lawyer_off_code'
            ,'payment_lawyer_off_created_date'
            ,'payment_lawyer_off_status'
            ,'payment_lawyer_off_updatedate'
            ,'amount'
            ,'month'
            ,'startedmonth'
            ,'endmonth'
            ))           
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_lawyer_offline.category_fee_id',
            array('name'=>'name'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_lawyer_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname'
                ,'cus_lawyer_number'=>'cus_lawyer_number'))    
            ->joinLeft(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code','law_certfication_no'=>'law_certfication_no'))  
            ->where('customers.cus_id = ?',$cus_id)
            ->where('payment_lawyer_offline.payment_lawyer_off_status != ?' ,-1)
            ->where('payment_lawyer_offline.payment_lawyer_off_status != ?' , 2)
            ->order('payment_lawyer_offline.payment_lawyer_off_id desc');
                        
        $row = $db->fetchAll($select);
        return $row;
    }
    

    // load danh sách phí thành viên
    public function loadPaymentLawyerOffline()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_lawyer_offline', array(
            'payment_lawyer_off_id'
            ,'payment_lawyer_off_code'
            ,'payment_lawyer_off_created_date'
            ,'payment_lawyer_off_status'
            ,'payment_lawyer_off_updatedate'
            ,'amount'
            ,'month'
            ,'payment_type'
            ,'startedmonth'
            ,'endmonth'
            ))           
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_lawyer_offline.category_fee_id',
            array('name'=>'name','category_fee_id'=>'category_fee_id'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_lawyer_offline.cus_id',
                array('cus_lawyer_number' => 'cus_lawyer_number','cus_firstname'=>'cus_firstname',
                 'cus_lastname' =>'cus_lastname','cus_lawyer_number' => 'cus_lawyer_number'))    
            // ->joinLeft(
            //     'lawyer',
            //     'lawyer.cus_id = customers.cus_id',
            //     array('law_code'=>'law_code','law_certfication_no'=>'law_certfication_no'))
            ->where('payment_lawyer_offline.payment_lawyer_off_status = 0')
            ->order('payment_lawyer_offline.payment_lawyer_off_id DESC');
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    // load phí thành viên bằng id
    public function loadPaymentLawyerOfflineById($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        // $select = new Zend_Db_Select($db);
        $query = $db->select()
            ->from("payment_lawyer_offline", array('payment_lawyer_off_id'
            ,'payment_lawyer_off_code'
            ,'payment_lawyer_off_created_date'
            ,'payment_lawyer_off_status'
            ,'payment_lawyer_off_updatedate'
            ,'month'
            ,'amount'
            ,'startedmonth'
            ,'endmonth'
            ,'cus_id'))           
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_lawyer_offline.category_fee_id',
            array('name'=>'name'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_lawyer_offline.cus_id',
            array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
            'cus_birthday'=>'cus_birthday','cus_lawyer_number' => 'cus_lawyer_number'))   
            ->joinLeft(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_joining_number'=>'law_joining_number','law_code'=>'law_code','law_certfication_no'=>'law_certfication_no'))
            ->where('payment_lawyer_offline.payment_lawyer_off_id = ?',$id)
            ->limit(1);   
                        
        return $db->fetchRow($query);
        // if (!$row) {
        //     return null;
        // }
        //return $row->toArray();
    }

    //count phí thành viên bằng id
    public function countPaymentLawyerByFilter($time,$year,$type){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->from('payment_lawyer_offline', array('payment_lawyer_off_id'          
            ))           
            ->order('payment_lawyer_offline.payment_lawyer_off_created_date')
            ->where('payment_lawyer_offline.payment_lawyer_off_status = 1');

            if($time == 1){
                $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
                $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-03-31')))).' 00:00:00';
    
                $query = $query
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date >= ?",  $start_date_formatted)
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date <= ?",  $end_date_formatted);                
            }else if($time == 2){
                $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-04-01')))).' 00:00:00';          
                $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-06-30')))).' 00:00:00';
                $query = $query
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date >= ?",  $start_date_formatted)
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date <= ?",  $end_date_formatted);                

            }else if($time == 3){
                $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-07-01')))).' 00:00:00';          
                $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-09-30')))).' 00:00:00';
                $query = $query
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date >= ?",  $start_date_formatted)
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date <= ?",  $end_date_formatted);                
            
            }else if($time == 4){
                $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-10-01')))).' 00:00:00';          
                $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';
                       
                $query = $query
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date >= ?",  $start_date_formatted)
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date <= ?",  $end_date_formatted);                
            }    

            if($type != 'all'){
                $query = $query
                ->where('payment_lawyer_offline.payment_type = ?',$type);
            }
                        
        $row = $db->fetchAll($query);
        return $row;
        
    }

    //load fee member pagination
    //using in report controller
    public function loadPaymentLawyerByFilter($start, $length, $time,$year,$type){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('payment_lawyer_offline', array('payment_lawyer_off_id'
            ,'payment_lawyer_off_code'
            ,'payment_lawyer_off_created_date'
            ,'payment_lawyer_off_status'
            ,'payment_lawyer_off_updatedate'
            ,'amount'
            ,'startedmonth'
            ,'endmonth'
            ,'payment_type'
            ,'month'
            ,'category_fee_id'
            ,'cus_id'
            ))
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_lawyer_offline.category_fee_id',
            array('name'=>'name'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_lawyer_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
                'cus_birthday'=>'cus_birthday','cus_lawyer_number' => 'cus_lawyer_number'))    
            ->joinLeft(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code','law_certfication_no'=>'law_certfication_no'))
            ->order('payment_lawyer_offline.payment_lawyer_off_created_date')
            ->where('payment_lawyer_offline.payment_lawyer_off_status = 1');

            if($time == 1){
                $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
                $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-03-31')))).' 00:00:00';
    
                $query = $query
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date >= ?",  $start_date_formatted)
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date <= ?",  $end_date_formatted);                
            }else if($time == 2){
                $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-04-01')))).' 00:00:00';          
                $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-06-30')))).' 00:00:00';
                $query = $query
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date >= ?",  $start_date_formatted)
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date <= ?",  $end_date_formatted);                

            }else if($time == 3){
                $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-07-01')))).' 00:00:00';          
                $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-09-30')))).' 00:00:00';
                $query = $query
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date >= ?",  $start_date_formatted)
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date <= ?",  $end_date_formatted);                
            
            }else if($time == 4){
                $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-10-01')))).' 00:00:00';          
                $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';
                       
                $query = $query
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date >= ?",  $start_date_formatted)
                ->where("payment_lawyer_offline.payment_lawyer_off_created_date <= ?",  $end_date_formatted);                
            
            }    

            if($type != 'all'){
                $query = $query
                ->where('payment_lawyer_offline.payment_type = ?',$type);
            }
                        
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);

    }



    //load fee lawyer paging

    public function loadPaymentLawyerByFilterStatisticTotals($startdate,$enddate,$type){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->from('payment_lawyer_offline', array('payment_lawyer_off_id'))
            ->where('payment_lawyer_offline.payment_lawyer_off_status = 1');
        
        $startdate = str_replace('/', '-', $startdate);
        $enddate = str_replace('/', '-', $enddate);
        $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';

        $query = $query
        ->where("payment_lawyer_offline.payment_lawyer_off_created_date >= ?",  $start_date_formatted)
        ->where("payment_lawyer_offline.payment_lawyer_off_created_date <= ?",  $end_date_formatted);


        if($type == 'online'){
            $query = $query
            ->where('payment_lawyer_offline.payment_type = ?','online')
            ->order('payment_lawyer_offline.payment_lawyer_off_created_date desc');
        }else if($type == 'offline'){
            $query = $query
            ->where('payment_lawyer_offline.payment_type = ?','offline')
            ->order('payment_lawyer_offline.payment_lawyer_off_created_date desc');
        }else{
            $query = $query           
            ->order('payment_lawyer_offline.payment_lawyer_off_id desc');
        }
      
        $row = $db->fetchAll($query);
        $rowCount = count($row);
        if ($rowCount < 0) {
            $rowCount = 0;
        }
        return $rowCount;
    }

    //using in statistic controller
    public function loadPaymentLawyerByFilterStatistic($start, $length, $startdate,$enddate,$type){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('payment_lawyer_offline', array('payment_lawyer_off_id'
            ,'payment_lawyer_off_code'
            ,'payment_lawyer_off_created_date'
            ,'payment_lawyer_off_status'
            ,'payment_lawyer_off_updatedate'
            ,'amount'
            ,'startedmonth'
            ,'endmonth'
            ,'payment_type'
            ,'month'
            ,'category_fee_id'
            ,'cus_id'
            ))
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_lawyer_offline.category_fee_id',
            array('name'=>'name'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_lawyer_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
                'cus_birthday'=>'cus_birthday','cus_lawyer_number' => 'cus_lawyer_number'))    
            ->joinLeft(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code','law_certfication_no'=>'law_certfication_no'))            
            ->where('payment_lawyer_offline.payment_lawyer_off_status = 1');

            $startdate = str_replace('/', '-', $startdate);
            $enddate = str_replace('/', '-', $enddate);
            $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
            $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';

            $query = $query
            ->where("payment_lawyer_offline.payment_lawyer_off_created_date >= ?",  $start_date_formatted)
            ->where("payment_lawyer_offline.payment_lawyer_off_created_date <= ?",  $end_date_formatted);


            if($type == 'online'){
                $query = $query
                ->where('payment_lawyer_offline.payment_type = ?','online')
                ->order('payment_lawyer_offline.payment_lawyer_off_created_date desc');
            }else if($type == 'offline'){
                $query = $query
                ->where('payment_lawyer_offline.payment_type = ?','offline')
                ->order('payment_lawyer_offline.payment_lawyer_off_created_date desc');
            }else{
                $query = $query           
                ->order('payment_lawyer_offline.payment_lawyer_off_created_date desc');
            }
                        
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);

    }

    
}