<?php

class Default_Model_Order extends Zend_Db_Table_Abstract {
  protected $_name    = 'order';
  protected $_primary = 'order_id';

  public function loadOrder() {
    try {
      $sql = 'SELECT o.order_id
                   , o.order_document_date
                   , o.order_invoice_date
                   , o.order_invoice_number
                   , c.cus_code
                   , c.cus_name
                   , o.order_description
                   , o.order_created
                   , sc.staff_username AS order_created_by_username
                   , o.order_last_updated
                   , sm.staff_username AS order_last_updated_by_username
                FROM [order] o
                     JOIN customer c ON o.order_customer_id = c.cus_id
                     LEFT JOIN staff sc ON o.order_created_by_staff_id = sc.staff_id
                     LEFT JOIN staff sm ON o.order_last_updated_by_staff_id = sm.staff_id
            ORDER BY o.order_document_date ASC, c.cus_code ASC';

      $orders = $this->getAdapter()->fetchAll($sql);

      return $orders;
    } catch (Exception $err) {
      throw $err;
    }
  }

  /**
   * Import orders to DB.
   *
   * @param  array $data          Array of rows, each row is array of columns.
   * @param  string $username     Current username.
   * @return object               Import result.
   */
  public function importOrder($data, $username) {
    try {
      $dataCount    = count($data);
      $dataError    = array();
      $successCount = 0;

      for ($i = 0; $i < $dataCount; $i++) {
        try {
          $staffModel = new Default_Model_Staff();
          $staffId    = $staffModel->getStaffIdByUsername($username);

          $cusModel = new Default_Model_Customer();
          $cusId    = $cusModel->getCustomerIdByCustomerCode($data[$i][3]);

          $documentDate = strtotime($data[$i][0]);
          $invoiceDate  = strtotime($data[$i][1]);
          $orderId      = $this->getOrderId($cusId, $documentDate, $data[$i][2], $invoiceDate);

          if ($orderId === null) {
            $orderId = $this->insert(
              array(
                'order_customer_id'         => $cusId,
                'order_description'         => $data[$i][5],
                'order_document_date'       => $documentDate,
                'order_invoice_number'      => $data[$i][2],
                'order_invoice_date'        => $invoiceDate,
                'order_created'             => date('Y-m-d H:i:s'),
                'order_created_by_staff_id' => $staffId,
              )
            );
          }

          $productModel = new Default_Model_Product();
          $productId    = $productModel->getProductIdByProductCode($data[$i][6]);

          $orderDetailModel = new Default_Model_OrderDetail();
          $orderDetailModel->insert(
            array(
              'order_detail_order_id'             => $orderId,
              'order_detail_product_id'           => $productId,
              'order_detail_product_unit_measure' => $data[$i][8],
              'order_detail_quantity'             => $data[$i][9],
              'order_detail_price'                => $data[$i][10],
              'order_detail_pre_tax_amount'       => $data[$i][11],
              'order_detail_tax_amount'           => $data[$i][14],
              'order_detail_total'                => $data[$i][15],
              'order_detail_return_quantity'      => $data[$i][12],
              'order_detail_return_amount'        => $data[$i][13],
            )
          );

          $successCount++;
        } catch (Exception $err1) {
          array_push($dataError, $data[$i]);
        }
      }

      $status = 0; // All error

      if ($successCount === $dataCount) {
        $status = 1; // All success
      } else if ($successCount > 0) {
        $status = 2; // Partial success
      }

      return array(
        'ordersError'  => $dataError,
        'successCount' => $successCount,
        'status'       => $status,
      );
    } catch (Exception $err2) {
      throw $err2;
    }
  }

  /**
   * Insert order to DB.
   *
   * @param  array  $data     Data row.
   * @param  string $username Current username.
   * @return int              Order id.
   */
  public function insertOrder($data, $username) {
    try {
      $orderExists = $this->isOrderExists(
        $data['order_customer_id'],
        $data['order_document_date'],
        $data['order_invoice_number'],
        $data['order_invoice_date']
      );

      if (!$orderExists) {
        $staffModel = new Default_Model_Staff();
        $staffId    = $staffModel->getStaffIdByUsername($username);

        $data['order_created']             = date('Y-m-d H:i:s');
        $data['order_created_by_staff_id'] = $staffId;

        $orderId = $this->insert($data);

        return $orderId;
      } else {
        throw new Exception('Order exists');
      }
    } catch (Exception $err) {
      throw $err;
    }
  }

