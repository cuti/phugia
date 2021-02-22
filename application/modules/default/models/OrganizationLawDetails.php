<?php

class Default_Model_OrganizationLawDetails  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'organization_law_details';

    protected $_primary = 'organ_detail_id'; 

    protected $_sequence = true;

    public function validateEmail($data){
    
        $checkquery = $this->select()
        ->from($this, array("num"=>"COUNT(*)"))
        ->where("organ_email = ?", $data);
       

        $checkrequest = $this->fetchRow($checkquery);
        return $checkrequest["num"];

    }

    public function loadOrganzationsLaw()
    {
        $select = $this->select()
                       ->from($this,array('organ_detail_id','organ_name'));        
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    public function loadOrganzationsLawById($id)
    {
        $select = $this->select()
        ->from($this,array(
            'organ_detail_id',
        'organ_name'
        ,'organ_mobile'
        ,'total_law_native'
        ,'total_law_foreign'
        ,'total_job_done'
        ,'total_law'
        ,'total_procedure'
        ,'total_criminal'
        ,'total_support_service'
        ,'total_support'
        ,'amount'
        ,'amount_charge'
        ,'law_id'
        ,'organ_email'
        ,'law_organ_address'
        ,'district'
        ,'customers'
        ,'organ_type'
        ,'organ_fax'
        ,'organ_certification'
        ,'law_organ_address_hktt'
        ,'createddate'
        ,'createdby'
        ,'organ_note'
        ,'thongtin_thaydoi'
        ,'ngaycapnhat'
        ))
        ->where("organ_detail_id = ?", $id);      
                        
        $row = $this->fetchRow($select);
        return $row;
    }

    public function loadOrganzations()
    {
        $select = $this->select()
        ->from($this,array(
        'organ_detail_id',
        'organ_name'
        ,'organ_mobile'
        ,'total_law_native'
        ,'total_law_foreign'
        ,'total_job_done'
        ,'total_law'
        ,'total_procedure'
        ,'total_criminal'
        ,'total_support_service'
        ,'total_support'
        ,'amount'
        ,'amount_charge'
        ));           
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    //load information of organization details
    public function loadOrganzationDetails()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select
            ->from('organization_law_details',array(
            'organ_detail_id'
            ,'organ_name'
            ,'organ_mobile'
            ,'total_law_native'
            ,'total_law_foreign'
            ,'total_job_done'
            ,'total_law'
            ,'total_procedure'
            ,'total_criminal'
            ,'total_support_service'
            ,'total_support'
            ,'amount'
            ,'amount_charge'
            ,'law_id'
            ,'organ_email'
            ,'law_organ_address'
            ,'district'
            ,'customers'
            ,'organ_type'
            ,'organ_fax'
            ,'organ_certification'
            ,'law_organ_address_hktt'
            ,'createddate'
            ,'createdby'
            ,'organ_note'
            ,'organ_certification_date'
            ,'thongtin_thaydoi'
            ,'ngaycapnhat'
            ,'chidinh'
            ))
            ->joinLeft(
            'lawyer',
            'lawyer.law_id = organization_law_details.law_id',
            array('law_code'
            ,'law_code_createdate'
            ,'law_certfication_no'
            ,'law_certification_createdate'))
            ->joinLeft('customers',
                'customers.cus_id = lawyer.cus_id',
            array('cus_firstname'=>'cus_firstname',
            'cus_lastname'=>'cus_lastname'
            ,'cus_birthday' => 'cus_birthday'
            ,'cus_cellphone' => 'cus_cellphone',
            'cus_lawyer_number' => 'cus_lawyer_number' ));                        
        $row = $db->fetchAll($query);
        return $row;            
    }

}