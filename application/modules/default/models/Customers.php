<?php

class Default_Model_Customers  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'customers';

    protected $_primary = 'cus_id'; 

    protected $_sequence = true;


    /*load customers*/
    public function loadCustomersByIds($ids)
    {
        $select = $this->select()
                       ->from($this,array('cus_status','cus_birthday','cus_identity_place',
                       'cus_identity_card','cus_sex','cus_id','cus_firstname',
                       'cus_lastname','cus_email',
                       'cus_cellphone','cus_fullname','cus_lawyer_number','cus_date_lawyer_number'
                       ,'cus_lawyer_number','cus_date_lawyer_number','cus_member'))
                       ->where('cus_status = ?','1');

        if($ids != null && $ids != ''){
            $select = $select->where('cus_id IN (?)',explode(",",$ids));
        }               
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    /**search customers */
    public function searchCustomersFilter($enddate,$startdate,$hovalot,$ten,$cmnd,$thanhvien,$trinhdohv,
    $diachilienhe,$thuongtru,$lamviec,$gioitinh,$socchn,$dotgianhap,$diachi,$tinhtrang,
    $dangvien,$phanloai,$sapxep,$ngoaingu,$start,$length,$sothels,$noisinh,$dottapsu,$xoaten){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->from("customers",
            array('cus_id' => 'cus_id', 'cus_status','cus_birthday','cus_identity_place',
                'cus_identity_card','cus_sex','cus_id','cus_firstname','cus_lastname',
                'cus_cellphone','cus_lawyer_number','cus_date_lawyer_number','cus_member','cus_fullname',
            'deleted_online','cus_email','cus_address_resident','cus_address_resident_now'))
            ->joinLeft(
                'lawyer',
                'customers.cus_id = lawyer.cus_id',
            array(
            'law_certfication_no' => 'law_certfication_no',
            'law_certification_createdate' => 'law_certification_createdate',
            'law_joining_number' => 'law_joining_number'
            ))
            ->joinLeft(
                'languages',
                'customers.language_id = languages.language_id',
            array(
            'language_id' => 'language_id',
            'language_name' => 'language_name'       
            ))
            ->joinLeft(
                'intership',
                'customers.cus_id = intership.cus_id',
            array(
            'inter_number_name' => 'inter_number_name'
            ))
            ->joinLeft(
                'lawyer_removed',
                'lawyer_removed.cus_id = customers.cus_id',
            array(
            'type_deleted' => 'type_deleted'
            ));
                //->where('city_active = ?', '1');
        if($hovalot != '' && $hovalot != null){
            $hovalot = trim($hovalot);
            $query =  $query->where('cus_firstname LIKE N'."'".'%' . $hovalot .'%'."'");  
        }

        if($ten != '' && $ten != null){
            $ten = trim($ten);
            $query =  $query->where('cus_lastname LIKE N'."'".'%' . $ten .'%'."'");  
        }

        if($gioitinh != '' && $gioitinh != null){
            if($gioitinh == 'nam'){
                $nam = 'Nam';
                $query =  $query->where('cus_sex LIKE N'."'".'%' . $nam .'%'."'"); 
            }else if($gioitinh == 'nu'){
                $nu = 'Nữ';
                $query =  $query->where('cus_sex LIKE N'."'".'%' . $nu .'%'."'"); 
            }
             
        }

        if($enddate != '' && $startdate != ''){
            $startdate = str_replace('/', '-', $startdate);
            $enddate = str_replace('/', '-', $enddate);
            $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';          
            $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';
            $query = $query
            ->where("cus_birthday >= ?",  $start_date_formatted)
            ->where("cus_birthday <= ?",  $end_date_formatted);            
        }else if($enddate != ''){
            //$start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';            
            $enddate = str_replace('/', '-', $enddate);          
            $end_date_formatted = date('Y-m-d', strtotime($enddate)).' 23:59:59';
            $query = $query
            ->where("cus_birthday >= ?",  $end_date_formatted);            
        }else if($startdate != ''){
            $startdate = str_replace('/', '-', $startdate);           
            $start_date_formatted = date('Y-m-d', strtotime($startdate)).' 00:00:00';
            $query = $query
            ->where("cus_birthday >= ?",  $start_date_formatted);   
        }

        //cmnd
        if($cmnd != '' && $cmnd != null){
            $query =  $query->where('cus_identity_card = ?',$cmnd);
        }
       
        if($sothels != '' && $sothels != null){
            $query =  $query->where('cus_lawyer_number = ?',$sothels);
        } 

        //cmnd
        if($socchn != '' && $socchn != null){
            $query =  $query->where('lawyer.law_certfication_no = ?',$socchn);
        }

        //cmnd
        if($thanhvien != '' && $thanhvien != null && $thanhvien == 1){    
            if($dotgianhap != '' && $dotgianhap != null){
                $query =  $query->where('lawyer.law_joining_number = ?',$dotgianhap);
            }
        }else if($thanhvien != '' && $thanhvien != null && $thanhvien == 0){
            //cmnd            
            if($dottapsu != '' && $dottapsu != null){
                $query =  $query->where('intership.inter_number_name = ?',$dottapsu);
            }        
        }

        if($tinhtrang != '' && $tinhtrang != null ){
            if($tinhtrang != '' && $tinhtrang != null && $tinhtrang != 0){            
                $query =  $query->where('cus_status = ?',$tinhtrang);
            }        

            if($tinhtrang != '' && $tinhtrang != null && $tinhtrang == 7){
                if($xoaten != '' && $xoaten != null && $xoaten == 1) {
                    //kiluat
                    $query =  $query->where('lawyer_removed.type_deleted LIKE N'."'".'%' . 'kiluat' .'%'."'"); 
                }else if($xoaten != '' && $xoaten != null && $xoaten == 2){
                    //no phi
                    $query =  $query->where('lawyer_removed.type_deleted LIKE N'."'".'%' . 'nophi' .'%'."'"); 
                }
            }
        }
	
        if($ngoaingu != '' && $ngoaingu != null ){
            if($ngoaingu != 'all'){
                $query =  $query->where('languages.language_id = ?',$ngoaingu);
            }            
        } 

        if($trinhdohv != '' && $trinhdohv != null){
            $query =  $query->where('cus_educations LIKE N'."'" . $trinhdohv .'%'."'");  
        }

        if($thanhvien != '' && $thanhvien != null && $thanhvien != 'all'){
            $query =  $query->where('cus_type  =?',$thanhvien);  
        }

        if($noisinh != '' && $noisinh != null && $noisinh != 'all'){
            $query =  $query->where('city_id  = ?',(int)$noisinh);  
        }

        if($diachilienhe != '' && $diachilienhe != null && $diachilienhe != 'all'){
            $query =  $query->where('cus_address_resident_now LIKE N'."'" . $diachilienhe .'%'."'");  
        }

        if($thuongtru != '' && $thuongtru != null && $thuongtru != 'all'){
            $query =  $query->where('cus_address_resident LIKE N'."'" . $thuongtru .'%'."'");  
        }

        // if($lamviec != '' && $lamviec != null && $lamviec != 'all'){
        //     $query =  $query->where('cus_educations LIKE N'."'" . $trinhdohv .'%'."'");  
        // }
	 
        $query = $query->order('cus_lastname asc');

        // $startdate = str_replace('/', '-', $startdate);
        //     $enddate = str_replace('/', '-', $enddate);
        
        // echo "<prev>";
        //     echo $query;
        //     echo $enddate;
        //     echo date('Y-m-d', strtotime($startdate)).' 00:00:00';
        // echo "</prev>";
        // exit;
        
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
    }

    /*load customers*/
    public function loadCustomers()
    {
        $select = $this->select()
                       ->from($this,array('cus_status','cus_birthday','cus_identity_place',
                       'cus_identity_card','cus_sex','cus_id','cus_firstname','cus_lastname',
                       'cus_cellphone','cus_lawyer_number','cus_date_lawyer_number','cus_member','cus_fullname'));
                       //->where('city_active = ?', '1');
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    public function loadCustomersFilter($search,$start,$length){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->from("customers",array('cus_id' => 'cus_id', 'cus_status','cus_birthday','cus_identity_place',
                       'cus_identity_card','cus_sex','cus_id','cus_firstname','cus_lastname',
                       'cus_cellphone','cus_lawyer_number','cus_date_lawyer_number','cus_member','cus_fullname',
                    'deleted_online'))
                    ->where('cus_status = ?', '1');
        if($search != ''){
            //$query = $query->where('customers.cus_lawyer_number = ?',$search);
            //->limitPage(0, 1);
            $query =  $query ->where('cus_fullname LIKE N'."'" . $search .'%'."'");  
        }
        
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
        
    }

    /*get endmonth fee payment by count max table fee payment_lawyer_offline by cus_id*/

    public function getEndMonthByCusId($cus_id){     
        $db = Zend_Db_Table::getDefaultAdapter(); 
        
        $query = $db->select()
        ->from("payment_lawyer_offline",
            array(new Zend_Db_Expr('max(payment_lawyer_off_id) as maxId')
        ))->where('cus_id = ?',$cus_id)
	->where('payment_lawyer_off_status = ?',1);
        
        $query1 = $db->select()
            ->from("payment_lawyer_offline",array('endmonth'))
            ->where('payment_lawyer_off_id = ?',$db->fetchAll($query));
        $row = $db->fetchRow($query1); 
        if (!$row) {
            return null;
        }
        return $row['endmonth'];
    }

    /*get endmonth fee payment by count max table fee payment_lawyer_offline by cus_id*/

    public function getLastPaymentJoiningByCusId($cus_id){     
        $db = Zend_Db_Table::getDefaultAdapter(); 
        
        $query = $db->select()
        ->from("payment_joining_offline",
            array(new Zend_Db_Expr('max(payment_joining_off_id) as maxId')
        ))->where('cus_id = ?',$cus_id)
        ->where('payment_joining_off_status = ?',1);
        
        $query1 = $db->select()
            ->from("payment_joining_offline",array('payment_joining_off_code'))
            ->where('payment_joining_off_id = ?',$db->fetchAll($query));
        $row = $db->fetchRow($query1); 
        if (!$row) {
            return null;
        }
        return $row['payment_joining_off_code'];
    }


            /*get endmonth fee payment by count max table fee payment_lawyer_offline by cus_id*/

    public function getLastPaymentIntershipByCusId($cus_id){     
        $db = Zend_Db_Table::getDefaultAdapter(); 
        
        $query = $db->select()
        ->from("payment_intership_offline",
            array(new Zend_Db_Expr('max(payment_inter_off_id) as maxId')
        ))->where('cus_id = ?',$cus_id)
        ->where('payment_inter_off_status = ?',1);
        
        $query1 = $db->select()
            ->from("payment_intership_offline",array('payment_inter_off_code'))
            ->where('payment_inter_off_id = ?',$db->fetchAll($query));
        $row = $db->fetchRow($query1); 
        if (!$row) {
            return null;
        }
        return $row['payment_inter_off_code'];
    }

    /*get customer by phone and load fee payment*/
    public function getCustomerWithLawyerFee($phone_number){
        $db = Zend_Db_Table::getDefaultAdapter();
        // $select = new Zend_Db_Select($db);
        $query = $db->select()
            ->from("customers", 
            array('cus_id',
            'cus_username',
            'cus_password',
            'cus_email',
            'cus_firstname',
            'cus_lastname',
            'cus_sex',
            'cus_country',
            'cus_birthday',
            'cus_nation',
            'cus_birthplace',
            'cus_cellphone',
            'cus_homephone',
            'cus_identity_card',
            'cus_identity_place',
            'cus_identity_date',
            // 'cus_passport_card',
            // 'cus_passport_place',
            // 'cus_passport_date',
            'cus_address_contact',
            'cus_address_resident',
            'cus_educations',
            'cus_member',
            'cus_language_level',
            'cus_users_created',
            'cus_date_created',
            'cus_users_update',
            'cus_date_update',
            'cus_status',
            'cus_active',
            'city_id',
            'user_id',
            //'cus_lawyer_hcm',
            'cus_fullname',
            'cus_joining_communist_youth',
            'cus_joining_communist_prepare',
            'cus_joining_communist',
            //'cus_major',
            'cus_address_resident_now',
            'cus_religion',
            'cus_organization',
            'cus_lawyer_number',
            'cus_date_lawyer_number',
            'cus_ls_tinh',
            'cus_lawyer_cityid'
            ))
            ->joinLeft(
                'lawyer',
                'customers.cus_id = lawyer.cus_id',
            array(
            'law_id' => 'law_id',
            'law_code' => 'law_code',
            'law_certification_createdate'=>'law_certification_createdate',
            'law_certfication_no'=>'law_certfication_no'
            // 'endmonth' => 'endmonth',
            // 'startmonth' => 'startmonth' 
            ))
            ->joinLeft(
                'intership',
                'customers.cus_id = intership.cus_id',
            array(
            'inter_id' => 'inter_id',
            'inter_number_name' => 'inter_number_name'            
            ))
            ->joinLeft(
                'city',
                'customers.city_id = city.city_id',
            array(
            'city_name' => 'city_name'  ,
            'city_id' => 'city_id'            
            ))
            ->joinLeft(
                'languages',
                'customers.language_id = languages.language_id',
            array(
            'language_id' => 'language_id',
            'language_name' => 'language_name'            
            ))
            ->where('customers.cus_identity_card = ?',$phone_number); 
                        
        $result =  $db->fetchRow($query); 
        if (!$result) {
            return null;
        }
        return $result;
    }

    /*get customer by phone*/
    public function getCustomer($phone_number)
    {
        $row = $this->fetchRow('cus_cellphone = ' 
        . $phone_number);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    /*get customer by userid*/
    public function getCustomerByUserId($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('cus_id = ' . $id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    /*searchu customer by id or name */
    public function searchByCellPhoneOrIdentityCardOrName($q,$field)
    {
        $select = $this->select()
                       ->from($this,array("cus_fullname" => "cus_fullname",
                       "cus_cellphone" => "cus_cellphone",
                       "cus_birthday" => "cus_birthday","cus_identity_card"=>"cus_identity_card"));                
                       
        $q = trim($q);
        if($field != null){
            if($field == "lawyer_number"){
                $select =  $select ->where('cus_lawyer_number LIKE ?','%' . $q .'%');  
            }else if($field == "identity_card"){
                $select =  $select ->where('cus_identity_card LIKE ?','%' . $q .'%');   
            }else if($field == "name"){
                $select =  $select ->where('cus_fullname LIKE N'."'" . $q .'%'."'");             
            }else{
                $select =  $select ->where('cus_cellphone LIKE ?','%' . $q .'%'); 
            }
        }
        $arrayStatus = array(1,3,4,5,6,7);
        $select = $select->where('cus_status IN (?)',$arrayStatus);
        $select = $select->order('cus_id');   
        $row = $this->fetchAll($select);
         return $row;
    }
    
    /* search ls tinh*/
    public function searchByCellPhoneOrIdentityCardOrNameTinh($q,$field)
    {
        $select = $this->select()
                       ->from($this,array("cus_fullname" => "cus_fullname",
                       "cus_cellphone" => "cus_cellphone",
                       "cus_birthday" => "cus_birthday",
                       "cus_identity_card"=>"cus_identity_card"
                        ,"cus_ls_tinh" => "cus_ls_tinh"));                
                       
        $q = trim($q);
        if($field != null){
            if($field == "lawyer_number"){
                $select =  $select ->where('cus_lawyer_number LIKE ?','%' . $q .'%');  
            }else if($field == "identity_card"){
                $select =  $select ->where('cus_identity_card LIKE ?','%' . $q .'%');   
            }else if($field == "name"){
                $select =  $select ->where('cus_fullname LIKE N'."'" . $q .'%'."'");             
            }else{
                $select =  $select ->where('cus_cellphone LIKE ?','%' . $q .'%'); 
            }
        }
        $select = $select->where('cus_status = ?',1);
        $select = $select->where('cus_ls_tinh = ?',1);
        $select = $select->order('cus_id');   
        $row = $this->fetchAll($select);
         return $row;
    }


    /*update customer by cus_cellphone*/
    public function updateCustomer($cus_cellphone,$cus_firstname)
    {
        $data = array(
            // 'payment' => $payment,
            // 'pass' => $pass,
            'cus_firstname'=> $cus_firstname
        );
        $this->update($data, 'cus_cellphone = '. $cus_cellphone);
    }

    public function validateUsernameCustomer($type,$data){
    
         $checkquery = $this->select()
        ->from($this, array("num"=>"COUNT(*)"))
        ->where("cus_username = ?", $data);
       

        $checkrequest = $this->fetchRow($checkquery);
        // echo $checkrequest["num"];

        // $row = $this->fetchRow($select);
        return $checkrequest["num"];

    }

    public function validateIndentityCardOrPhoneOrEmail($type,$data){

        $checkquery = $this->select()
        ->from($this, array("num"=>"COUNT(*)"));
        if($type == 'ID'){
            $checkquery = $checkquery->where("cus_identity_card = ?", $data);   
        }else if($type == 'phone'){
            $checkquery = $checkquery->where("cus_cellphone = ?", $data);   
        }else if($type == 'email'){
            $checkquery = $checkquery->where("cus_email = ?", $data);   
        }else if($type == 'username'){
            $checkquery = $checkquery->where("cus_username = ?", $data);  
        }       

        $checkrequest = $this->fetchRow($checkquery);
        return $checkrequest["num"];
    }




    /* validate data customer*/
    public function validateCustomer($type,$data){
        //validate phone number
        $row = null;
        if($type == "phone"){
            $row = $this->fetchRow('cus_cellphone = ' . $data);
        }else if($type == "cardnumber"){
            $row = $this->fetchRow('cus_identity_card = ' . $data);    
        }else if($type == "username"){
            $row = $this->fetchRow('cus_username = ' . $data);     
        }        

        // echo $row->toArray();

        if (!$row) {
            return null;
        }  

        return $row->toArray();
    }

}    