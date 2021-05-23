<?php

class CityController extends Zend_Controller_Action
{
    public function getAllAction()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {
            $city = new Default_Model_City();
            $data = $city->loadCity();
        } else {
            $data = array();
        }

        echo json_encode(array('results' => $data));
        exit;
    }
}
