<?php

class Default_Model_Lawyer  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'lawyer';

    protected $_primary = 'law_id'; 

    protected $_sequence = true;

    //load danh sach no phi gia nhap
    public function loadListLoanPaymentJoining($start,$length,$startdate,$enddate){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
                       ->from('history_joining',array(
                        'history_joining_id','cus_id',
                       'law_id','law_joining_number','createddate', 
                       'law_joining_note',                       
                       'payment_joining_status'))
                       ->joinLeft(
                        'customers',
                        'customers.cus_id = history_joining.cus_id',
                        array('cus_fullname'=>'cus_fullname',
                        'cus_firstname'=>'cus_firstname',
                        'cus_lastname'=>'cus_lastname',
                        'cus_identity_card'=>'cus_identity_card',
                        'cus_cellphone'=>'cus_cellphone',
                        'cus_address_resident'=>'cus_address_resident',
                        'cus_address_resident_now'=>'cus_address_resident_now',
                        'cus_lawyer_number'=>'cus_lawyer_number',
                        'cus_date_lawyer_number'=>'cus_date_lawyer_number'))                    
                        ->joinLeft(
                            'lawyer',
                            'lawyer.cus_id = history_joining.cus_id',
                        array('cus_id' => 'cus_id'))     
                       ->where('history_joining.payment_joining_status = ?', 0)
                       ->order('customers.cus_lastname asc');      
        
        if($enddate != '' && $startdate != ''){
            $startdate = str_replace('/', '-', $startdate);
            $enddate = str_replace('/', '-', $enddate);
            $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
            $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';
            $query = $query
            ->where("history_joining.createddate >= ?",  $start_date_formatted)
            ->where("history_joining.createddate <= ?",  $end_date_formatted);
            // ->limitPage(0, 1);
        }                
             
                       
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
    }

    public function loadLawyer75YearOldByFilter(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->from('lawyer',array('law_id','law_certfication_no',
        'law_certification_createdate',
        'law_joining_number'))
            ->joinInner(
            'customers',
            'customers.cus_id = lawyer.cus_id',
            array('cus_birthday'=>'cus_birthday','cus_firstname'=>'cus_firstname', 'cus_lastname'=>'cus_lastname'
            ,'cus_cellphone'=>'cus_cellphone',
            'cus_date_lawyer_number'=>'cus_date_lawyer_number',
            'cus_lawyer_number'=>'cus_lawyer_number' ,'cus_identity_card'=>'cus_identity_card',
            'cus_address_resident'=>'cus_address_resident','cus_address_resident_now' => 'cus_address_resident_now'
        ));                         
                      
        $row = $db->fetchAll($query);
        return $row;
    }

    public function loadLawyer15YearCertificationByFilter(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->from('lawyer',array('law_id','law_certfication_no',
        'law_certification_createdate',
        'law_joining_number'))
            ->joinInner(
            'customers',
            'customers.cus_id = lawyer.cus_id',
            array('cus_birthday'=>'cus_birthday','cus_firstname'=>'cus_firstname', 'cus_lastname'=>'cus_lastname'
            ,'cus_cellphone'=>'cus_cellphone','cus_date_lawyer_number'=>'cus_date_lawyer_number',
            'cus_lawyer_number'=>'cus_lawyer_number' ,
            'cus_identity_card'=>'cus_identity_card', 'cus_id' => 'cus_id'));                         
                      
        $row = $db->fetchAll($query);
        return $row;
    }

    public function loadLawyer(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select ->from('lawyer',array('law_id','law_certfication_no',
                       'cus_id','organ_lid','act_id',
                       'lawstatus_id','certification_id','organization_law.organ_name'))
                       ->joinLeft(
                        'customers',
                        'customers.cus_id = lawyer.cus_id',
                        array('cus_firstname'=>'cus_firstname', 
                        'cus_lastname'=>'cus_lastname','cus_cellphone'=>'cus_cellphone','cus_fullname'=>'cus_fullname','cus_identity_card'=>'cus_identity_card')) 
                        ->joinLeft(
                        'organization_law',
                        'organization_law.organ_lid = lawyer.organ_lid',
                        array('organ_name'=>'organ_name')) 
                        ->joinLeft(
                        'activity_law',
                        'activity_law.act_id = lawyer.act_id',
                        array('act_name'=>'act_name'))
                        ->joinLeft(
                            'law_status',
                            'law_status.lawstatus_id = lawyer.lawstatus_id',
                            array('lawstatus_name'=>'lawstatus_name'))                      
                       ->where('customers.cus_member = ?',1)
                       ->order('customers.cus_lastname asc');
    
                        
        $row = $db->fetchAll($select);
        return $row;

    }

    public function loadLawyerByLawStatus($idstatus){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
                       ->from('lawyer',array('law_id','law_certfication_no',
                       'cus_id','organ_lid','act_id','law_type','law_code',
                       'lawstatus_id','certification_id','organization_law.organ_name'))
                       ->joinLeft(
                        'customers',
                        'customers.cus_id = lawyer.cus_id',
                        array('cus_sex'=>'cus_sex' ,'cus_firstname'=>'cus_firstname',
                         'cus_lastname'=>'cus_lastname',
                         'cus_cellphone'=>'cus_cellphone',
                         'cus_identity_card'=>'cus_identity_card',
                         'cus_lawyer_number' => 'cus_lawyer_number')) 
                        ->joinLeft(
                        'organization_law',
                        'organization_law.organ_lid = lawyer.organ_lid',
                        array('organ_name'=>'organ_name')) 
                        ->joinLeft(
                        'activity_law',
                        'activity_law.act_id = lawyer.act_id',
                        array('act_name'=>'act_name'))
                        ->joinLeft(
                            'law_status',
                            'law_status.lawstatus_id = lawyer.lawstatus_id',
                            array('lawstatus_name'=>'lawstatus_name'))
                        // ->joinLeft(
                        //     'certification',
                        //     'certification.certification_id = lawyer.certification_id',
                        //     array('number'=>'cer_number'))
                       //->where('lawyer.cus_id = ?',$cus_id);
                    //    ->group('lawyer.law_id')
                    //    ->group('lawyer.law_certfication_no');
                    ->where('law_status.lawstatus_id = ?',$idstatus);
                        
        $row = $db->fetchAll($select);
        return $row;

    }

    public function loadLawyerStatusNotActive(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select
            ->from('lawyer',array('law_id','law_certfication_no',
            'cus_id','organ_lid','act_id','law_type','law_code',
            'lawstatus_id','certification_id'))
            ->joinInner(
            'customers',
            'customers.cus_id = lawyer.cus_id',
            array('cus_firstname'=>'cus_firstname',
            'cus_lastname'=>'cus_lastname',
            'cus_cellphone'=>'cus_cellphone',
            'cus_sex'=>'cus_sex',
            'cus_identity_card'=>'cus_identity_card'
            ,'cus_birthday'=>'cus_birthday',
            'cus_status' => 'cus_status'
            ,'cus_identity_place'=>'cus_identity_place',
            'cus_lawyer_number' => 'cus_lawyer_number')) 
            ->joinInner(
                'law_status',
                'law_status.lawstatus_id = lawyer.lawstatus_id',
                array('lawstatus_name'=>'lawstatus_name'))
            ->where('law_status.lawstatus_id != ?', 1);
        $row = $db->fetchAll($query);
        return $row;

    }

    public function loadLawyerActiveByFilter($search,$start,$length,$startdate,$enddate,$idstatus){
             
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select
                       ->from('lawyer',array('law_id','law_certfication_no','law_certification_createdate',
                       'cus_id','organ_lid','act_id','law_type','law_code',
                       'lawstatus_id','certification_id','organization_law.organ_name'))
                       ->joinLeft(
                        'customers',
                        'customers.cus_id = lawyer.cus_id',
                        array('cus_firstname'=>'cus_firstname',
                        'cus_lastname'=>'cus_lastname',
                        'cus_cellphone'=>'cus_cellphone',
                        'cus_sex'=>'cus_sex',
                        'cus_identity_card'=>'cus_identity_card',
                        'cus_lawyer_number' => 'cus_lawyer_number',
                        'cus_date_lawyer_number' => 'cus_date_lawyer_number',
                        'cus_address_resident' => 'cus_address_resident',
                        'cus_address_resident_now' => 'cus_address_resident_now'
                        )) 
                        ->joinLeft(
                        'organization_law',
                        'organization_law.organ_lid = lawyer.organ_lid',
                        array('organ_name'=>'organ_name')) 
                        ->joinLeft(
                        'activity_law',
                        'activity_law.act_id = lawyer.act_id',
                        array('act_name'=>'act_name'))
                        ->joinLeft(
                            'law_status',
                            'law_status.lawstatus_id = lawyer.lawstatus_id',
                            array('lawstatus_name'=>'lawstatus_name'));
      
        if($search != ''){
            $query = $query->where('customers.cus_lawyer_number = ?',$search);
            //->limitPage(0, 1);
        }      

        if($enddate != '' && $startdate != ''){
            $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
            $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';
            $query = $query
            ->where("createddate >= ?",  $start_date_formatted)
            ->where("createddate <= ?",  $end_date_formatted);
            // ->limitPage(0, 1);
        }

        if($search == '' && $enddate == '' && $startdate == ''){
             $query = $query->order('createddate DESC');
             //$query = $query->order('createddate DESC')->limitPage(1,1);
        }
        $query = $query->where('law_status.lawstatus_id = ?',$idstatus)
        ->where('customers.cus_lawyer_number is not null'); 
                  
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
        //return $row;

    }


    public function countLawyerActiveByFilter($idstatus){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select
                       ->from('lawyer',array('law_id','law_certfication_no',
                       'cus_id','organ_lid','act_id','law_type','law_code',
                       'lawstatus_id','certification_id','organization_law.organ_name'))
                       ->joinLeft(
                        'customers',
                        'customers.cus_id = lawyer.cus_id',
                        array('cus_firstname'=>'cus_firstname',
                        'cus_lastname'=>'cus_lastname',
                        'cus_cellphone'=>'cus_cellphone',
                        'cus_sex'=>'cus_sex',
                        'cus_identity_card'=>'cus_identity_card',
                        'cus_lawyer_number' => 'cus_lawyer_number'
                        )) 
                        ->joinLeft(
                        'organization_law',
                        'organization_law.organ_lid = lawyer.organ_lid',
                        array('organ_name'=>'organ_name')) 
                        ->joinLeft(
                        'activity_law',
                        'activity_law.act_id = lawyer.act_id',
                        array('act_name'=>'act_name'))
                        ->joinLeft(
                            'law_status',
                            'law_status.lawstatus_id = lawyer.lawstatus_id',
                            array('lawstatus_name'=>'lawstatus_name'));      
        $query = $query->where('law_status.lawstatus_id = ?',$idstatus)
        ->where('customers.cus_lawyer_number is not null'); 
        $row = $db->fetchAll($query);
        return $row;

    }

    
    public function loadLawyerActive(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
                       ->from('lawyer',array('law_id','law_certfication_no',
                       'cus_id','organ_lid','act_id','law_type','law_code',
                       'lawstatus_id','certification_id','organization_law.organ_name'))
                       ->joinLeft(
                        'customers',
                        'customers.cus_id = lawyer.cus_id',
                        array('cus_firstname'=>'cus_firstname',
                        'cus_lastname'=>'cus_lastname',
                        'cus_cellphone'=>'cus_cellphone',
                        'cus_sex'=>'cus_sex',
                        'cus_identity_card'=>'cus_identity_card'
                        ,'cus_lawyer_number' => 'cus_lawyer_number')) 
                        ->joinLeft(
                        'organization_law',
                        'organization_law.organ_lid = lawyer.organ_lid',
                        array('organ_name'=>'organ_name')) 
                        ->joinLeft(
                        'activity_law',
                        'activity_law.act_id = lawyer.act_id',
                        array('act_name'=>'act_name'))
                        ->joinLeft(
                            'law_status',
                            'law_status.lawstatus_id = lawyer.lawstatus_id',
                            array('lawstatus_name'=>'lawstatus_name'));
                        // ->joinLeft(
                        //     'certification',
                        //     'certification.certification_id = lawyer.certification_id',
                        //     array('number'=>'cer_number'))
                       //->where('lawyer.cus_id = ?',$cus_id);
                    //    ->group('lawyer.law_id')
                    //    ->group('lawyer.law_certfication_no');
                        
        $row = $db->fetchAll($select);
        return $row;

    }

    public function loadLawyerByLawId($law_id){
        $row = $this->fetchRow('law_id = ' .(int) $law_id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function validateLawCode($code){
        $row = $this->fetchRow('law_code = ' .(int) $code);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function loadLawyerByCusId($cus_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
                       ->from('lawyer',array('law_joining_number',
                       'law_organ_old','law_type','law_id','law_certfication_no',               
                       'law_certification_createdate',
                       'cus_id','organ_lid','act_id',
                       'law_code','law_code_createdate',
                       'lawstatus_id','certification_id','organization_law_details.organ_name'))
                       ->joinInner(
                        'customers',
                        'customers.cus_id = lawyer.cus_id',
                        array('cus_lawyer_number' => 'cus_lawyer_number',
                        'cus_date_lawyer_number' => 'cus_date_lawyer_number','cus_member'))
                        ->joinLeft(
                            'city',
                            'city.city_id = customers.cus_lawyer_cityid',
                            array('city_name'=>'city_name')) 
                        ->joinLeft(
                        'organization_law_details',
                        'organization_law_details.organ_detail_id = lawyer.organ_lid',
                        array('organ_name'=>'organ_name')) 
                        ->joinLeft(
                        'activity_law',
                        'activity_law.act_id = lawyer.act_id',
                        array('act_name'=>'act_name'))
                        ->joinLeft(
                            'law_status',
                            'law_status.lawstatus_id = lawyer.lawstatus_id',
                            array('lawstatus_name'=>'lawstatus_name'))
                        // ->joinLeft(
                        //     'certification',
                        //     'certification.certification_id = lawyer.certification_id',
                        //     array('number'=>'cer_number'))
                       ->where('lawyer.cus_id = ?',$cus_id);
                    //    ->group('lawyer.law_id')
                    //    ->group('lawyer.law_certfication_no');
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    // load danh sach luat su no phi thanh vien

    public function loadLawyerDebt($start,$length){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);       
        $dateAfter18months = date("Y-m-d",strtotime('-19 months'));     

        $first_date_find = strtotime(date("Y-m-d", strtotime($dateAfter18months)) . ", first day of this month");
        $first_date = date("Y-m-d",$first_date_find).' 00:00:00';
        
        $query1 = $db->select()
        ->from("payment_lawyer_offline",array('cus_id' => 'cus_id'))
        ->where('payment_lawyer_off_status = ?',1)       
        ->where('endmonthdate >= ?','2016-01-01 00:00:00')
        ->group('cus_id')
        ->having('MAX(endmonthdate) <= ?',$first_date);


        $row1 = $db->fetchAll($query1);
        $ids = array();
        if($row1 != null && sizeof($row1) > 0){
            foreach($row1 as $r){ 
                array_push($ids,[$r['cus_id']]);     
            }
        }
       
        $query = $select->distinct()
                       ->from('lawyer',array(
                       'law_joining_number',
                       'law_organ_old',
                       'law_type',
                       'law_id',
                       'law_certfication_no',
                       'law_certification_createdate',
                       'cus_id'
                    //    'organ_lid',
                    //    'act_id',
                    //    'law_code',
                    //    'law_code_createdate',
                       //'lawstatus_id',
                       //'certification_id',
                       //'organization_law.organ_name',
                       //'debt',
                      ))
                       ->joinLeft(
                        'customers',
                        'customers.cus_id = lawyer.cus_id',
                        array('cus_lastname'=>'cus_lastname',
                        'cus_firstname' => 'cus_firstname','cus_lawyer_number' => 'cus_lawyer_number'
                        )) 
                        // ->joinLeft(
                        // 'organization_law',
                        // 'organization_law.organ_lid = lawyer.organ_lid',
                        // array('organ_name'=>'organ_name')) 
                        // ->joinLeft(
                        // 'activity_law',
                        // 'activity_law.act_id = lawyer.act_id',
                        // array('act_name'=>'act_name'))
                        ->joinLeft(
                            'law_status',
                            'law_status.lawstatus_id = lawyer.lawstatus_id',
                            array('lawstatus_name'=>'lawstatus_name'))
                        // ->joinLeft(
                        //     'certification',
                        //     'certification.certification_id = lawyer.certification_id',
                        //     array('number'=>'cer_number'))
                       //->where('lawyer.debt > 0 ')
                       //->where('lawyer.month >= 18')
                       ->where('lawyer.lawstatus_id = 1');

        if($ids != null && sizeof($ids) > 0){
            $query = $query->where('lawyer.cus_id IN (?) ',$ids);
        }               
                        
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }  
        return array_slice($row,$start,$length);
    }

}