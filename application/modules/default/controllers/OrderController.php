<?php

require_once 'MyControllerAction.php';

class OrderController extends MyControllerAction {
  public function indexAction() {
    $this->view->pageTitle = 'Quản Lý Đơn Hàng';
  }

  public function getAllAction() {
    $this->setRestResponse();

    try {
      $req = $this->getRequest();

      if ($req->isXmlHttpRequest() && $req->isGet()) {
        $order  = new Default_Model_Order();
        $data   = $order->loadOrder();
        $result = array('data' => $data);
      } else {
        $this->setStatusCode(404);
        $result = 0;
      }
    } catch (Exception $err) {
      $this->setStatusCode(500);
      $result = 0;
    }

    echo json_encode($result);
  }

  public function importAction() {
    $this->setRestResponse();

    try {
      $req = $this->getRequest();

      if ($req->isXmlHttpRequest() && $req->isPost()) {
        $content         = base64_decode($req->getParam('fileContent'));
        $fileNameWithExt = $req->getParam('fileName');

        require_once 'Utility.php';

        $username   = $this->currentUser();
        $saveResult = Utility::saveFile($fileNameWithExt, $content, 'order', $username);

        if ($saveResult) {
          require_once 'ExcelReaderWriter.php';

          $fileData = ExcelReaderWriter::read($saveResult['path'], $saveResult['ext']);

          $order        = new Default_Model_Order();
          $importResult = $order->importOrder($fileData, $username);
        }
      } else {
        $importResult = array(
          'message' => 'Invalid request',
          'status'  => -1,
        );
        $this->setStatusCode(404);
      }
    } catch (Exception $err) {
      $importResult = array(
        'message' => 'Cannot import data',
        'status'  => -1,
      );
      $this->setStatusCode(500);
    }

    echo json_encode($importResult);
  }

  public function insertAction() {
    $this->setRestResponse();

    try {
      $req = $this->getRequest();

      if ($req->isXmlHttpRequest() && $req->isPost()) {
        $body        = $req->getRawBody();
        $data        = json_decode($body);
        $order       = json_decode(json_encode($data->order), true);
        $orderDetail = json_decode(json_encode($data->orderDetail), true);

        $orderModel = new Default_Model_Order();
        $orderId    = $orderModel->insertOrder($order, $this->currentUser());

        $orderDetail['order_detail_order_id'] = $orderId;

        $orderDetailModel = new Default_Model_OrderDetail();
        $orderDetailId    = $orderDetailModel->insert($orderDetail);

        $insertResult = array(
          'data'   => $order,
          'status' => 1,
        );
      } else {
        $insertResult = array(
          'message' => 'Invalid request',
          'status'  => -1,
        );
        $this->setStatusCode(404);
      }
    } catch (Exception $err) {
      if ($err->getMessage() === 'Order exists') {
        $insertResult = array(
          'message' => 'Order exists',
          'status'  => 0,
        );
      } else {
        $insertResult = array(
          'message' => 'Cannot create order',
          'status'  => -1,
        );
        $this->setStatusCode(500);
      }
    }

    echo json_encode($insertResult);
  }

  public function updateAction() {
    $this->setRestResponse();

    try {
      $req = $this->getRequest();

      if ($req->isXmlHttpRequest() && $req->isPost()) {
        $body        = $req->getRawBody();
        $data        = json_decode($body);
        $order       = json_decode(json_encode($data->order), true);
        $orderId     = $data->orderId;
        $orderDetail = json_decode(json_encode($data->orderDetail), true);

        $orderModel   = new Default_Model_Order();
        $affectedRows = $orderModel->updateOrder($orderId, $order, $this->currentUser());

        $orderDetail['order_detail_order_id'] = $orderId;

        $orderDetailModel = new Default_Model_OrderDetail();
        $where            = $orderDetailModel->getAdapter()->quoteInto('order_detail_order_id = ?', $orderId);
        $affectedRows     = $orderDetailModel->delete($where);
        $orderDetailId    = $orderDetailModel->insert($orderDetail);

        $updateResult = array(
          'data'   => $order,
          'status' => 1,
        );
      } else {
        $updateResult = array(
          'message' => 'Invalid request',
          'status'  => -1,
        );
        $this->setStatusCode(404);
      }
    } catch (Exception $err) {
      if ($err->getMessage() === 'Order exists') {
        $updateResult = array(
          'message' => 'Order exists',
          'status'  => 0,
        );
      } else {
        $updateResult = array(
          'message' => 'Cannot update order',
          'status'  => -1,
        );
        $this->setStatusCode(500);
      }
    }

    echo json_encode($updateResult);
  }

  public function deleteAction() {
    $this->setRestResponse();

    try {
      $req = $this->getRequest();

      if ($req->isXmlHttpRequest() && $req->isPost()) {
        $body          = $req->getRawBody();
        $data          = json_decode($body);
        $orderId       = $data->orderId;
        $customerCode  = $data->customerCode;
        $documentDate  = $data->documentDate;
        $invoiceNumber = $data->invoiceNumber;
        $invoiceDate   = $data->invoiceDate;

        $orderModel   = new Default_Model_Order();
        $deleteOrder  = $orderModel->getOrderByOrderId($orderId);
        $affectedRows = $orderModel->deleteOrder($orderId, $cusCode, $documentDate, $invoiceNumber, $invoiceDate, $this->currentUser());

        $deleteResult = array(
          'data'   => $deleteOrder,
          'status' => 1,
        );
      } else {
        $deleteResult = array(
          'message' => 'Invalid request',
          'status'  => -1,
        );
        $this->setStatusCode(404);
      }
    } catch (Exception $err) {
      $deleteResult = array(
        'message' => 'Cannot delete order',
        'status'  => -1,
      );
      $this->setStatusCode(500);
    }

    echo json_encode($deleteResult);
  }
}
