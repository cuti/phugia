<?php

class Default_Model_CustomersRewardDiscipline  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'customers_reward_discipline';

    protected $_primary = 'id'; 

    protected $_sequence = true;


     /*load oayment joining ofiline by cus id*/
     public function loadCustomersRewardByCusId($cus_id)
     {
         $db = Zend_Db_Table::getDefaultAdapter();
         $select = new Zend_Db_Select($db);
         $select->distinct()
             ->from('customers_reward_discipline', array('id'
             ,'cus_id'
             ,'reward_date'
             ,'reward_reason'
             ,'discipline_date'
             ,'discipline_reason',
             'createdate',
             'type'))
             ->joinInner(
                 'customers',
                 'customers.cus_id = customers_reward_discipline.cus_id',
                 array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname'))   
            
             ->where('customers.cus_id = ?',$cus_id)
             ->order('customers_reward_discipline.createdate');
                         
         $row = $db->fetchAll($select);
         return $row;
     }
 
     public function loadCustomersRewardById($id){        
        $row = $this->fetchRow('id = ' .(int) $id);
        if (!$row) {
            return null;
        }
        return $row->toArray();       
     }

     public function loadCustomersRewardWithDataById($id){        
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('customers_reward_discipline', array('id'
            ,'cus_id'
            ,'reward_date'
            ,'reward_reason'
            ,'discipline_date'
            ,'discipline_reason',
            'createdate',
            'type',
            'people_problem',
            'cus_reward_document',
            'cus_reward_discipline_document',
            'cus_reward_discipline_month',
            'cus_reward_discipline_type',
            'year',
            'law_help'))
            ->joinInner(
                'customers',
                'customers.cus_id = customers_reward_discipline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
                'cus_lawyer_number' => 'cus_lawyer_number','cus_date_lawyer_number' => 'cus_date_lawyer_number'))
           ->joinInner(
           'lawyer',
           'customers.cus_id = lawyer.cus_id',
           array('law_code'=>'law_code',
            'law_id' =>'law_id',
            'lawstatus_id' => 'lawstatus_id',
            'law_certfication_no' => 'law_certfication_no',
            'law_certification_createdate' => 'law_certification_createdate'
            ))   
           
            ->where('customers_reward_discipline.id = ?',$id);
           //  ->order('customers_reward_discipline.createdate');

            // $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
            // $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';
            // $query = $query
            // ->where("customers_reward_discipline.createdate >= ?",  $start_date_formatted)
            // ->where("customers_reward_discipline.createdate <= ?",  $end_date_formatted)
            //->order('customers_reward_discipline.id desc');            
        
        
            $row = $db->fetchRow($query);            
            return $row;    
     }




     public function loadCustomersRewardDiscipline(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('customers_reward_discipline', array('id'
            ,'cus_id'
            ,'reward_date'
            ,'reward_reason'
            ,'discipline_date'
            ,'discipline_reason',
            'createdate',
            'type',
            'people_problem',
            'cus_reward_document',
            'cus_reward_discipline_document',
            'cus_reward_discipline_month',
            'cus_reward_discipline_type',
            'year',
            'law_help'))
            ->joinInner(
                'customers',
                'customers.cus_id = customers_reward_discipline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
                'cus_lawyer_number' => 'cus_lawyer_number','cus_date_lawyer_number' => 'cus_date_lawyer_number'))
           ->joinInner(
           'lawyer',
           'customers.cus_id = lawyer.cus_id',
           array('law_code'=>'law_code',
            'law_id' =>'law_id',
            'lawstatus_id' => 'lawstatus_id',
            'law_certfication_no' => 'law_certfication_no',
            'law_certification_createdate' => 'law_certification_createdate'
            ))   
           
           //  ->where('customers.cus_id = ?',$cus_id)
           //  ->order('customers_reward_discipline.createdate');

            // $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
            // $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';
            // $query = $query
            // ->where("customers_reward_discipline.createdate >= ?",  $start_date_formatted)
            // ->where("customers_reward_discipline.createdate <= ?",  $end_date_formatted)
            ->order('customers_reward_discipline.id desc');            
        
        
            $row = $db->fetchAll($query);
            //if($start == '' && $length == ''){
                return $row;
            //}    
            //return array_slice($row,$start,$length);
     } 

     public function loadCustomersRewardByFilter($start,$length,$year)
     {
         $db = Zend_Db_Table::getDefaultAdapter();
         $select = new Zend_Db_Select($db);
         $query = $select->distinct()
             ->from('customers_reward_discipline', array('id'
             ,'cus_id'
             ,'reward_date'
             ,'reward_reason'
             ,'discipline_date'
             ,'discipline_reason',
             'createdate',
             'type',
             'people_problem',
             'law_help'))
             ->joinInner(
                 'customers',
                 'customers.cus_id = customers_reward_discipline.cus_id',
                 array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
                 'cus_lawyer_number' => 'cus_lawyer_number'))
            ->joinInner(
            'lawyer',
            'customers.cus_id = lawyer.cus_id',
            array('law_code'=>'law_code',
             'law_id' =>'law_id',
             'lawstatus_id' => 'lawstatus_id'))              
            ->where('customers.cus_status != ?',7);
            //  ->order('customers_reward_discipline.createdate');

             $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
             $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';
             $query = $query
             ->where("customers_reward_discipline.discipline_date >= ?",  $start_date_formatted)
             ->where("customers_reward_discipline.discipline_date <= ?",  $end_date_formatted)
             ->order('customers_reward_discipline.discipline_date');            
         
         
             $row = $db->fetchAll($query);
             if($start == '' && $length == ''){
                 return $row;
             }    
             return array_slice($row,$start,$length);
     }

    
}