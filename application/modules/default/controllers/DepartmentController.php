<?php

class DepartmentController extends Zend_Controller_Action
{
    public function getAllAction()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {
            $department = new Default_Model_Department();
            $data = $department->loadDepartment();
        } else {
            $data = array();
        }

        echo json_encode(array('results' => $data));
        exit;
    }
}
