<?php

class LogoutController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();

        if (strpos($_SERVER['REQUEST_URI'], $this->_request->getBaseUrl() . '/admin') > 0) {
            $this->_redirect('/admin/login');
        } else {
            $this->_redirect('/login');
        }
    }
}
