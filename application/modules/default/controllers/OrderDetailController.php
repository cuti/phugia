<?php

require_once 'MyControllerAction.php';

class OrderDetailController extends MyControllerAction {
  public function getByOrderAction() {
    $this->setRestResponse();

    try {
      $req = $this->getRequest();

      if ($req->isXmlHttpRequest() && $req->isGet()) {
        $orderId = $req->getParam('orderId');

        $orderDetail = new Default_Model_OrderDetail();
        $data = $orderDetail->getOrderDetailByOrderId($orderId);

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
}
