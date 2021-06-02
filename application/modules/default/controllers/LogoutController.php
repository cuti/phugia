<?php

class LogoutController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        Zend_Session::destroy();

        if (strpos($_SERVER['REQUEST_URI'], 'admin') > 0) {
            $module = '/admin';
        } else {
            $module = '';
        }

        $this->_redirect($module . '/login');
    }
}
