<?php

class Default_Model_PaymentTrainingOffline  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'payment_training_offline';

    protected $_primary = 'payment_training_off_id'; 

    protected $_sequence = true;

    public function generationCode($paymentType,$paymentOption){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $currentdate = new Zend_Date();
        $monthYear = $currentdate->toString('MMYYYY');
        $checkquery = $db->select()
        ->from("payment_training_offline", array('payment_training_off_id'))
        ->order('payment_training_off_id desc')
        ->limit(1);

        $checkrequest = $db->fetchRow($checkquery);      

        $text = '';
        $length = strlen($checkrequest["payment_training_off_id"]);
        if($length > 0){
            $id = $checkrequest["payment_training_off_id"] + 1;
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

    public function loadPaymentLawyerOffline()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_training_offline', array(
            'payment_training_off_id'
            ,'payment_training_off_code'
            ,'payment_training_off_created_date'
            ,'payment_training_off_status'
            ,'payment_training_off_updatedate'
            ,'amount'
            ,'payment_type'
            ))               
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_training_offline.category_fee_id',
            array('name'=>'name'))
            ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training_offline.category_training_id',
                array('cat_train_name'=>'cat_train_name','cat_train_number'=>'cat_train_number'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_training_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname'))    
            ->where('payment_training_offline.payment_training_off_status = ?','1')
            ->order('payment_training_offline.payment_training_off_id DESC');
            
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    //find paymenttraining offline by custommer id and cattraining id
    public function loadTrainingPaymentOfflineByCusIdAndCatTrainingId($cus_id,$cat_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_training_offline', array(
                'payment_training_off_id'
            ,'payment_training_off_code'
            ,'payment_training_off_created_date'
            ,'payment_training_off_status'
            ,'payment_training_off_updatedate'
            ,'amount'
            ,'training_certification_number'
            ,'category_training_id'))                
            ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training_offline.category_training_id',
                array('cat_train_name'=>'cat_train_name',
                'cat_train_number'=>'cat_train_number'
                ,'cat_train_fromdate' => 'cat_train_fromdate'
                ,'cat_train_address' => 'cat_train_address',
                'cat_trainer'=>'cat_trainer'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_training_offline.cus_id',
                array('cus_firstname'=>'cus_firstname',
                 'cus_lastname' =>'cus_lastname',
                 'cus_birthday'=>'cus_birthday',
                 'cus_lawyer_number' => 'cus_lawyer_number'))     
            ->order('payment_training_offline.payment_training_off_created_date')           
            ->where('category_training.cat_train_id = ?',$cat_id)
            ->where('customers.cus_id = ?',$cus_id)
            ->where('payment_training_offline.payment_training_off_status = 1');
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    public function loadTrainingOfflineToCreateCertificationByCatTrainingId($cat_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_training_offline', array(
                'payment_training_off_id'
            ,'payment_training_off_code'
            ,'payment_training_off_created_date'
            ,'payment_training_off_status'
            ,'payment_training_off_updatedate'
            ,'amount'
            ,'training_certification_number'
            ,'category_training_id'))                
            ->joinLeft(
                'category_training',
                'category_training.cat_train_id = payment_training_offline.category_training_id',
                array('cat_train_name'=>'cat_train_name',
                'cat_train_number'=>'cat_train_number'
                ,'cat_train_fromdate' => 'cat_train_fromdate'
                ,'cat_train_address' => 'cat_train_address',
                'cat_trainer'=>'cat_trainer'))
            ->joinLeft(
                'customers',
                'customers.cus_id = payment_training_offline.cus_id',
                array('cus_firstname'=>'cus_firstname',
                 'cus_lastname' =>'cus_lastname',
                 'cus_birthday'=>'cus_birthday',
                 'cus_lawyer_number' => 'cus_lawyer_number',
                 'cus_date_lawyer_number' => 'cus_date_lawyer_number',
                 'cus_member' => 'cus_member',
                 'cus_lawyer_cityid' => 'cus_lawyer_cityid')) 
            ->joinLeft(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code', 'law_code_createdate' =>'law_code_createdate'))        
            
            ->order('payment_training_offline.payment_training_off_id DESC')           
            ->where('category_training.cat_train_id = ?',$cat_id)
            ->where('payment_training_offline.payment_training_off_status = 1');
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    public function loadPaymentLawyerOfflineToCreateCertificationByCusId($cus_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_training_offline', array('payment_training_off_id'
            ,'payment_training_off_code'
            ,'payment_training_off_created_date'
            ,'payment_training_off_status'
            ,'payment_training_off_updatedate'
            ,'training_certification_number'
            ,'category_training_id'
            ))                  
            ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training_offline.category_training_id',
                array('cat_train_name'=>'cat_train_name','cat_train_number'=>'cat_train_number'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_training_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname','cus_lawyer_number' => 'cus_lawyer_number')) 
            ->joinInner(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code', 'law_code_createdate' =>'law_code_createdate'))        
            
            ->order('payment_training_offline.payment_training_off_created_date')
            ->where('customers.cus_id = ?',$cus_id);
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    public function loadPaymentLawyerOfflineByFilter($start,$length,$search)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('payment_training_offline', array('payment_training_off_id'
            ,'payment_training_off_code'
            ,'payment_training_off_created_date'
            ,'payment_training_off_status'
            ,'payment_training_off_updatedate'        
            ,'training_certification_number'))                 
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_training_offline.category_fee_id',
            array('name'=>'name'))
            ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training_offline.category_training_id',
                array('cat_train_id'=>'cat_train_id','cat_train_name'=>'cat_train_name',
                'cat_train_number'=>'cat_train_number','cat_train_fromdate'=>'cat_train_fromdate',
                'cat_train_address'=>'cat_train_address'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_training_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
                'cus_birthday'=>'cus_birthday','cus_lawyer_number' => 'cus_lawyer_number'
                ,'cus_date_lawyer_number' => 'cus_date_lawyer_number',
                'cus_address_resident' => 'cus_address_resident',
                'cus_address_resident_now' => 'cus_address_resident_now',
                'cus_identity_card' => 'cus_identity_card'
                )) 
            ->joinLeft(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code', 'law_code_createdate' =>'law_code_createdate'))       
            ->where('payment_training_offline.payment_training_off_status = ?',1);
        if($search != ''){
            $query = $query->where('category_training.cat_train_id = ?',$search);
            //->limitPage(0, 1);
        }   

        $query = $query->order('payment_training_offline.payment_training_off_id desc'); 
                  
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
    }

    public function loadLawyerOfflineToCreatingCertificationByFilter($start,$length,$search)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('payment_training_offline', array('payment_training_off_id'
            ,'payment_training_off_code'
            ,'payment_training_off_created_date'
            ,'payment_training_off_status'
            ,'payment_training_off_updatedate'         
            ,'bill_trainingoffline_id'
            ,'checkin'
            ,'training_certification_number'))     
            ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training_offline.category_training_id',
                array('cat_train_id'=>'cat_train_id','cat_train_name'=>'cat_train_name','cat_train_number'=>'cat_train_number'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_training_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
                'cus_lawyer_number' => 'cus_lawyer_number',
                'cus_date_lawyer_number' => 'cus_date_lawyer_number',
                'cus_address_resident' => 'cus_address_resident',
                'cus_address_resident_now' => 'cus_address_resident_now')) 
            ->joinLeft(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code', 'law_code_createdate' =>'law_code_createdate'));       
            
            // ->order('payment_training_offline.payment_training_off_created_date')
            // ->where('payment_training_offline.payment_training_off_status = 1');
        if($search != ''){
            $query = $query->where('category_training.cat_train_id = ?',$search);
            //->limitPage(0, 1);
        }   

        $query = $query->where('payment_training_offline.payment_training_off_status = 1')
        ->order('payment_training_offline.payment_training_off_created_date DESC');
                  
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
    }

    public function countLawyerOfflineToCreatingCertificationByFilter()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('payment_training_offline', array('payment_training_off_id'
            ,'payment_training_off_code'
            ,'payment_training_off_created_date'
            ,'payment_training_off_status'
            ,'payment_training_off_updatedate'         
            ,'bill_trainingoffline_id'
            ,'checkin'))      
            ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training_offline.category_training_id',
                array('cat_train_name'=>'cat_train_name','cat_train_number'=>'cat_train_number'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_training_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname',
                'cus_lawyer_number' => 'cus_lawyer_number')) 
            ->joinInner(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code', 'law_code_createdate' =>'law_code_createdate'));        
            
            // ->order('payment_training_offline.payment_training_off_created_date')
            // ->where('payment_training_offline.payment_training_off_status = 1');

        $query = $query
        //->where('payment_training_offline.checkin = 1')
        ->order('payment_training_offline.payment_training_off_created_date'); 
                  
        $row = $db->fetchAll($query);
       
        return $row;
      
    }

    public function countPaymentLawyerOfflineByFilter()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('payment_training_offline', array('payment_training_off_id'
            ,'payment_training_off_code'
            ,'payment_training_off_created_date'
            ,'payment_training_off_status'
            ,'payment_training_off_updatedate'         
            ,'bill_trainingoffline_id'))
            ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training_offline.category_training_id',
                array('cat_train_name'=>'cat_train_name','cat_train_number'=>'cat_train_number'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_training_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname'
                ,'cus_lawyer_number' => 'cus_lawyer_number')) 
            ->joinInner(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code', 'law_code_createdate' =>'law_code_createdate'));        
            
            // ->order('payment_training_offline.payment_training_off_created_date')
            // ->where('payment_training_offline.payment_training_off_status = 1');

        $query = $query->order('payment_training_offline.payment_training_off_created_date'); 
                  
        $row = $db->fetchAll($query);
       
        return $row;
      
    }

    public function loadPaymentLawyerOfflineToCreateCertification()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_training_offline', array('payment_training_off_id'
            ,'payment_training_off_code'
            ,'payment_training_off_created_date'
            ,'payment_training_off_status'
            ,'payment_training_off_updatedate'         
            ))         
            ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training_offline.category_training_id',
                array('cat_train_name'=>'cat_train_name','cat_train_number'=>'cat_train_number'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_training_offline.cus_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname'
                ,'cus_lawyer_number' => 'cus_lawyer_number')) 
            ->joinInner(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code', 'law_code_createdate' =>'law_code_createdate'))        
            
            ->order('payment_training_offline.payment_training_off_created_date')
            ->where('payment_training_offline.payment_training_off_status = 1');
                        
        $row = $db->fetchAll($select);
        return $row;
    }


    public function loadPaymentTrainingOfflineById($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        // $select = new Zend_Db_Select($db);
        $query = $db->select()
            ->from("payment_training_offline", array('payment_training_off_id'
            ,'payment_training_off_code'
            ,'payment_training_off_created_date'
            ,'payment_training_off_status'
            ,'payment_training_off_updatedate'            
            ,'amount'
            ,'cus_id'))                
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_training_offline.category_fee_id',
            array('name'=>'name'))
            ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training_offline.category_training_id',
                array('cat_train_name'=>'cat_train_name',
                'cat_train_number'=>'cat_train_number'
                ,'cat_train_fromdate' => 'cat_train_fromdate'
                ,'cat_train_address' => 'cat_train_address'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_training_offline.cus_id',
                array('cus_id'=>'cus_id','cus_birthday'=>'cus_birthday','cus_firstname'=>'cus_firstname', 
                'cus_lastname' =>'cus_lastname','cus_lawyer_number' => 'cus_lawyer_number'
                ,'cus_date_lawyer_number' => 'cus_date_lawyer_number'))   
            ->joinLeft(
                'lawyer',
                'lawyer.cus_id = customers.cus_id',
                array('law_code'=>'law_code', 'law_code_createdate' =>'law_code_createdate'))        
            ->where('payment_training_offline.payment_training_off_id = ?',$id)
            ->limit(1);   
                        
        return $db->fetchRow($query);
        // if (!$row) {
        //     return null;
        // }
        //return $row->toArray();
    }

    
    /*load oayment joining ofiline by cus id*/
    public function loadPaymentTrainingOfflineByCusId($cus_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
            ->from('payment_training_offline', array('payment_training_off_id'
            ,'payment_training_off_code'
            ,'payment_training_off_created_date'
            ,'payment_training_off_status'
            ,'payment_training_off_updatedate'
            ,'amount'
            ))  
            ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_training_offline.category_fee_id',
            array('name'=>'name'))
            ->joinInner(
                'customers',
                'customers.cus_id = payment_training_offline.cus_id',
                array('cus_lawyer_number' => 'cus_lawyer_number',
                'cus_firstname'=>'cus_firstname',
                 'cus_lastname' =>'cus_lastname'))
            ->joinLeft(
            'category_training',
            'category_training.cat_train_id = payment_training_offline.category_training_id',
            array('cat_train_name'=>'cat_train_name',
            'cat_train_number'=>'cat_train_number',
            'cat_train_address' => 'cat_train_address'))    
            
            ->order('payment_training_offline.payment_training_off_created_date desc')
            ->where('customers.cus_id = ?',$cus_id);
                        
        $row = $db->fetchAll($select);
        return $row;
    }

     /*load oayment joining ofiline by cus id*/
     public function loadPaymentTrainingOfflineByCategoryTrainingId($id)
     {
         $db = Zend_Db_Table::getDefaultAdapter();
         $select = new Zend_Db_Select($db);
         $select->distinct()
             ->from('payment_training_offline', array('payment_training_off_id'
             ,'payment_training_off_code'
             ,'payment_training_off_created_date'
             ,'payment_training_off_status'
             ,'payment_training_off_updatedate'
             ,'category_training_id'
             ))               
             ->joinInner(
             'category_training',
             'category_training.cat_train_id = payment_training_offline.category_training_id',
             array('cat_train_number'=>'cat_train_number','cat_train_quantity'=>'cat_train_quantity'))             
             ->order('payment_training_offline.payment_training_off_created_date')
             ->where('payment_training_offline.category_training_id = ?',$id)
             ->where('payment_training_offline.payment_training_off_status = ?',1);
                         
         $row = $db->fetchAll($select);
         return $row;
     }

     //láy danh sách id người học
     public function loadListLawyerIdTrained($year){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        $query = $select->distinct()
             ->from('payment_training_offline',
                    array('cus_id'));
                                
        $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';
        $query = $query
        ->where("payment_training_offline.payment_training_off_created_date >= ?",  $start_date_formatted)
        ->where("payment_training_offline.payment_training_off_created_date <= ?",  $end_date_formatted); 
        
        return $db->fetchAll($query);
     }

     // lấy thông tin người học không nằm trong list id

     public function loadLawyerWithoutTrainingByFilterTotals($ids){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);     

        if(sizeof($ids) == 0 ){
            return null;
        }
        $query = $select->distinct()
        ->from(
        'customers',        
        array('cus_id' => 'cus_id'
        ))        
        ->where('customers.cus_id NOT IN (?)', $ids);
                     
        $row = $db->fetchAll($query);

        if($row == null){
            return 0;
        }    
        return count($row);
     }

     public function loadLawyerWithoutTrainingByFilter($start, $length,$ids){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

     

        if(sizeof($ids) == 0 ){
            return null;
        }
        $query = $select->distinct()
        ->from(
        'customers',        
        array('cus_firstname'=>'cus_firstname',
         'cus_lastname' =>'cus_lastname',
        'cus_birthday'=>'cus_birthday'
        ,'cus_cellphone'=>'cus_cellphone'
        ,'cus_identity_card' => 'cus_identity_card'
        ,'cus_lawyer_number' => 'cus_lawyer_number'
        ,'cus_date_lawyer_number' => 'cus_date_lawyer_number'
        ))
        ->joinLeft('lawyer',
            'lawyer.cus_id = customers.cus_id', array(
            'law_id' => 'law_id'
            ,'law_code' => 'law_code'
            ,'law_code_createdate' => 'law_code_createdate'
            ,'law_certfication_no' => 'law_certfication_no'
            ,'law_certification_createdate' => 'law_certification_createdate'
            ,'law_joining_number' => 'law_joining_number'
            ,'createddate' => 'createddate'
            ,'cus_id'=> 'cus_id'))           
        ->where('customers.cus_id NOT IN (?)', $ids);
                       
        $row = $db->fetchAll($query);

        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);

    }

    // lấy thông tin người học 
    // thống kê theo ngày bắt đầu ngày kết thúc

        public function loadLawyerTrainingByFilterTotals($type, $startdate, $enddate){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        $query = $select
        ->from('payment_training_offline', array(
            'payment_training_off_id'            
            ))                  
        ->where('payment_training_offline.payment_training_off_status = 1');      

        $startdate = str_replace('/', '-', $startdate);
        $enddate = str_replace('/', '-', $enddate);
        $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';

        $query = $query
                ->where("payment_training_offline.payment_training_off_created_date >= ?",  $start_date_formatted)
                ->where("payment_training_offline.payment_training_off_created_date <= ?",  $end_date_formatted);
    
        if($type == 'online'){
            $query = $query
            ->where('payment_training_offline.payment_type = ?',$type)
            ->order('payment_training_offline.payment_training_off_created_date desc');
        }else if($type == 'offline'){           
            $query = $query
            ->where('payment_training_offline.payment_type = ?',$type)
            ->order('payment_training_offline.payment_training_off_created_date desc');
        }else{
            $query = $query            
            ->order('payment_training_offline.payment_training_off_created_date desc');
        }
        
        $row = $db->fetchAll($query);

        if($row == null){
            return 0;
        }    
        return count($row);

    }

    public function loadLawyerTrainingByFilter($start, $length, $type, $startdate, $enddate){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        $query = $select->distinct()
        ->from('payment_training_offline', array(
            'payment_training_off_id'
            ,'payment_training_off_code'
            ,'payment_training_off_created_date'
            ,'payment_training_off_status'
            ,'payment_training_off_updatedate'
            ,'amount'            
            ,'payment_type'
            ,'category_training_id'
            ,'category_fee_id'
            ,'cus_id'
            ))
        ->joinInner(
        'customers',
        'customers.cus_id = payment_training_offline.cus_id',
        array('cus_firstname'=>'cus_firstname',
            'cus_lastname' =>'cus_lastname',
        'cus_birthday'=>'cus_birthday'
        ,'cus_cellphone'=>'cus_cellphone'
        ,'cus_identity_card' => 'cus_identity_card'
        ,'cus_lawyer_number' => 'cus_lawyer_number'
        ))
        ->joinInner(
            'category_fee',
            'category_fee.category_fee_id = payment_training_offline.category_fee_id',
            array('name'=>'name'))
        ->joinInner(
            'category_training',
            'category_training.cat_train_id = payment_training_offline.category_training_id',
            array())  
        ->joinLeft(
            'lawyer',
            'lawyer.cus_id = customers.cus_id',
            array('law_code'=>'law_code',
            'law_certfication_no'=>'law_certfication_no'
            ,'law_joining_number' => 'law_joining_number'))            
        ->where('payment_training_offline.payment_training_off_status = 1');      

        $startdate = str_replace('/', '-', $startdate);
        $enddate = str_replace('/', '-', $enddate);
        $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';

        $query = $query
                ->where("payment_training_offline.payment_training_off_created_date >= ?",  $start_date_formatted)
                ->where("payment_training_offline.payment_training_off_created_date <= ?",  $end_date_formatted);
    
        if($type == 'online'){
            $query = $query
            ->where('payment_training_offline.payment_type = ?',$type)
            ->order('payment_training_offline.payment_training_off_created_date desc');
        }else if($type == 'offline'){           
            $query = $query
            ->where('payment_training_offline.payment_type = ?',$type)
            ->order('payment_training_offline.payment_training_off_created_date desc');
        }else{
            $query = $query            
            ->order('payment_training_offline.payment_training_off_created_date desc');
        }            
        
        $row = $db->fetchAll($query);

        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);

    }

    // lấy thông tin thông tin luật sư tham gia đào 
    // thong kê theo năm
    public function loadLawyerTrainedByFilter($start, $length,$year,$type){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        $query = $select
            ->from(
                'customers',               
                array('cus_firstname'=>'cus_firstname',
                    'cus_lastname' =>'cus_lastname',
                'cus_birthday'=>'cus_birthday'
                ,'cus_cellphone'=>'cus_cellphone'
                ,'cus_identity_card' => 'cus_identity_card'
                ,'cus_lawyer_number' => 'cus_lawyer_number'
                ,'cus_date_lawyer_number' => 'cus_date_lawyer_number'   
                ,'cus_address_resident' => 'cus_address_resident'
                ,'cus_address_resident_now' => 'cus_address_resident_now'
                ))           
            ->joinInner(
                'payment_training_offline',
                'payment_training_offline.cus_id = customers.cus_id',
                array(
                'payment_training_off_code'=>'payment_training_off_code' 
                ,'amount'  => 'amount'
                ,'payment_type' => 'payment_type'
                ,'payment_training_off_created_date' =>'payment_training_off_created_date'                              
                ))      
            ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training_offline.category_training_id',
                array('cat_train_name'=>'cat_train_name',
                'cat_train_number'=>'cat_train_number'                  
                ))          
            ->order('payment_training_offline.payment_training_off_id DESC')
            ->where('payment_training_offline.payment_training_off_status = ?','1');
        
        /*
        Nếu năm tồn tại thì dùng cái dưới này
        */
        $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';

        $query = $query
        ->where("payment_training_offline.payment_training_off_created_date >= ?",  $start_date_formatted)
        ->where("payment_training_offline.payment_training_off_created_date <= ?",  $end_date_formatted);                
            
        if($type != ''){
            $query = $query
            ->where('payment_training_offline.payment_type = ?',$type);
        }       

        $row = $db->fetchAll($query);

        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);

    }

    // lấy thông luật sư tham gia bồi dưỡng theo giờ theo năm
    public function loadLawyerTrainedByHoursFilter($start, $length,$year,$lists){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        $query = $select->distinct()
            ->from(
                'customers',               
                array('cus_firstname'=>'cus_firstname',
                    'cus_lastname' =>'cus_lastname',
                'cus_birthday'=>'cus_birthday'
                ,'cus_cellphone'=>'cus_cellphone'
                ,'cus_identity_card' => 'cus_identity_card'
                ,'cus_lawyer_number'
                ,'cus_date_lawyer_number' 
                ,'cus_id' => 'cus_id'
                ,'cus_address_resident' => 'cus_address_resident'
                ,'cus_address_resident_now' => 'cus_address_resident_now'                                             
                ))           
            ->joinInner(
                'payment_training_offline',
                'payment_training_offline.cus_id = customers.cus_id',
                array(
                'payment_training_off_code'=>'payment_training_off_code' 
                ,'amount'  => 'amount'
                ,'payment_type' => 'payment_type'
                ,'payment_training_off_created_date' =>'payment_training_off_created_date'                              
                ))      
            ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training_offline.category_training_id',
                array('cat_train_name'=>'cat_train_name',
                'cat_train_number'=>'cat_train_number'                  
                ))          
            ->order('payment_training_offline.payment_training_off_created_date DESC')
            ->where('payment_training_offline.payment_training_off_status = ?','1');           
        
        /*
        Nếu năm tồn tại thì dùng cái dưới này
        */
        $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';

        $query = $query
        ->where("payment_training_offline.payment_training_off_created_date >= ?",  $start_date_formatted)
        ->where("payment_training_offline.payment_training_off_created_date <= ?",  $end_date_formatted);                
 
        if($lists != null && sizeof($lists)){
            $query = $query
            ->where('customers.cus_id IN (?)', $lists);
        }

        $row = $db->fetchAll($query);

        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);

    }

    public function countLawyerWithoutTrainingByFilter($start, $length,$ids){

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);

        if(sizeof($ids) == 0 ){
            return null;
        }

        $query = $select->distinct()
        ->from('lawyer', array(
        'law_id' => 'law_id'
        ,'law_code' => 'law_code'
        ,'law_code_createdate' => 'law_code_createdate'
        ,'law_certfication_no' => 'law_certfication_no'
        ,'law_certification_createdate' => 'law_certification_createdate'
        ,'law_joining_number' => 'law_joining_number'
        ,'createddate' => 'createddate'
        ,'cus_id'=> 'cus_id'))
        ->joinInner(
        'customers',
        'customers.cus_id = lawyer.cus_id',
        array('cus_firstname'=>'cus_firstname',
         'cus_lastname' =>'cus_lastname',
        'cus_birthday'=>'cus_birthday'
        ,'cus_cellphone'=>'cus_cellphone'
        ,'cus_identity_card' => 'cus_identity_card',
        'cus_lawyer_number' => 'cus_lawyer_number'
        ))  
        ->where('customers.cus_id NOT IN (?)', $ids);
                       
        $row = $db->fetchAll($query);

        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);

    }

}