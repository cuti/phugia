<?php

class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->_request->getBaseUrl();
    }

    public function preDispatch()
    {
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();

        if ($this->identity) {
            $username = $this->identity->user_username;
            $password = $this->identity->user_password;

            $users2 = new Default_Model_User();

            if ($users2->num($username, $password) > 0) {

            } else {
                $this->_redirect('login');
                exit;
            }
        } else {
            $this->_redirect('login');
            exit;
        }
    }

    public function indexAction()
    {
        $this->view->pageTitle = 'Trang Chủ';
    }
}
