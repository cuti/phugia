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
            exit;
        }

        $this->_helper->layout->setLayoutPath(APPLICATION_PATH . '/modules/default/views/scripts/login');
    }

    public function indexAction()
    {
        $this->view->pageTitle = 'Đăng Nhập';
        $req = $this->getRequest();

        if ($req->isPost()) {
            $username = $req->getParam('username', '');
            $password = MD5($req->getParam('password', ''));

            $users = new Default_Model_User();
            $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(), 'user');
            $authAdapter->setIdentityColumn('user_username')->setCredentialColumn('user_password');
            $authAdapter->setIdentity($username)->setCredential($password);

            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);

            if ($result->isValid()) {
                Zend_Session::rememberMe(3600); // 1 hour
                Zend_Session::start();

                $data = $authAdapter->getResultRowObject();
                $user_department = $this->getUserDepartment($data->user_id);
                $identity = array(
                    'user_id'      => $data->user_id,
                    'fullname'     => $data->user_fullname,
                    'display_name' => $data->user_display_name,
                    'username'     => $data->user_username,
                    'image'        => $data->user_image,
                    'department'   => $user_department,
                );

                // Use session storage, with default namespace 'Zend_Auth'
                $auth->getStorage()->write($identity);
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
        $result = $db->fetchRow($select);

        if ($result['dep_name'] === null) {
            return '';
        } else {
            return $result['dep_name'];
        }
    }
}
