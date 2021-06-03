<?php

class UserInfoController extends Zend_Controller_Action
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

            if ($users2->num($username, $password) === 0) {
                $this->_redirect('/login');
                exit;
            }
        } else {
            $this->_redirect('/login');
            exit;
        }
    }

    public function indexAction()
    {
        $this->view->pageTitle = 'Quản Lý Khách Hàng';
        $this->view->data = $this->getUserInfo();
    }

    public function cpAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $userInfo = $this->getUserInfo();
            $userId = $userInfo['user_id'];
            $userName = $userInfo['user_username'];
            $oldPass = $req->getParam('oldpass', '');
            $newPass = $req->getParam('newpass', '');
            $user = new Default_Model_User();

            if ($user->num($userName, MD5($oldPass)) > 0) {
                $affectedCount = $user->changeUserPassword($userId, $newPass);

                $result = array(
                    'data' => $affectedCount,
                    'status' => 1,
                );
            } else {
                $result = array(
                    'message' => 'Mật khẩu cũ không chính xác',
                    'status' => 0,
                );
            }
        } else {
            $result = array(
                'message' => 'Invalid request',
                'status' => 0,
            );
        }

        echo json_encode($result);
        exit;
    }

    public function ciAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $userInfo = $this->getUserInfo();
            $userId = $userInfo['user_id'];

            $fullName = $req->getParam('fullName', '');
            $displayName = $req->getParam('displayName', '');

            $data = array(
                'user_fullname' => $fullName,
                'user_display_name' => $displayName,
            );

            $user = new Default_Model_User();
            $affectedCount = $user->updateUserInfo($userId, $data);

            $result = array(
                'data' => $affectedCount,
                'status' => 1,
            );
        } else {
            $result = array(
                'message' => 'Invalid request',
                'status' => 0,
            );
        }

        echo json_encode($result);
        exit;
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    /**
     * Get current user information.
     */
    private function getUserInfo()
    {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        return json_decode(json_encode($identity), true);
    }
}
