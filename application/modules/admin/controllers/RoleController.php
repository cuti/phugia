<?php

class Admin_RoleController extends Zend_Controller_Action
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
        $this->view->pageTitle = 'Quản Lý Nhóm Người Dùng';
    }

    public function getAllAction()
    {
        if ($this->getRequest()->isGet()) {
            $roleModel = new Admin_Model_Role();
            $data = $roleModel->loadRole();
        } else {
            $data = array();
        }

        echo json_encode(array('data' => $data));
    }

    public function getListAction()
    {
        if ($this->getRequest()->isGet()) {
            $roleModel = new Admin_Model_Role();
            $data = $roleModel->loadRoleShort();
        } else {
            $data = array();
        }

        echo json_encode(array('data' => $data));
    }

    public function insertAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            try {
                $body = $req->getRawBody();
                $data = json_decode($body);
                $roleObj = json_decode(json_encode($data->role), true);
                $roleMenu = json_decode(json_encode($data->role_menu), true);

                try {
                    $roleModel = new Admin_Model_Role();
                    $roleObj['role_id'] = $roleModel->insertRole($roleObj);
                    $roleModel->insertRoleMenuAction($roleObj['role_id'], $roleMenu);

                    $result = array(
                        'data' => $roleObj,
                        'status' => 1,
                    );
                } catch (Exception $err2) {
                    if ($err2->getMessage() === 'role_name') {
                        $result = array(
                            'message' => 'NAME_DUP',
                            'status' => 0,
                        );
                    } else {
                        $result = array(
                            'message' => 'Internal error',
                            'status' => 0,
                        );
                    }
                }
            } catch (Exception $err1) {
                $result = array(
                    'message' => 'Invalid request',
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

    public function updateAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            try {
                $body = $req->getRawBody();
                $data = json_decode($body);
                $roleObj = json_decode(json_encode($data->role), true);
                $roleMenu = json_decode(json_encode($data->role_menu), true);

                try {
                    $roleModel = new Admin_Model_Role();
                    $roleModel->updateRole($roleObj);
                    $roleModel->insertRoleMenuAction($roleObj['role_id'], $roleMenu);

                    $result = array(
                        'data' => $roleObj,
                        'status' => 1,
                    );
                } catch (Exception $err2) {
                    if ($err2->getMessage() === 'role_name') {
                        $result = array(
                            'message' => 'NAME_DUP',
                            'status' => 0,
                        );
                    } else {
                        $result = array(
                            'message' => 'Internal error',
                            'status' => 0,
                        );
                    }
                }
            } catch (Exception $err1) {
                $result = array(
                    'message' => 'Invalid request',
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

    public function deleteAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            try {
                $body = $req->getRawBody();
                $data = json_decode($body);
                $roleObj = json_decode(json_encode($data->role), true);
                $roleId = $roleObj['role_id'];
                $roleName = $roleObj['role_name'];

                try {
                    $roleModel = new Admin_Model_Role();
                    $roleModel->deleteRole($roleId, $roleName);

                    $result = array(
                        'data' => $roleObj,
                        'status' => 1,
                    );
                } catch (Exception $err2) {
                    $result = array(
                        'message' => 'Internal error',
                        'status' => 0,
                    );
                }
            } catch (Exception $err1) {
                $result = array(
                    'message' => 'Invalid request',
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
            try {
                $body = $req->getRawBody();
                $data = json_decode($body);
                $roleObj = json_decode(json_encode($data->role), true);
                $roleId = $roleObj['role_id'];
                $roleActive = $roleObj['role_active'];

                try {
                    $roleModel = new Admin_Model_Role();
                    $roleModel->changeStatusRole($roleId, $roleActive);

                    $result = array(
                        'data' => $roleObj,
                        'status' => 1,
                    );
                } catch (Exception $err2) {
                    $result = array(
                        'message' => 'Internal error',
                        'status' => 0,
                    );
                }
            } catch (Exception $err1) {
                $result = array(
                    'message' => 'Invalid request',
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

    public function menuActionsAction()
    {
        $req = $this->getRequest();

        if ($req->isGet()) {
            $roleId = $req->getQuery('roleId');
            $roleModel = new Admin_Model_Role();
            $data = $roleModel->loadMenuActionsByRole($roleId);
        } else {
            $data = array();
        }

        echo json_encode(array('data' => $data));
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function setRestResponse()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
    }
}
