<?php

class Default_Model_PaymentOffline  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'payment_offline';

    protected $_primary = 'payment_id'; 

    protected $_sequence = true;

    /**count monney in year */

    public function countMooneyInYear($year){

        $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';

        $checkquery = $this->select()
        ->from($this, array("SUM(amount) as total"))
        ->where("payment_off_status = ?", '1')
        ->where("payment_off_created_date >= ?",  $start_date_formatted)
        ->where("payment_off_created_date <= ?",  $end_date_formatted);                
        

        $checkrequest = $this->fetchRow($checkquery);

        return $checkrequest["total"];

    }

    
    public function generationCode($paymentType,$paymentOption){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $currentdate = new Zend_Date();
        $monthYear = $currentdate->toString('MMYYYY');
        $checkquery = $db->select()
        ->from("payment_offline", array('payment_id'))
        ->order('payment_id desc')
        ->limit(1);

        $checkrequest = $db->fetchRow($checkquery);      

        $text = '';
        $length = strlen($checkrequest["payment_id"]);
        if($length > 0){
            $id = $checkrequest["payment_id"] + 1;
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

    public function loadPaymentOffline()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_offline', array(
            'payment_id',
            'payment_off_code'
            ,'payment_off_created_date'
            ,'payment_off_status'
            ,'payment_off_updatedate'
            ,'amount'
            ,'reason'
            ,'type'
            ,'community'
            ,'law_id'
            ,'payment_type'))
            ->joinLeft(
            'lawyer',
            'lawyer.law_id = payment_offline.law_id',
            array())
            ->joinLeft(
                'customers',
                'customers.cus_id = lawyer.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname'))    
            ->where('payment_offline.payment_off_status = 0')
            ->order('payment_offline.payment_off_created_date DESC');
                        
        $row = $db->fetchAll($select);
        return $row;
    }


    public function loadPaymentOfflineFilter($start,$length,$startdate,$enddate)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('payment_offline', array(
            'payment_id',
            'payment_off_code'
            ,'payment_off_created_date'
            ,'payment_off_status'
            ,'payment_off_updatedate'
            ,'amount'
            ,'reason'
            ,'type'
            ,'community'
            ,'law_id'
            ,'payment_type'))
            ->joinLeft(
            'lawyer',
            'lawyer.law_id = payment_offline.law_id',
            array())
            ->joinLeft(
                'customers',
                'customers.cus_id = lawyer.cus_id',
                array('cus_firstname'=>'cus_firstname',
                 'cus_lastname' =>'cus_lastname',
                 'cus_lawyer_number' =>'cus_lawyer_number',
                 'cus_date_lawyer_number' =>'cus_date_lawyer_number',
                 'cus_address_resident' =>'cus_address_resident',
                 'cus_address_resident_now' =>'cus_address_resident_now'))    
            ->where('payment_offline.payment_off_status = 0')
            ->order('payment_offline.payment_off_created_date DESC');

            $startdate = str_replace('/', '-', $startdate);
            $enddate = str_replace('/', '-', $enddate);
            $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
            $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';
    
            $query = $query
            ->where("payment_offline.payment_off_created_date >= ?",  $start_date_formatted)
            ->where("payment_offline.payment_off_created_date <= ?",  $end_date_formatted);                
        
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){                
            return $row;
        }
        return array_slice($row,$start,$length);
    }

    //load by id
    public function loadPaymentOfflineById($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_offline', array(
            'payment_id',
            'payment_off_code'
            ,'payment_off_created_date'
            ,'payment_off_status'
            ,'payment_off_updatedate'           
            ,'amount'
            ,'reason'
            ,'type'
            ,'community'
            ,'law_id'))
            ->joinLeft(
            'lawyer',
            'lawyer.law_id = payment_offline.law_id',
            array())
            ->joinLeft(
                'customers',
                'customers.cus_id = lawyer.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname'))    
            //->where('payment_offline.payment_off_status = 0')
            ->where('payment_offline.payment_id = ?',$id)
            ->order('payment_offline.payment_off_created_date DESC');
                        
        $row = $db->fetchRow($select);
        return $row;
    }
}