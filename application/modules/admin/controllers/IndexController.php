<?php

class Admin_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->_request->getBaseUrl();
    }

    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();

        if ($identity) {
            $username = $identity->user_username;
            $password = $identity->user_password;

            $users2 = new Admin_Model_User();

            if ($users2->num($username, $password) === 0) {
                $this->_redirect($this->view->BaseUrl . '/admin/login');
                exit;
            }
        } else {
            $this->_redirect($this->view->BaseUrl . '/admin/login');
            exit;
        }
    }

    public function indexAction()
    {
        $this->view->pageTitle = 'Trang Chủ';
    }
}
