<?php

class Default_Model_OrderDetail extends Zend_Db_Table_Abstract {
  protected $_name    = 'order_detail';
  protected $_primary = 'order_detail_id';

  public function getOrderDetailByOrderId($orderId) {
    try {
      $sql = 'SELECT od.order_detail_id
                   , od.order_detail_order_id
                   , od.order_detail_product_id
                   , pc.product_code
                   , pc.product_name
                   , od.order_detail_product_unit_measure
                   , od.order_detail_quantity
                   , od.order_detail_price
                   , od.order_detail_pre_tax_amount
                   , od.order_detail_return_quantity
                   , od.order_detail_return_amount
                   , od.order_detail_tax_amount
                   , od.order_detail_total
                FROM order_detail od
                     JOIN product_catalog pc ON od.order_detail_product_id = pc.product_id
               WHERE od.order_detail_order_id = ?';

      $orderDetail = $this->getAdapter()->fetchAll($sql, array($orderId));

      return $orderDetail;
    } catch (Exception $err) {
      throw $err;
    }
  }
}
