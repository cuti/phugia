<?php

class Admin_LogoutController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        Zend_Session::destroy();
        $this->_redirect($this->_request->getBaseUrl() . '/admin/login');
    }
}
