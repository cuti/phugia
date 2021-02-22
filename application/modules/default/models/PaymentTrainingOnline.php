<?php

class Default_Model_PaymentTrainingOnline  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'payment_training';

    protected $_primary = 'payment_traning_id'; 

    protected $_sequence = true;

    // lấy thông tin thông tin luật sư tham gia bồi dưỡng
    public function loadLawyerTrainedByFilter($start, $length,$year){

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
            ))           
            ->joinInner(
                'payment_training',
                'payment_training.customer_id = customers.cus_id',
                array('payment_training_code'=>'payment_training_code' 
                ,'amount'  => 'amount',
                'payment_training_created_at' =>'payment_training_created_at'
                ,'payment_training_status'=>'payment_training_status'                              
                ))      
            ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training.category_training_id',
                array('cat_train_name'=>'cat_train_name',
                'cat_train_number'=>'cat_train_number'                  
                ))          
            ->order('payment_training.payment_training_created_at DESC')
            ->where('payment_training.payment_training_status = ?','1');
        $start_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-01-01')))).' 00:00:00';          
        $end_date_formatted = date('Y-m-d', strtotime(date('Y-m-d', strtotime($year.'-12-31')))).' 00:00:00';

        $query = $query
        ->where("payment_training.payment_training_created_at >= ?",  $start_date_formatted)
        ->where("payment_training.payment_training_created_at <= ?",  $end_date_formatted);                
            
        $row = $db->fetchAll($query);

        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);

    }

    // lấy thông tin đăng kí tham gia bồi dưỡng
    public function loadTrainedByCatTraining($start,$length,$search){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $query = $select->distinct()
            ->from('payment_training', array(
                'payment_training_id'
            ,'payment_training_code'
            ,'payment_training_created_at'
            ,'payment_training_status'
            ,'payment_training_updated_at'         
            ,'amount'
            ,'training_certification_number'
            ))
            //thiếu cái join category_fee_training --> không biết được nó đóng bn %                  
            ->joinInner(
            'category_training',
            'category_training.cat_train_id = payment_training.category_training_id',
            array('cat_train_name'=>'cat_train_name',
            'cat_train_number'=>'cat_train_number',
            'cat_train_fromdate'=>'cat_train_fromdate',
            'cat_train_address' => 'cat_train_address'
            ))           
            ->joinInner(
                'customers',
                'customers.cus_id = payment_training.customer_id',
                array('cus_firstname'=>'cus_firstname', 'cus_lastname' =>'cus_lastname'
                ,'cus_lawyer_number' => 'cus_lawyer_number')) 
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

        //$query = $query->where('payment_training_offline.checkin = 1')
        $query = $query
        ->where('payment_training.payment_training_status = 1')
        ->order('payment_training.payment_training_created_at DESC');
                  
        $row = $db->fetchAll($query);
        if($start == '' && $length == ''){
            return $row;
        }    
        return array_slice($row,$start,$length);
    }

      /*load oayment online by cat id not filter*/
      public function loadPaymentTrainingOnlineByCategoryTrainingId($id)
      {
          $db = Zend_Db_Table::getDefaultAdapter();
          $select = new Zend_Db_Select($db);
          $select->distinct()
              ->from('payment_training', array(
                  'payment_training_id'
              ,'payment_training_code'
              ,'payment_training_created_at'
              ,'payment_training_status'
              ,'payment_training_updated_at'             
              ))
              ->joinInner(
                'category_training',
                'category_training.cat_train_id = payment_training.category_training_id',
                array())
              ->where('payment_training.payment_training_status = 1')
              ->where('category_training.cat_train_id = ?',$id);
                          
          $row = $db->fetchAll($select);
          return $row;
      }

}
