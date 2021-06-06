<?php

require_once 'Utility.php';

class Admin_LoginController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->getRequest()->getBaseUrl();
    }

    public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/admin');
            exit;
        }

        $this->_helper->layout->setLayoutPath(APPLICATION_PATH . '/modules/admin/views/scripts/login');
    }

    public function indexAction()
    {
        $this->view->pageTitle = 'Đăng Nhập Quản Trị';
        $req = $this->getRequest();

        if ($req->isPost()) {
            $username = $req->getParam('username', '');
            $password = $req->getParam('password', '');

            $users = new Default_Model_User();
            $result = $users->authenticate($username, $password);

            if (count($result) > 0) {
                Zend_Session::rememberMe(3600); // 1 hour
                Zend_Session::start();

                $data = $result[0];
                $identity = array(
                    'user_id'      => $data['user_id'],
                    'fullname'     => $data['user_fullname'],
                    'display_name' => $data['user_display_name'],
                    'username'     => $data['user_username'],
                    'image'        => $data['user_image'],
                    'department'   => $data['dep_name'],
                    'email'        => $data['user_email'],
                );

                // Use session storage, with default namespace 'Zend_Auth'
                Zend_Auth::getInstance()->getStorage()->write($identity);
                $this->_redirect('/admin');
            } else {
                $this->view->note = 'Tài khoản hoặc mật khẩu không đúng.';
            }
        }
    }

    // --------------- PRIVATE FUNCTIONS ---------------
}
