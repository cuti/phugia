<?php

class UserInfoController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->getRequest()->getBaseUrl();
    }

    public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            if ($this->getRequest()->isXmlHttpRequest()) {
                echo json_encode(array('message' => 'SESSION_END'));
                exit;
            } else {
                $this->_redirect('/login');
            }
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->setRestResponse();
        }
    }

    public function indexAction()
    {
        $this->view->pageTitle = 'Thông Tin Người Dùng';
        $this->view->data = $this->getUserInfo();
    }

    public function cpAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $userInfo = $this->getUserInfo();
            $userId = $userInfo['user_id'];
            $userName = $userInfo['username'];
            $oldPass = $req->getParam('oldpass', '');
            $newPass = $req->getParam('newpass', '');
            $user = new Default_Model_User();

            if ($user->validate($userName, MD5($oldPass))) {
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
    }

    public function ciAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $userInfo = $this->getUserInfo();
            $userId = $userInfo['user_id'];

            $fullName = $req->getParam('fullName', '');
            $displayName = $req->getParam('displayName', '');
            $email = $req->getParam('email', '');

            $data = array(
                'user_fullname' => $fullName,
                'user_display_name' => $displayName,
                'user_email' => $email,
            );

            $user = new Default_Model_User();
            $affectedCount = $user->updateUserInfo($userId, $data);

            $userInfo['fullname'] = $fullName;
            $userInfo['display_name'] = $displayName;
            $userInfo['email'] = $email;

            $this->setUserInfo($userInfo);

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
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function setRestResponse()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
    }

    /**
     * Get current user information.
     */
    private function getUserInfo()
    {
        return Zend_Auth::getInstance()->getIdentity();
    }

    /**
     * Set current user information.
     */
    private function setUserInfo($data)
    {
        Zend_Auth::getInstance()->getStorage()->write($data);
    }
}
