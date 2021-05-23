<?php

class CountryController extends Zend_Controller_Action
{
    public function getAllAction()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {
            $country = new Default_Model_Country();
            $data = $country->loadCountry();
        } else {
            $data = array();
        }

        echo json_encode(array('results' => $data));
        exit;
    }
}
