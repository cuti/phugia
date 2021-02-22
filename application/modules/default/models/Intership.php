<?php

class Default_Model_Intership  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'intership';

    protected $_primary = 'inter_id'; 

    protected $_sequence = true;

    public function generationCode($paymentType,$paymentOption){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $currentdate = new Zend_Date();
        $monthYear = $currentdate->toString('MMYYYY');
        $checkquery = $db->select()
        ->from("intership", array('inter_id'))
        ->order('inter_id desc')
        ->limit(1);

        $checkrequest = $db->fetchRow($checkquery);      

        //$text = '0001'.'_'.$monthYear.'_'.'PTV'.'_'.'ONLINE';

        $text = '';
        if(strlen($checkrequest["inter_id"]) > 0){
            $id = $checkrequest["inter_id"] + 1;
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

    //load danh sach no tien phi tap su
    public function loadListLoanPaymentIntership($start,$length,$startdate,$enddate){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
                       ->from('intership',array('inter_id','inter_regis_date',
                       'inter_created_date','lawnum_id','intership_number_id', 'duration',
                       'intership_address','inter_number_name',
                       'payment_inter_status','cus_id','cus_old_id'))
                       ->joinLeft(
                        'customers',
                        'customers.cus_id = intership.cus_id',
                        array('cus_fullname'=>'cus_fullname',
                        'cus_firstname'=>'cus_firstname',
                        'cus_lastname'=>'cus_lastname',
                        'cus_identity_card'=>'cus_identity_card',
                        'cus_cellphone'=>'cus_cellphone',
                        'cus_lawyer_number'=>'cus_lawyer_number',
                        'cus_address_resident'=>'cus_address_resident',
                        'cus_address_resident_now'=>'cus_address_resident_now',                      
                        'cus_birthday'=>'cus_birthday',                      
                        'cus_date_lawyer_number'=>'cus_date_lawyer_number')) 
                        ->joinLeft(
                            'guide_law',
                            'guide_law.inter_id = intership.inter_id',
                        array('law_id'=>'law_id')) 
                        ->joinLeft(
                            'organization_law_details',
                            'organization_law_details.organ_detail_id = intership.organ_detail_id',
                        array('organ_name'=>'organ_name')) 
                        ->joinLeft(
                            'lawyer',
                            'lawyer.cus_id = intership.cus_id',
                        array('cus_id' => 'cus_id'))     
                       ->where('intership.payment_inter_status = ?', 0)
                       ->order('intership.inter_id desc');     

        if($enddate != '' && $startdate != ''){
            $startdate = str_replace('/', '-', $startdate);
            $enddate = str_replace('/', '-', $enddate);
            $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
            $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';
            $query = $query
            ->where("intership.inter_created_date >= ?",  $start_date_formatted)
            ->where("intership.inter_created_date <= ?",  $end_date_formatted);
            // ->limitPage(0, 1);
        }                

        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
        //return $row;
    }
    //function to check course expirse date before creating new intership for customers
    public function checkBeforeCreateIntership($cus_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()->from('intership', array('inter_id','cus_id'))
       ->joinInner(
           'intership_number',
           'intership_number.intership_number_id = intership.intership_number_id',
           array('intership_number_enddate'))   
       ->where('intership.cus_id = ?',$cus_id)
       ->order('intership.inter_created_date desc');
       //->limit(1);
    
        $resultSet = $db->fetchRow($select); 
     
        if(strtotime($resultSet['intership_number_enddate']) + 60 < time()) {
            return null;
        }
        return $resultSet;
    }

    public function listIntership($cus_username)
    {
        $select = $this->select();
        $adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
        return $adapter;

    }

    public function getLastIntership($cus_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $checkquery = $db->select()
        ->from("intership", array('inter_id','inter_number_name'))
        ->where('intership.cus_id = ?',$cus_id)
        ->order('inter_id desc')
        ->limit(1);

        return $db->fetchRow($checkquery); 
    }

    public function loadIntershipByInterId($inter_id){
        $row = $this->fetchRow('inter_id = ' .(int) $inter_id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function loadIntershipByCusId($cus_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
                       ->from('intership',array('inter_id','inter_regis_date',
                       'inter_created_date','lawnum_id','intership_number_id',
                        'duration','intership_address','inter_number_name',
                        'payment_inter_status'))
                    //    ->joinInner(
                    //     'intership_number',
                    //     'intership_number.intership_number_id = intership.intership_number_id',
                    //     array('intership_number_name'=>'intership_number_name')) 
                        ->joinLeft(
                            'guide_law',
                            'guide_law.inter_id = intership.inter_id',
                        array('law_id'=>'law_id')) 
                        ->joinLeft(
                            'organization_law_details',
                            'organization_law_details.organ_detail_id = intership.organ_detail_id',
                        array('organ_name'=>'organ_name')) 
                        ->joinLeft(
                            'lawyer',
                            'lawyer.cus_id = intership.cus_id',
                        array('cus_id' => 'cus_id'))     
                       ->where('intership.cus_id = ?', $cus_id)
                       ->order('intership.inter_id desc');       
                        
        $row = $db->fetchAll($select);
        return $row;
    }    

    public function countIntershipByFilter(){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->from('intership',array('inter_id','inter_regis_date','inter_created_date','cus_id','lawnum_id','intership_number_id',
        'inter_number_name'))
            // ->joinInner(
            // 'intership_number',
            // 'intership_number.intership_number_id = intership.intership_number_id',
            // array('intership_number_name'=>'intership_number_name')) 
            ->joinLeft(
                'guide_law',
                'guide_law.inter_id = intership.inter_id',
            array('law_id'=>'law_id')) 
            ->joinLeft(
             'customers',
             'customers.cus_id = intership.cus_id',
             array('cus_firstname'=>'cus_firstname', 'cus_lastname'=>'cus_lastname'
             ,'cus_cellphone'=>'cus_cellphone',
             'cus_sex'=>'cus_sex' ,'cus_identity_card'=>'cus_identity_card'))
             ->where("customers.cus_type = 0");  
        
        $row = $db->fetchAll($query);
        return $row;

    }

    public function loadIntershipByFilter($search,$intership_number_id,$start,$length,$startdate,$enddate){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->from('intership',array('inter_number_name','inter_id','inter_regis_date','inter_created_date','cus_id','lawnum_id','intership_number_id'))
                    //    ->joinInner(
                    //     'intership_number',
                    //     'intership_number.intership_number_id = intership.intership_number_id',
                    //     array('intership_number_name'=>'intership_number_name')) 
                        ->joinLeft(
                            'guide_law',
                            'guide_law.inter_id = intership.inter_id',
                        array('law_id'=>'law_id')) 
                        ->joinLeft(
                            'customers',
                            'customers.cus_id = intership.cus_id',
                            array('cus_firstname'=>'cus_firstname', 'cus_lastname'=>'cus_lastname'
                            ,'cus_cellphone'=>'cus_cellphone',
                            'cus_sex'=>'cus_sex' ,'cus_identity_card'=>'cus_identity_card',
                            'cus_address_resident' => 'cus_address_resident',
                            'cus_address_resident_now' => 'cus_address_resident_now'));  
                       //->where('cus_id = ?', $cus_id);   
        // if($search != null && $search != ''){
        //     //$query = $query->where('lawyer.law_code = ?',$search);
        //     //->limitPage(0, 1);
        //     $query = $query ->where('cus_cellphone LIKE ?', '%' . $search . '%')
        //     ->orWhere('cus_identity_card LIKE ?', '%' . $search . '%');            
        // }         
        
        if($enddate != '' && $startdate != ''){
            $startdate = str_replace('/', '-', $startdate);
            $enddate = str_replace('/', '-', $enddate);
            $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
            $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';
            $query = $query
            ->where("inter_created_date >= ?",  $start_date_formatted)
            ->where("inter_created_date <= ?",  $end_date_formatted);
            // ->limitPage(0, 1);
        }

        if($intership_number_id != null && $intership_number_id !=''){
            $intership_number_id = trim($intership_number_id);
            $query =  $query->where('inter_number_name LIKE N'."'" . $intership_number_id ."'");  
            //$intership_number_id = (int) $intership_number_id;
            //$query = $query ->where('intership_number.intership_number_id = ?', $intership_number_id);
            
        }   
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
    }

    public function loadIntership()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
                       ->from('intership',array('inter_id',
                       'inter_regis_date','inter_created_date',
                       'cus_id','lawnum_id','intership_number_id','inter_number_name'))
                    //    ->joinInner(
                    //     'intership_number',
                    //     'intership_number.intership_number_id = intership.intership_number_id',
                    //     array('intership_number_name'=>'intership_number_name')) 
                        ->joinLeft(
                            'guide_law',
                            'guide_law.inter_id = intership.inter_id',
                        array('law_id'=>'law_id')) 
                        ->joinLeft(
                            'customers',
                            'customers.cus_id = intership.cus_id',
                            array('cus_firstname'=>'cus_firstname', 'cus_lastname'=>'cus_lastname'
                            ,'cus_cellphone'=>'cus_cellphone',
                            'cus_sex'=>'cus_sex' ,'cus_identity_card'=>'cus_identity_card'));  
                       //->where('cus_id = ?', $cus_id);       
                        
        $row = $db->fetchAll($select);
        return $row;
    }    

}    