<?php

require_once 'Utility.php';

class ForgotPasswordController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->getRequest()->getBaseUrl();
    }

    public function preDispatch()
    {
        $this->_helper->layout->setLayoutPath(APPLICATION_PATH . '/modules/default/views/scripts/forgot-password');

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->setRestResponse();
        }
    }

    public function indexAction()
    {
    }

    /**
     * Reset password for a user.
     */
    public function rpAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            try {
                $username = $req->getParam('username', '');
                $userInfo = $this->getUserInfoByUsername($username);

                if ($userInfo) {
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
                } else {
                    // User không tồn tại, nhưng vẫn trả kết quả OK, tránh tình trạng dò user
                    $result = array(
                        'data' => 'OK',
                        'status' => 1,
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
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function setRestResponse()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
    }

    private function getUserInfoByUsername($username)
    {
        $userModel = new Admin_Model_User();
        $userObj = $userModel->getUserByUsername($username);

        if ($userObj) {
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
        } else {
            return null;
        }
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
