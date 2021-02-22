<?php

class Default_Model_PaymentIntershipOnline  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'payment_intership';

    protected $_primary = 'payment_inter_id'; 

    protected $_sequence = true;

    // public function countPaymentIntershipOnlineByCusId($cus_id){
    //     $db = Zend_Db_Table::getDefaultAdapter();
    //     $select = new Zend_Db_Select($db);
    //     $query = $select->from('payment_intership',array('payment_inter_id'))
    //     ->joinInner(
    //         'bill_fee_lawyer_temp',
    //         'bill_fee_lawyer_temp.bill_feelawyer_temp_id = payment_intership.bill_feelawyer_temp_id',
    //         array())
    //     ->where('bill_fee_lawyer_temp.cus_id = ?', $cus_id)
    //     ->where('payment_intership.payment_inter_status = ?','1');
         
    //     $row = $db->fetchAll($query);
    //     return $row;
    // }
}
