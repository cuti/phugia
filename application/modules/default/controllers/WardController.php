<?php

class WardController extends Zend_Controller_Action
{
    public function getAllAction()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {
            $ward = new Default_Model_Ward();
            $data = $ward->loadWard();
        } else {
            $data = array();
        }

        echo json_encode(array('results' => $data));
        exit;
    }
}
