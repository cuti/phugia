<?php

class Default_Model_PaymentIntershipOffline  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'payment_intership_offline';

    protected $_primary = 'payment_inter_off_id'; 

    protected $_sequence = true;

    /**count monney in year */

    public function countMooneyInYear($year){

        $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';

        $checkquery = $this->select()
       ->from($this, array("SUM(amount) as total"))
       ->where("payment_inter_off_status = ?", '1')
       ->where("payment_inter_off_created_date >= ?",  $start_date_formatted)
       ->where("payment_inter_off_created_date <= ?",  $end_date_formatted);                
      

       $checkrequest = $this->fetchRow($checkquery);

       return $checkrequest["total"];

   }

    public function countPaymentIntershipOfflineByCusId($cus_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->from('payment_intership_offline',array('payment_inter_off_id'))
        // ->joinInner(
        //     'bill_fee_offline',
        //     'bill_fee_offline.bill_feeoffline_id = payment_intership_offline.bill_feeoffline_id',
        //     array())
        ->where('payment_intership_offline.cus_id = ?', $cus_id)
        ->where('payment_intership_offline.payment_inter_off_status = ?','1');
         
        $row = $db->fetchAll($query);
        return $row;
    }

    
    public function generationCode($paymentType,$paymentOption){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $currentdate = new Zend_Date();
        $monthYear = $currentdate->toString('MMYYYY');
        $checkquery = $db->select()
        ->from("payment_intership_offline", array('payment_inter_off_id'))
        ->order('payment_inter_off_id desc')
        ->limit(1);

        $checkrequest = $db->fetchRow($checkquery);      

        $text = '';
        $length = strlen($checkrequest["payment_inter_off_id"]);
        if($length > 0){
            $id = $checkrequest["payment_inter_off_id"] + 1;
            if($length == 1){
                $text = '000'.$id.'-'.$monthYear.'-'.$paymentType;
            }else if($length == 2){
                $text = '00'.$id.'-'.$monthYear.'-'.$paymentType;    
            }else if($length == 3){
                $text = '0'.$id.'-'.$monthYear.'-'.$paymentType;
            }else if($length == 4){
                $text = $id.'-'.$monthYear.'-'.$paymentType;
            }else{
                $text = $id.'-'.$monthYear.'-'.$paymentType;
            }           
        }else{
            $text = '0001'.'-'.$monthYear.'-'.$paymentType;
        }
        return $text;
    }


    public function loadPaymentIntershipOfflineById($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        // $select = new Zend_Db_Select($db);
        $query = $db->select()
            ->from("payment_intership_offline", array('payment_inter_off_id'
            ,'payment_inter_off_code'
            ,'payment_inter_off_created_date'
            ,'payment_inter_off_status'
            ,'payment_inter_off_updatedate'
            ,'amount'
            ,'inter_id'))
            ->joinLeft(
            'intership',
            'intership.cus_id = payment_intership_offline.cus_id',
            array('duration'=>'duration'))       
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_intership_offline.category_fee_id',
            array('name'=>'name'))
            ->joinInner(
            'customers',
            'customers.cus_id = payment_intership_offline.cus_id',
            array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname','cus_birthday'=>'cus_birthday'))   
            ->where('payment_intership_offline.payment_inter_off_id = ?',$id)
            ->limit(1);   
                        
        return $db->fetchRow($query);
    }

    
    /*load oayment intership ofiline by cus id*/
    public function loadPaymentIntershipOfflineByCusId($cus_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_intership_offline', array('payment_inter_off_id'
            ,'payment_inter_off_code'
            ,'payment_inter_off_created_date'
            ,'payment_inter_off_status'
            ,'payment_inter_off_updatedate'
            ,'amount'
            ,'payment_type'
            ,'cus_id'))     
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_intership_offline.category_fee_id',
            array('name'=>'name'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_intership_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname'))   
           
            ->where('customers.cus_id = ?',$cus_id)
            ->order('payment_intership_offline.payment_inter_off_created_date desc');
                        
        $row = $db->fetchAll($select);
        return $row;
    }
    

    public function loadPaymentIntershipOffline()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_intership_offline', array('payment_inter_off_id'
            ,'payment_inter_off_code'
            ,'payment_inter_off_created_date'
            ,'payment_inter_off_status'
            ,'payment_inter_off_updatedate'
            ,'amount'
            ,'cus_id'
            ,'payment_type'         
            ))            
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_intership_offline.category_fee_id',
            array('name'=>'name'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_intership_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname'))    
            ->joinInner(
                'intership',
                'intership.cus_id = payment_intership_offline.cus_id',
                array('duration'=>'duration'))            
            ->where('payment_intership_offline.payment_inter_off_status = 0')
            ->order('payment_intership_offline.payment_inter_off_created_date DESC');
            //->where('customers.cus_id = ?',$cus_id);
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    public function loadPaymentIntershipOfflineFilter($start,$length,$type,$startdate,$enddate)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('payment_intership_offline', array('payment_inter_off_id'
            ,'payment_inter_off_code'
            ,'payment_inter_off_created_date'
            ,'payment_inter_off_status'
            ,'payment_inter_off_updatedate'
            ,'amount'
            ,'cus_id'
            ,'payment_type'         
            ))            
            ->joinLeft(
            'category_fee',
            'category_fee.category_fee_id = payment_intership_offline.category_fee_id',
            array('name'=>'name'))
            ->joinLeft(
                'customers',
                'customers.cus_id = payment_intership_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 
                'cus_lastname' =>'cus_lastname',
                'cus_identity_card' => 'cus_identity_card',
                'cus_cellphone'=>'cus_cellphone'))    
            ->joinLeft(
                'intership',
                'intership.cus_id = payment_intership_offline.cus_id',
                array('duration'=>'duration','inter_number_name'=>'inter_number_name'))           
            ->where('payment_intership_offline.payment_inter_off_status = 1');
            //->order('payment_intership_offline.payment_inter_off_created_date DESC');
            //->where('customers.cus_id = ?',$cus_id);

            $startdate = str_replace('/', '-', $startdate);
            $enddate = str_replace('/', '-', $enddate);
            $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
            $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';
    
            $query = $query
                    ->where("payment_intership_offline.payment_inter_off_created_date >= ?",  $start_date_formatted)
                    ->where("payment_intership_offline.payment_inter_off_created_date <= ?",  $end_date_formatted);
        
            if($type != 'all'){
                $query = $query
                ->where('payment_intership_offline.payment_type = ?',$type)
                ->order('payment_intership_offline.payment_inter_off_id desc');
            }            
            
            $row = $db->fetchAll($query);

            if($start == '' && $length == ''){
                return $row;
            }    
            return array_slice($row,$start,$length);                    
      
    }
}