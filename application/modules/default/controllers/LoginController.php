<?php

class LoginController extends Zend_Controller_Action
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
                $this->_redirect('');
                exit;
            }
        }

        $this->_helper->layout->setLayoutPath(APPLICATION_PATH . '/modules/default/views/scripts/login');
    }

    public function indexAction()
    {
        Zend_Session::rememberMe(7200); // 1 hour
        Zend_Session::start();

        $this->view->pageTitle = 'Đăng Nhập';

        if ($this->_request->isPost()) {
            $username = $this->_request->getParam('username', '');
            $password = MD5($this->_request->getParam('password', ''));
            $users = new Default_Model_User();
            $auth = Zend_Auth::getInstance();
            $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(), 'user');
            $authAdapter->setIdentityColumn('user_username')->setCredentialColumn('user_password');
            $authAdapter->setIdentity($username)->setCredential($password);
            $result = $auth->authenticate($authAdapter);

            if ($result->isValid()) {
                $data = $authAdapter->getResultRowObject();
                $data->user_department = $this->getUserDepartment($data->user_id);
                $auth->getStorage()->write($data);
                $_SESSION['login'] = "good";
                $_SESSION['config'] = $this->view->BaseUrl;
                $_SESSION['username'] = $username;
                $_SESSION['display_name'] = $data->user_display_name;
                $this->_redirect('');
            } else {
                $this->view->note = 'Tài khoản hoặc mật khẩu không đúng.';
            }
        }
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    /**
     * Get user's department.
     */
    private function getUserDepartment($userId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from('user', array())
            ->joinLeft(array('dep' => 'department'), '[user].user_department_id = dep.dep_id', array('dep_name'))
            ->where('[user].user_id = ?', $userId);
        $result = $db->fetchAll($select);

        if (count($result) === 0) {
            return '';
        } else {
            return $result[0]['dep_name'];
        }
    }
}
