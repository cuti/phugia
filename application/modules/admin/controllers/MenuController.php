<?php

class Admin_MenuController extends Zend_Controller_Action
{
    public function init()
    {
    }

    public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            if ($this->getRequest()->isXmlHttpRequest()) {
                echo json_encode(array('message' => 'SESSION_END'));
                exit;
            } else {
                $this->_redirect('/admin/login');
            }
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->setRestResponse();
        }
    }

    public function getPermissionTreeAction()
    {
        if ($this->getRequest()->isGet()) {
            $menuModel = new Default_Model_Menu();
            $data = $menuModel->getPermissionTree();
        } else {
            $data = array();
        }

        echo json_encode($data);
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function setRestResponse()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
    }
}
