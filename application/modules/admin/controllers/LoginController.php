<?php

class Admin_LoginController extends Zend_Controller_Action
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

            if ($users2->num($username, $password) > 0) {
                $this->_redirect('/admin');
                exit;
            }
        }

        $this->_helper->layout->setLayoutPath(APPLICATION_PATH . '/modules/admin/views/scripts/login');
    }

    public function indexAction()
    {
        Zend_Session::rememberMe(7200); // 1 hour
        Zend_Session::start();

        $this->view->pageTitle = 'Đăng Nhập Quản Trị';

        if ($this->_request->isPost()) {
            $username = $this->_request->getParam('username', '');
            $password = MD5($this->_request->getParam('password', ''));
            $users = new Admin_Model_User();
            $auth = Zend_Auth::getInstance();
            $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(), 'user');
            $authAdapter->setIdentityColumn('user_username')->setCredentialColumn('user_password');
            $authAdapter->setIdentity($username)->setCredential($password);
            $result = $auth->authenticate($authAdapter);

            if ($result->isValid()) {
                $data = $authAdapter->getResultRowObject();
                $auth->getStorage()->write($data);
                $_SESSION['login'] = "good";
                $_SESSION['config'] = $this->view->BaseUrl;
                $_SESSION['username'] = $username;
                $_SESSION['display_name'] = $data->user_display_name;
                $this->_redirect('/admin');
            } else {
                $this->view->note = 'Tài khoản hoặc mật khẩu không đúng.';
            }
        }
    }
}
