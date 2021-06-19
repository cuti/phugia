<?php

class Admin_StaffController extends Zend_Controller_Action
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
                $this->_redirect('/admin/login');
            }
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->setRestResponse();
        }
    }

    public function indexAction()
    {
        $this->view->pageTitle = 'Quản Lý Nhân Viên';
    }

    public function getAllAction()
    {
        if ($this->getRequest()->isGet()) {
            $staffModel = new Admin_Model_Staff();
            $data = $staffModel->loadStaff();
        } else {
            $data = array();
        }

        echo json_encode(array('data' => $data));
    }

    public function insertAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $body = $req->getRawBody();
            $data = json_decode($body);
            $staff = json_decode(json_encode($data->staff), true);
            $roleId = $data->roleId;

            $staffModel = new Admin_Model_Staff();
            $result = $staffModel->insertStaff($staff, $roleId);

            if ($result === 'staff_username') {
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
    }

    public function updateAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $body = $req->getRawBody();
            $data = json_decode($body);
            $staff = json_decode(json_encode($data->staff), true);
            $staffId = $data->staffId;
            $roleId = $data->roleId;

            $staffModel = new Admin_Model_Staff();
            $result = $staffModel->updateStaff($staffId, $staff, $roleId);

            if ($result === 'staff_username') {
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
    }

    public function deleteAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            try {
                $body = $req->getRawBody();
                $data = json_decode($body);
                $staffId = $data->staffId;
                $username = $data->username;
                $email = $data->email;

                $staffModel = new Admin_Model_Staff();
                $result = $staffModel->deleteStaff($staffId, $username, $email);

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
    }

    public function changeStatusAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $body = $req->getRawBody();
            $data = json_decode($body);
            $staffId = $data->staffId;

            $staffModel = new Admin_Model_Staff();
            $result = $staffModel->changeStaffStatus($staffId);

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
    }

    /**
     * Reset password for a staff.
     */
    public function rpAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            try {
                $body = $req->getRawBody();
                $data = json_decode($body);
                $username = $data->username;
                $staffInfo = $this->getStaffInfoByUsername($username);

                require_once 'Utility.php';

                $password = Utility::generateSecret(MIN_PASS_LEN);

                $staffModel = new Default_Model_Staff();
                $result = $staffModel->changeUserPassword($staffInfo['staff_id'], $password);

                if ($result > 0) {
                    $mailBody = $this->prepareBody($staffInfo['greetName'], $password);
                    $mail = $this->prepareMail($staffInfo['email'], $staffInfo['greetName'], $mailBody);

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
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function setRestResponse()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
    }

    private function getStaffInfoByUsername($username)
    {
        $staffModel = new Admin_Model_Staff();
        $staffObj = $staffModel->getStaffByUsername($username);

        if ($staffObj['staff_display_name']) {
            $greetName = $staffObj['staff_display_name'];
        } else if ($staffObj['staff_fullname']) {
            $greetName = $staffObj['staff_fullname'];
        } else {
            $greetName = $username;
        }

        return array(
            'staff_id'   => $staffObj['staff_id'],
            'greetName' => $greetName,
            'email'     => $staffObj['staff_email'],
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