  /**
   * Update order information.
   *
   * @param  string $orderId  Order id.
   * @param  array  $data     Data row.
   * @param  string $username Current username.
   * @return int              Number of rows affected.
   */
  public function updateOrder($orderId, $data, $username) {
    try {
      $orderExists = $this->isOrderExists(
        $data['order_customer_id'],
        $data['order_document_date'],
        $data['order_invoice_number'],
        $data['order_invoice_date'],
        $orderId
      );

      if (!$orderExists) {
        $staffModel = new Default_Model_Staff();
        $staffId    = $staffModel->getStaffIdByUsername($username);

        $data['order_last_updated']             = date('Y-m-d H:i:s');
        $data['order_last_updated_by_staff_id'] = $staffId;

        $where         = $this->getAdapter()->quoteInto('order_id = ?', $orderId);
        $affectedCount = $this->update($data, $where);

        return $affectedCount;
      } else {
        throw new Exception('Order exists');
      }
    } catch (Exception $err) {
      throw $err;
    }
  }

  /**
   * Delete order.
   *
   * @param  int    $orderId          Order id.
   * @param  string $cusCode          Customer code.
   * @param  string $documentDate     Document date.
   * @param  string $invoiceNumber    Invoice number.
   * @param  string $invoiceDate      Invoice date.
   * @param  string $username         Current username.
   * @return int                      The number of rows deleted.
   */
  public function deleteOrder($orderId, $cusCode, $documentDate, $invoiceNumber, $invoiceDate, $username) {
    try {
      $staffModel = new Default_Model_Staff();
      $staffId    = $staffModel->getStaffIdByUsername($username);

      $cusModel = new Default_Model_Customer();
      $cusId    = $cusModel->getCustomerIdByCustomerCode($cusCode);

      $adapter = $this->getAdapter();

      // Also delete data in ORDER_DETAIL b/c of delete cascade
      $affectedCount = $this->delete(
        array(
          $adapter->quoteInto('order_id = ?', $orderId),
          $adapter->quoteInto('order_customer_id = ?', $cusId),
          $adapter->quoteInto('DATEDIFF(DAY, order_document_date, ?) = 0', $documentDate),
          $adapter->quoteInto('order_invoice_number = ?', $invoiceNumber),
          $adapter->quoteInto('DATEDIFF(DAY, order_invoice_date, ?) = 0', $invoiceDate),
        )
      );

      $logText = 'Xóa đơn hàng (Mã KH: ' . $cusCode;
      $logText .= ', Ngày chứng từ: ' . date("d/m/Y", strtotime($documentDate));
      $logText .= ', Số hóa đơn: ' . $invoiceNumber;
      $logText .= ', Ngày hóa đơn: ' . date("d/m/Y", strtotime($invoiceDate)) . ')';

      $logger = new Default_Model_LogOperation();
      $logger->writeLog($logText, $username);

      return $affectedCount;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function getOrderByOrderId($orderId) {
    $select  = $this->select(true)->where('order_id = ?', $orderId);
    $order = $this->fetchRow($select);
    return $order;
  }

  // --------------- PRIVATE FUNCTIONS ---------------

  private function getOrderId($cusId, $documentDate, $invoiceNumber, $invoiceDate) {
    $sql = 'SELECT order_id
              FROM [order]
             WHERE     order_customer_id = ?
                   AND DATEDIFF(DAY, order_document_date, ?) = 0
                   AND order_invoice_number = ?
                   AND DATEDIFF(DAY, order_invoice_date, ?) = 0';

    $order = $this->getAdapter()->fetchRow($sql, array($cusId, $documentDate, $invoiceNumber, $invoiceDate));

    if ($order) {
      return $order['order_id'];
    } else {
      return null;
    }
  }

  private function isOrderExists($cusId, $documentDate, $invoiceNumber, $invoiceDate, $orderId = null) {
    $sql = 'SELECT order_id
              FROM [order]
             WHERE     order_customer_id = ?
                   AND DATEDIFF(DAY, order_document_date, ?) = 0
                   AND order_invoice_number = ?
                   AND DATEDIFF(DAY, order_invoice_date, ?) = 0';

    $order = $this->getAdapter()->fetchRow($sql, array($cusId, $documentDate, $invoiceNumber, $invoiceDate));

    if ($order) {
      return $order['order_id'] !== $orderId;
    } else {
      return false;
    }
  }
}
