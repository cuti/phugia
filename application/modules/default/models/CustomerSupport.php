<?php

class Default_Model_CustomerSupport  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'customer_support';

    protected $_primary = 'support_id'; 

    protected $_sequence = true;

    public function loadCustomerSupportByCusId($cusid)
    {
        $select = $this->select()
                       ->from($this,array('support_id'
                       ,'cus_id'
                       ,'hours'
                       ,'year'
                       ,'reason'
                       ,'createddate'
                       ,'modifieddate'))
                       ->where('cus_id = ?',$cusid);
                        
        $row = $this->fetchAll($select);

        return $row;
    }

    public function loadCustomerSupportByFilter($start, $length,$startdate,$enddate){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('customer_support', array(
            'support_id'
            ,'cus_id'
            ,'hours'
            ,'year'
            ,'reason'           
            ,'createddate'
            ,'modifieddate'
            ))
            ->joinInner(
                'customers',
                'customers.cus_id = customer_support.cus_id',
                array('cus_firstname'=>'cus_firstname', 
                'cus_lastname' =>'cus_lastname',
                'cus_birthday'=>'cus_birthday',
                'cus_identity_card'=>'cus_identity_card',
                'cus_lawyer_number' => 'cus_lawyer_number',
                'cus_date_lawyer_number' => 'cus_date_lawyer_number',
                'cus_address_resident' => 'cus_address_resident',
                'cus_address_resident_now' => 'cus_address_resident_now'))    
            ->joinLeft(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code',
                'law_certfication_no'=>'law_certfication_no'));            
            

            $startdate = str_replace('/', '-', $startdate);
            $enddate = str_replace('/', '-', $enddate);
            $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
            $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';

            $query = $query
            ->where("customer_support.createddate >= ?",  $start_date_formatted)
            ->where("customer_support.createddate <= ?",  $end_date_formatted);


            $row = $db->fetchAll($query);

            if($start == '' && $length == ''){
                return $row;
            }    
            return array_slice($row,$start,$length);
    }

    public function loadCustomerSupportWithData($support_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('customer_support', array(
            'support_id'
            ,'cus_id'
            ,'hours'
            ,'year'
            ,'reason'           
            ,'createddate'
            ,'modifieddate'
            ))
            ->joinInner(
                'customers',
                'customers.cus_id = customer_support.cus_id',
                array('cus_firstname'=>'cus_firstname', 
                'cus_lastname' =>'cus_lastname','cus_birthday'=>'cus_birthday',
                'cus_lawyer_number' => 'cus_lawyer_number'))    
            ->joinLeft(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code','law_certfication_no'=>'law_certfication_no'))
            ->where('customer_support.support_id = ?',$support_id);            
            
            $row = $db->fetchRow($query);
            return $row;
                       
    }

    public function loadCustomerSupports(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('customer_support', array(
            'support_id'
            ,'cus_id'
            ,'hours'
            ,'year'
            ,'reason'           
            ,'createddate'
            ,'modifieddate'
            ))
            ->joinInner(
                'customers',
                'customers.cus_id = customer_support.cus_id',
                array('cus_firstname'=>'cus_firstname', 
                'cus_lastname' =>'cus_lastname','cus_birthday'=>'cus_birthday',
                'cus_lawyer_number' => 'cus_lawyer_number'))    
            ->joinLeft(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code','law_certfication_no'=>'law_certfication_no',
                'law_certification_createdate'=>'law_certification_createdate'))
            ->order('customer_support.support_id desc');           
            

            // $startdate = str_replace('/', '-', $startdate);
            // $enddate = str_replace('/', '-', $enddate);
            // $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
            // $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';

            // $query = $query
            // ->where("customer_support.createddate >= ?",  $start_date_formatted)
            // ->where("customer_support.createddate <= ?",  $end_date_formatted);


            $row = $db->fetchAll($query);

            //if($start == '' && $length == ''){
                return $row;
            //}    
            //return array_slice($row,$start,$length);
    }
}