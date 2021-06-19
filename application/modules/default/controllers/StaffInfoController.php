<?php

class StaffInfoController extends Zend_Controller_Action
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
        $this->view->pageTitle = 'Thông Tin Nhân Viên';
        $this->view->data = $this->getUserInfo();
    }

    public function cpAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $staffInfo = $this->getUserInfo();
            $staffId = $staffInfo['staff_id'];
            $userName = $staffInfo['username'];
            $oldPass = $req->getParam('oldpass', '');
            $newPass = $req->getParam('newpass', '');
            $staffModel = new Default_Model_Staff();

            if ($staffModel->validate($userName, MD5($oldPass))) {
                $affectedCount = $staffModel->changeUserPassword($staffId, $newPass);

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
            $staffInfo = $this->getUserInfo();
            $staffId = $staffInfo['staff_id'];

            $fullName = $req->getParam('fullName', '');
            $displayName = $req->getParam('displayName', '');
            $email = $req->getParam('email', '');

            $data = array(
                'staff_fullname' => $fullName,
                'staff_display_name' => $displayName,
                'staff_email' => $email,
            );

            $staffModel = new Default_Model_Staff();
            $affectedCount = $staffModel->updateStaffInfo($staffId, $data);

            $staffInfo['fullname'] = $fullName;
            $staffInfo['display_name'] = $displayName;
            $staffInfo['email'] = $email;

            $this->setUserInfo($staffInfo);

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
