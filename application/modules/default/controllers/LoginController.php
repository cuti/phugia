<?php

class LoginController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->getRequest()->getBaseUrl();
    }

    public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('');
        }

        $this->_helper->layout->setLayoutPath(APPLICATION_PATH . '/modules/default/views/scripts/login');
    }

    public function indexAction()
    {
        $this->view->pageTitle = 'Đăng Nhập';
        $req = $this->getRequest();

        if ($req->isPost()) {
            $username = $req->getParam('username', '');
            $password = $req->getParam('password', '');

            $staffModel = new Default_Model_Staff();
            $result = $staffModel->authenticate($username, $password);

            if (count($result) > 0) {
                Zend_Session::rememberMe(3600); // 1 hour
                Zend_Session::start();

                $data = $result[0];
                $identity = array(
                    'staff_id'      => $data['staff_id'],
                    'fullname'     => $data['staff_fullname'],
                    'display_name' => $data['staff_display_name'],
                    'username'     => $data['staff_username'],
                    'image'        => $data['staff_image'],
                    'department'   => $data['dep_name'],
                    'email'        => $data['staff_email'],
                );

                // Use session storage, with default namespace 'Zend_Auth'
                Zend_Auth::getInstance()->getStorage()->write($identity);
                $this->_redirect('');
            } else {
                $this->view->note = 'Tài khoản hoặc mật khẩu không đúng.';
            }
        }
    }

    // --------------- PRIVATE FUNCTIONS ---------------
}
