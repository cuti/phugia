<?php

class Default_Model_PaymentJoiningOnline  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'payment_joining';

    protected $_primary = 'payment_joining_id'; 

    protected $_sequence = true;

    // public function countPaymentJoiningOnlineByCusId($cus_id){
    //     $db = Zend_Db_Table::getDefaultAdapter();
    //     $select = new Zend_Db_Select($db);
    //     $query = $select->from('payment_joining',array('payment_joining_id'))
    //     ->joinInner(
    //         'bill_fee_lawyer_temp',
    //         'bill_fee_lawyer_temp.bill_feelawyer_temp_id = payment_joining.bill_feelawyer_temp_id',
    //         array())
    //     ->where('bill_fee_lawyer_temp.cus_id = ?', $cus_id);
    //     // ->where('payment_joining.payment_joining_status = ?','1');
         
    //     $row = $db->fetchAll($query);
    //     return $row;
    // }

}