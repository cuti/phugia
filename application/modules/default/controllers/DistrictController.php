<?php

class DistrictController extends Zend_Controller_Action
{
    public function getAllAction()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {
            $district = new Default_Model_District();
            $data = $district->loadDistrict();
        } else {
            $data = array();
        }

        echo json_encode(array('results' => $data));
        exit;
    }
}
