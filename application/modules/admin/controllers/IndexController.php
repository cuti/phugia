<?php

class Admin_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->getRequest()->getBaseUrl();
    }

    public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/admin/login');
            exit;
        }
    }

    public function indexAction()
    {
        $this->view->pageTitle = 'Trang Chủ';
    }
}
