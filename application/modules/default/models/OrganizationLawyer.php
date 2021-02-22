<?php

class Default_Model_OrganizationLawyer  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'organization_lawyer';

    protected $_primary = 'organ_lawyer_id'; 

    protected $_sequence = true;

    public function checkLawyerExistInOrgan($law_id,$organ_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select
                       ->from('organization_lawyer',array(
                            'organ_lawyer_id'                           
                       ))
                       ->where('organization_lawyer.law_id = ?',$law_id)
                       ->where('organization_lawyer.organ_law_id = ?',$organ_id);
        return $db->fetchAll($query);               
    }

    public function loadOrganizationLawyerByFilter($start,$length,$organ_id){
             
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select
                       ->from('organization_lawyer',array(
                            'organ_lawyer_id'
                            ,'organ_law_id'                         
                            ,'law_id'
                            ,'note'
                            ,'law_joining_organ_date'
                       ))
                       ->joinInner(
                        'organization_law_details',
                        'organization_law_details.organ_detail_id = organization_lawyer.organ_law_id',
                        array('organ_name'=>'organ_name'))
                       ->joinInner(
                        'lawyer',
                        'lawyer.law_id = organization_lawyer.law_id',
                        array('law_code'
                        ,'law_code_createdate'
                        ,'law_certfication_no'
                        ,'law_certification_createdate'))
                       ->joinInner(
                            'customers',
                            'customers.cus_id = lawyer.cus_id',
                            array('cus_firstname'=>'cus_firstname',
                            'cus_identity_card'=>'cus_identity_card',
                        'cus_lastname'=>'cus_lastname'
                    ,'cus_birthday' => 'cus_birthday'
                ,'cus_lawyer_number' => 'cus_lawyer_number'
                ,'cus_address_resident' => 'cus_address_resident',
                'cus_address_resident_now' => 'cus_address_resident_now'
                ,'cus_date_lawyer_number' => 'cus_date_lawyer_number' )); 
    ; 

                        $query = $query->where('organization_law_details.organ_detail_id = ?',$organ_id); 
                  
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
 
    }


    public function countLawyerActiveByFilter($start,$length,$organ_id){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select
                       ->from('organization_lawyer',array(
                            'organ_lawyer_id'
                            ,'law_name'
                            ,'law_birthday'
                            ,'law_nation'
                            ,'organ_law_id'
                            ,'law_certification'
                            ,'law_certification_date'
                            ,'law_joining_organ_date'
                       ))
                       ->joinInner(
                        'organization_law_details',
                        'organization_law_details.organ_detail_id = organization_lawyer.organ_law_id',
                        array('organ_name'=>'organ_name')); 

                        $query = $query->where('organization_law_details.organ_detail_id = ?',$organ_id); 
                  
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);

    }


}