<?php

class CustomerTypeController extends Zend_Controller_Action
{
    public function getAllAction()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {
            $customerType = new Default_Model_CustomerType();
            $data = $customerType->loadCustomerType();
        } else {
            $data = array();
        }

        echo json_encode(array('results' => $data));
        exit;
    }
}
