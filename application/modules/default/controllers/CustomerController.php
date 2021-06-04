<?php

require_once 'ExcelReaderWriter.php';

class CustomerController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->getRequest()->getBaseUrl();
    }

    public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/login');
            exit;
        }
    }

    public function indexAction()
    {
        $this->view->pageTitle = 'Quản Lý Khách Hàng';
    }

    public function getAllAction()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {
            $customer = new Default_Model_Customer();
            $data = $customer->loadCustomer();
        } else {
            $data = array();
        }

        echo json_encode(array('data' => $data));
        exit;
    }

    public function importAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $content = base64_decode($req->getParam('fileContent'));
            $fileNameWithExt = $req->getParam('fileName');
            $fileName = substr($fileNameWithExt, 0, strrpos($fileNameWithExt, '.')) . rand(1000000000, 9999999999);
            $fileExt = substr($fileNameWithExt, strrpos($fileNameWithExt, '.'));
            $fileDir = ROOT_PATH . '/upload';

            if (!is_dir($fileDir)) {
                mkdir($fileDir);
            }

            $success = file_put_contents($fileDir . '/' . $fileName . $fileExt, $content);

            if ($success) {
                $fileData = ExcelReaderWriter::read($fileDir . '/' . $fileName . $fileExt, $fileExt);

                $customer = new Default_Model_Customer();
                $result = $customer->importCustomer($fileData, $this->currentUser());
            }
        } else {
            $result = array(
                'message' => 'Invalid request',
                'status' => -1,
            );
        }

        echo json_encode($result);
        exit;
    }

    public function insertAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $body = $req->getRawBody();
            $data = json_decode($body);
            $customer = json_decode(json_encode($data->customer), true);
            $cusTypes = $data->customerTypes;

            $customerModel = new Default_Model_Customer();
            $result = $customerModel->insertCustomer($customer, $cusTypes, $this->currentUser());

            if ($result === 'cus_code') {
                $result = array(
                    'message' => 'CUS_CODE_DUP',
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
            $customer = json_decode(json_encode($data->customer), true);
            $cusId = $data->cusId;
            $cusTypes = $data->customerTypes;

            $customerModel = new Default_Model_Customer();
            $result = $customerModel->updateCustomer($cusId, $customer, $cusTypes, $this->currentUser());

            if ($result === 'cus_code') {
                $result = array(
                    'message' => 'CUS_CODE_DUP',
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
                $cusId = $data->cusId;
                $cusCode = $data->cusCode;
                $cusName = $data->cusName;

                $customerModel = new Default_Model_Customer();
                $result = $customerModel->deleteCustomer($cusId, $cusCode, $cusName, $this->currentUser());

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

    // --------------- PRIVATE FUNCTIONS ---------------

    /**
     * Get current username
     */
    private function currentUser()
    {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        return $identity->user_username;
    }
}
