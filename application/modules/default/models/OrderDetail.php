<?php

class Default_Model_OrderDetail extends Zend_Db_Table_Abstract {
  protected $_name    = 'order_detail';
  protected $_primary = 'order_detail_id';

  public function getOrderDetailByOrderId($orderId) {
    try {
      $sql = 'SELECT order_detail_id
                   , order_detail_order_id
                   , order_detail_product_id
                   , order_detail_description
                   , order_detail_quantity
                   , order_detail_price
                   , order_detail_pre_tax_amount
                   , order_detail_tax_rate
                   , order_detail_tax_amount
                   , order_detail_total
                   , order_detail_return_quantity
                   , order_detail_return_amount
                FROM order_detail
               WHERE order_detail_order_id = ?';

      $orderDetail = $this->getAdapter()->fetchAll($sql, array($orderId));

      return $orderDetail;
    } catch (Exception $err) {
      throw $err;
    }
  }
}
