<?php

class Default_Model_PaymentLawyerOnline  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'payment_lawyer';

    protected $_primary = 'payment_lawyer_id'; 

    protected $_sequence = true;

    // tính phí thành viên online bởi cus_id
    public function countPaymentLawyerOnlineByCusId($cus_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->from('payment_lawyer',array('payment_lawyer_id'))
        // ->joinInner(
        //     'bill_fee_lawyer_temp',
        //     'bill_fee_lawyer_temp.bill_feelawyer_temp_id = payment_lawyer.bill_feelawyer_temp_id',
        //     array())
        ->where('payment_lawyer.customer_id = ?', $cus_id);
        //->where('payment_lawyer.payment_lawyer_status = ?','1');
         
        $row = $db->fetchAll($query);
        return $row;
    }

    //load phí thành viên online có phân trang 
    public function loadPaymentLawyerOnlineByFilter($time,$year){

            $db = Zend_Db_Table::getDefaultAdapter();
            $select = new Zend_Db_Select($db);
            $query = $select->distinct()
                ->from('payment_lawyer', array(
                'payment_lawyer_id'
                ,'payment_lawyer_code'
                ,'payment_lawyer_created_at'
                ,'payment_lawyer_status'
                ,'payment_lawyer_updated_at'
                ,'customer_id'
                ,'year_of_payment'
                ,'number_month_of_payment'
                ,'payment_data'
                ,'amount'))               
                ->joinInner(
                    'customers',
                    'customers.cus_id = payment_lawyer.customer_id',
                    array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
                    'cus_birthday'=>'cus_birthday','cus_lawyer_number' => 'cus_lawyer_number'))    
                ->joinInner(
                    'lawyer',
                    'lawyer.cus_id = customers.cus_id',
                    array('law_code'=>'law_code','law_certfication_no'=>'law_certfication_no'))
                ->where('payment_lawyer.payment_lawyer_status = ?','1')    
                ->order('payment_lawyer.payment_lawyer_created_at DESC');
                    
                if($time == 1){
                    $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
                    $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-03-31')))).' 00:00:00';
        
                    $query = $query
                    ->where("payment_lawyer.payment_lawyer_created_at >= ?",  $start_date_formatted)
                    ->where("payment_lawyer.payment_lawyer_created_at <= ?",  $end_date_formatted);                
                }else if($time == 2){
                    $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-04-01')))).' 00:00:00';          
                    $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-06-30')))).' 00:00:00';
                    $query = $query
                    ->where("payment_lawyer.payment_lawyer_created_at >= ?",  $start_date_formatted)
                    ->where("payment_lawyer.payment_lawyer_created_at <= ?",  $end_date_formatted);                
    
                }else if($time == 3){
                    $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-07-01')))).' 00:00:00';          
                    $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-09-30')))).' 00:00:00';
                    $query = $query
                    ->where("payment_lawyer.payment_lawyer_created_at >= ?",  $start_date_formatted)
                    ->where("payment_lawyer.payment_lawyer_created_at <= ?",  $end_date_formatted);                
                
                }else if($time == 4){
                    $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-10-01')))).' 00:00:00';          
                    $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';
                           
                    $query = $query
                    ->where("payment_lawyer.payment_lawyer_created_at >= ?",  $start_date_formatted)
                    ->where("payment_lawyer.payment_lawyer_created_at <= ?",  $end_date_formatted);                
                
                }    
                            
            $row = $db->fetchAll($query);            
            return $row;
    
    }


}