<?php

class Default_Model_OrganizationLaw  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'organization_law';

    protected $_primary = 'organ_lid'; 

    protected $_sequence = true;

    public function loadOrganzationLaw()
    {
        $select = $this->select()
                       ->from($this,array('organ_lid','organ_name'));        
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    // load danh sach hanh nghe boi ls dung ten hoac to chuc
    public function loadOrganzationLawByFilter($start,$length,$search, $type)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select
                       ->from('organization_law_details',array(
                        'organ_detail_id'
                        ,'organ_name'
                        ,'law_organ_address'
                        ,'organ_mobile'
                        ,'organ_fax'
                        ,'organ_email'
                        //,'organ_website'
                        ,'createddate'
                        ,'organ_certification'
                        ,'law_organ_address_hktt'
                        ,'organ_type'
                        ,'district'
                        ,'law_id'
                        ,'customers'
                        ,'organ_certification_date'
                        ,'thongtin_thaydoi'
                        ,'ngaycapnhat'
                        ,'organ_note'))
                        ->joinInner(
                            'lawyer',
                            'lawyer.law_id = organization_law_details.law_id',
                            array('law_code'=>'law_code',
                            'lawstatus_id'=>'lawstatus_id',
                            'law_certfication_no' => 'law_certfication_no')) 
                       ->joinLeft(
                        'customers',
                        'lawyer.cus_id = customers.cus_id',
                        array('cus_firstname'=>'cus_firstname',
                        'cus_lastname'=>'cus_lastname',
                        'cus_fullname'=>'cus_fullname',
                        'cus_cellphone'=>'cus_cellphone',
                        'cus_sex'=>'cus_sex',
                        'cus_identity_card'=>'cus_identity_card',
                        'cus_lawyer_number' => 'cus_lawyer_number'))                       
                        ->joinLeft(
                        'activity_law',
                        'activity_law.act_id = lawyer.act_id',
                        array('act_name'=>'act_name'))
                        ->joinLeft(
                            'law_status',
                            'law_status.lawstatus_id = lawyer.lawstatus_id',
                            array('lawstatus_name'=>'lawstatus_name'))
                        ->where('lawyer.lawstatus_id != 7' );    

        //                ->where('phone_number LIKE ?', '%' . $q . '%');
        if($search != '' && $type == 'all'){
            $query = $query->where('organization_law_details.organ_name LIKE ?','%'.$search.'%')
            ->where('customers.cus_lawyer_number LIKE ?','%'.$search.'%');
        }else if($search != '' && $type == 'tentchn'){
            $query = $query->where('organization_law_details.organ_name LIKE N'."'" . $search .'%'."'");            
        }else if($search != '' && $type == 'mals'){
            $query = $query->where('customers.cus_lawyer_number LIKE ?','%'.$search.'%');    
        }
                  
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
    }

    // load danh sach hanh nghe boi ls dung ten hoac to chuc
    public function loadOrganzationLawByFilterTotals($search, $type)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        $query = $select
                       ->from('organization_law_details',array(
                        'organ_detail_id'
                        ,'organ_name'
                        ,'law_organ_address'
                        ,'organ_mobile'
                        ,'organ_fax'
                        ,'organ_email'
                        //,'organ_website'
                        ,'createddate'
                        ,'organ_certification'
                        ,'law_organ_address_hktt'
                        ,'organ_type'
                        ,'district'
                        ,'law_id'
                        ,'customers'
                        //,'organ_address_1'
                        //,'organ_document'
                        ,'organ_note'))
                        ->joinInner(
                            'lawyer',
                            'lawyer.law_id = organization_law_details.law_id',
                            array('law_code'=>'law_code',
                            'lawstatus_id'=>'lawstatus_id')) 
                       ->joinLeft(
                        'customers',
                        'lawyer.cus_id = customers.cus_id',
                        array('cus_firstname'=>'cus_firstname',
                        'cus_lastname'=>'cus_lastname',
                        'cus_cellphone'=>'cus_cellphone',
                        'cus_sex'=>'cus_sex',
                        'cus_identity_card'=>'cus_identity_card',
                        'cus_lawyer_number' => 'cus_lawyer_number'))                       
                        ->joinLeft(
                        'activity_law',
                        'activity_law.act_id = lawyer.act_id',
                        array('act_name'=>'act_name'))
                        ->joinLeft(
                            'law_status',
                            'law_status.lawstatus_id = lawyer.lawstatus_id',
                            array('lawstatus_name'=>'lawstatus_name'))
                        ->where('lawyer.lawstatus_id != 7' );    

        //                ->where('phone_number LIKE ?', '%' . $q . '%');
        if($search != '' && $type == 'all'){
            $query = $query->where('organization_law_details.organ_name LIKE ?','%'.$search.'%')
            ->where('customers.cus_lawyer_number LIKE ?','%'.$search.'%');
        }else if($search != '' && $type == 'tentchn'){
            $query = $query->where('organization_law_details.organ_name LIKE ?','%'.$search.'%');
        }else if($search != '' && $type == 'mals'){
            $query = $query->where('customers.cus_lawyer_number LIKE ?','%'.$search.'%');    
        }
                  
        $row = $db->fetchAll($query);
        return count($row);
       
    }

}