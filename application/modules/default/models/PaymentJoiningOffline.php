<?php

class Default_Model_PaymentJoiningOffline  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'payment_joining_offline';

    protected $_primary = 'payment_joining_off_id'; 

    protected $_sequence = true;

    /**count monney in year */

    public function countMooneyInYear($year){

        $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';

        $checkquery = $this->select()
        ->from($this, array("SUM(amount) as total"))
        ->where("payment_joining_off_status = ?", '1')
        ->where("payment_joining_off_created_date >= ?",  $start_date_formatted)
        ->where("payment_joining_off_created_date <= ?",  $end_date_formatted);                
        

        $checkrequest = $this->fetchRow($checkquery);

        return $checkrequest["total"];

    }

    public function countPaymentJoiningOfflineByCusId($cus_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->from('payment_joining_offline',array('payment_joining_off_id'))
        // ->joinInner(
        //     'bill_fee_offline',
        //     'bill_fee_offline.bill_feeoffline_id = payment_joining_offline.bill_feeoffline_id',
        //     array())
        ->where('payment_joining_offline.cus_id = ?', $cus_id)
        ->where('payment_joining_offline.payment_joining_off_status = ?','1');
         
        $row = $db->fetchAll($query);
        return $row;
    }

    public function generationCode($paymentType,$paymentOption){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $currentdate = new Zend_Date();
        $monthYear = $currentdate->toString('MMYYYY');
        $checkquery = $db->select()
        ->from("payment_joining_offline", array('payment_joining_off_id'))
        ->order('payment_joining_off_id desc')
        ->limit(1);

        $checkrequest = $db->fetchRow($checkquery);      

        $text = '';
        $length = strlen($checkrequest["payment_joining_off_id"]);
        if($length > 0){
            $id = $checkrequest["payment_joining_off_id"] + 1;
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

    /*load oayment joining ofiline by filter*/
    public function loadPaymentJoiningOfflineByFilter($start,$length,$year)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('payment_joining_offline', array('payment_joining_off_id'
            ,'payment_joining_off_code'
            ,'payment_joining_off_created_date'
            ,'payment_joining_off_status'
            ,'payment_joining_off_updatedate'            
            ,'amount'))           
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_joining_offline.category_fee_id',
            array('name'=>'name','category_fee_id'=>'category_fee_id'))
            ->joinInner(
            'customers',
            'customers.cus_id = payment_joining_offline.cus_id',
            array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
            'cus_lawyer_number' => 'cus_lawyer_number',
            'cus_date_lawyer_number' => 'cus_date_lawyer_number',
            'cus_identity_card' => 'cus_identity_card',
            'cus_address_resident' => 'cus_address_resident',
            'cus_address_resident_now' => 'cus_address_resident_now'
            ))   
            ->joinInner(
            'lawyer',
            'lawyer.cus_id = customers.cus_id',
            array('law_certfication_no'=>'law_certfication_no',
            'law_certification_createdate'=>'law_certification_createdate'))
            //->where('customers.cus_id = ?',$cus_id)
            ->order('payment_joining_offline.payment_joining_off_created_date');

        $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';

        $query = $query
        ->where("payment_joining_offline.payment_joining_off_created_date >= ?",  $start_date_formatted)
        ->where("payment_joining_offline.payment_joining_off_created_date <= ?",  $end_date_formatted);                
            
        $row = $db->fetchAll($select);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
    }

    /*load oayment joining ofiline by filter*/
    public function loadPaymentJoiningOfflineStatisticByFilter($start,$length,$type,$startdate,$enddate)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('payment_joining_offline', array('payment_joining_off_id'
            ,'payment_joining_off_code'
            ,'payment_joining_off_created_date'
            ,'payment_joining_off_status'
            ,'payment_joining_off_updatedate' 
            ,'payment_type'           
            ,'amount'))           
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_joining_offline.category_fee_id',
            array('name'=>'name','category_fee_id'=>'category_fee_id'))
            ->joinInner(
            'customers',
            'customers.cus_id = payment_joining_offline.cus_id',
            array('cus_firstname'=>'cus_firstname',
             'cus_lastname' =>'cus_lastname',
             'cus_identity_card' => 'cus_identity_card',
             'cus_cellphone' => 'cus_cellphone','cus_lawyer_number' => 'cus_lawyer_number'))   
            ->joinLeft(
            'lawyer',
            'lawyer.cus_id = customers.cus_id',
            array('law_code'=>'law_code','law_certfication_no'=>'law_certfication_no'));
            //->where('customers.cus_id = ?',$cus_id)
            //->order('payment_joining_offline.payment_joining_off_created_date');

        $startdate = str_replace('/', '-', $startdate);
        $enddate = str_replace('/', '-', $enddate);
        $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';

        $query = $query
        ->where("payment_joining_offline.payment_joining_off_created_date >= ?",  $start_date_formatted)
        ->where("payment_joining_offline.payment_joining_off_created_date <= ?",  $end_date_formatted);                
            
        // if($type != 'all'){
            $query = $query
            ->where('payment_joining_offline.payment_type = ?','offline')
            ->order('payment_joining_offline.payment_joining_off_created_date desc');
        // }

        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
    }


    /*load oayment joining ofiline by cus id*/
    public function loadPaymentJoiningOfflineByCusId($cus_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_joining_offline', array('payment_joining_off_id'
            ,'payment_joining_off_code'
            ,'payment_joining_off_created_date'
            ,'payment_joining_off_status'
            ,'payment_joining_off_updatedate'
            ,'amount'))           
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_joining_offline.category_fee_id',
            array('name'=>'name'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_joining_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname'))   
           
            ->where('customers.cus_id = ?',$cus_id)
            ->order('payment_joining_offline.payment_joining_off_created_date desc');
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    public function loadPaymentJoiningOfflineById($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        // $select = new Zend_Db_Select($db);
        $query = $db->select()
            ->from("payment_joining_offline", array('payment_joining_off_id'
            ,'payment_joining_off_code'
            ,'payment_joining_off_created_date'
            ,'payment_joining_off_status'
            ,'payment_joining_off_updatedate'    
            ,'cus_id'        
            ,'amount'
            ,'history_id'))
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_joining_offline.category_fee_id',
            array('name'=>'name'))          
            ->joinInner(
            'customers',
            'customers.cus_id = payment_joining_offline.cus_id',
            array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
            'cus_birthday'=>'cus_birthday','cus_lawyer_number' => 'cus_lawyer_number',
            'cus_date_lawyer_number' => 'cus_date_lawyer_number'))   
            ->joinLeft(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
            array('law_code'=>'law_code', 'law_code_createdate' =>'law_code_createdate'))
            ->where('payment_joining_offline.payment_joining_off_id = ?',$id)
            ->limit(1);   
                        
        return $db->fetchRow($query);
    }
    

    public function loadPaymentJoiningOffline()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from('payment_joining_offline', array(
            'payment_joining_off_id'
            ,'payment_joining_off_code'
            ,'payment_joining_off_created_date'
            ,'payment_joining_off_status'
            ,'payment_joining_off_updatedate'
            ,'amount'
            ,'payment_type'
            ))           
            ->joinLeft(
            'category_fee',
            'category_fee.category_fee_id = payment_joining_offline.category_fee_id',
            array('name'=>'name'))
            ->joinLeft(
                'customers',
                'customers.cus_id = payment_joining_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
                'cus_lawyer_number' => 'cus_lawyer_number'))    
            // ->joinLeft(
            //     'lawyer',
            //     'lawyer.cus_id = customers.cus_id',
            //     array('law_code'=>'law_code','law_certfication_no'=>'law_certfication_no'))
            ->where('payment_joining_offline.payment_joining_off_status = 0')
            ->order('payment_joining_offline.payment_joining_off_id DESC');
            
            //->where('customers.cus_id = ?',$cus_id);
                        
        $row = $db->fetchAll($select);
        return $row;
    }
}