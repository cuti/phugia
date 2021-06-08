<?php

require_once 'Utility.php';

class Admin_UserController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->getRequest()->getBaseUrl();
    }

    public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/admin/login');
            exit;
        }
    }

    public function indexAction()
    {
        $this->view->pageTitle = 'Quản Lý Người Dùng';
    }

    public function getAllAction()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {
            $user = new Admin_Model_User();
            $data = $user->loadUser();
        } else {
            $data = array();
        }

        echo json_encode(array('data' => $data));
        exit;
    }

    public function insertAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $body = $req->getRawBody();
            $data = json_decode($body);
            $user = json_decode(json_encode($data->user), true);

            $userModel = new Admin_Model_User();
            $result = $userModel->insertUser($user, $this->currentUser());

            if ($result === 'user_username') {
                $result = array(
                    'message' => 'UNAME_DUP',
                    'status' => 0,
                );
            } else {
                $result = array(
                    'data' => $result,
                    'status' => 1,
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

    public function updateAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $body = $req->getRawBody();
            $data = json_decode($body);
            $user = json_decode(json_encode($data->user), true);
            $userId = $data->userId;

            $userModel = new Admin_Model_User();
            $result = $userModel->updateUser($userId, $user, $this->currentUser());

            if ($result === 'user_username') {
                $result = array(
                    'message' => 'UNAME_DUP',
                    'status' => 0,
                );
            } else {
                $result = array(
                    'data' => $result,
                    'status' => 1,
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

    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            try {
                $body = $req->getRawBody();
                $data = json_decode($body);
                $userId = $data->usrId;
                $username = $data->uname;
                $email = $data->email;

                $user = new Admin_Model_User();
                $result = $user->deleteUser($userId, $username, $email, $this->currentUser());

                $result = array(
                    'data' => $result,
                    'status' => 1,
                );
            } catch (Exception $err) {
                $result = array(
                    'message' => 'Delete failed',
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

    public function changeStatusAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $body = $req->getRawBody();
            $data = json_decode($body);
            $userId = $data->usrId;

            $user = new Admin_Model_User();
            $result = $user->changeStatusUser($userId, $this->currentUser());

            $result = array(
                'data' => $result,
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

    /**
     * Reset password for a user.
     */
    public function rpAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            try {
                $body = $req->getRawBody();
                $data = json_decode($body);
                $username = $data->username;
                $userInfo = $this->getUserInfoByUsername($username);
                $password = Utility::generateSecret(MIN_PASS_LEN);

                $userModel = new Default_Model_User();
                $result = $userModel->changeUserPassword($userInfo['user_id'], $password);

                if ($result > 0) {
                    $mailBody = $this->prepareBody($userInfo['greetName'], $password);
                    $mail = $this->prepareMail($userInfo['email'], $userInfo['greetName'], $mailBody);

                    $config = array(
                        'auth' => 'login',
                        'username' => ADMIN_EMAIL,
                        'password' => base64_decode(ADMIN_EMAIL_SECRET),
                        'ssl' => SMTP_SSL,
                        'port' => SMTP_PORT,
                    );

                    $transport = new Zend_Mail_Transport_Smtp(SMTP_SERVER, $config);

                    $mail->send($transport);

                    $result = array(
                        'data' => 'OK',
                        'status' => 1,
                    );
                } else {
                    $result = array(
                        'message' => 'RESET_FAIL_1',
                        'status' => 0,
                    );
                }
            } catch (Exception $err) {
                $result = array(
                    'message' => 'RESET_FAIL_2',
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

    // --------------- PRIVATE FUNCTIONS ---------------

    /**
     * Get current username
     */
    private function currentUser()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $identity['username'];
    }

    private function getUserInfoByUsername($username)
    {
        $userModel = new Admin_Model_User();
        $userObj = $userModel->getUserByUsername($username);

        if ($userObj['user_display_name']) {
            $greetName = $userObj['user_display_name'];
        } else if ($userObj['user_fullname']) {
            $greetName = $userObj['user_fullname'];
        } else {
            $greetName = $username;
        }

        return array(
            'user_id'   => $userObj['user_id'],
            'greetName' => $greetName,
            'email'     => $userObj['user_email'],
        );
    }

    private function prepareBody($greetName, $password)
    {
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/mail/');
        $html->assign('name', $greetName);
        $html->assign('password', $password);

        return $html->render('_new-pass.phtml');
    }

    private function prepareMail($email, $greetName, $mailBody)
    {
        $mail = new Zend_Mail('UTF-8');
        $mail->setFrom(ADMIN_EMAIL, 'Post Master');
        $mail->addTo($email, $email);
        $mail->setSubject('Phú Gia CCRM - Thiết lập lại mật khẩu');
        $mail->setBodyHtml($mailBody, 'UTF-8');

        return $mail;
    }
}
