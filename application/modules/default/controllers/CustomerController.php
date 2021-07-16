<?php

class CustomerController extends Zend_Controller_Action
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
        $this->view->pageTitle = 'Quản Lý Khách Hàng';
    }

    public function getAllAction()
    {
        if ($this->getRequest()->isGet()) {
            $customer = new Default_Model_Customer();
            $data = $customer->loadCustomer();
        } else {
            $data = array();
        }

        echo json_encode(array('data' => $data));
    }

    public function importAction()
    {
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $content = base64_decode($req->getParam('fileContent'));
            $fileNameWithExt = $req->getParam('fileName');

            require_once 'Utility.php';

            $username = $this->currentUser();
            $saveResult = Utility::saveFile($fileNameWithExt, $content, 'customer', $username);

            if ($saveResult) {
                require_once 'ExcelReaderWriter.php';

                $fileData = ExcelReaderWriter::read($saveResult['path'], $saveResult['ext']);

                $customer = new Default_Model_Customer();
                $importResult = $customer->importCustomer($fileData, $username);
            }
        } else {
            $importResult = array(
                'message' => 'Invalid request',
                'status' => -1,
            );
        }

        echo json_encode($importResult);
    }

    public function insertAction()
    {
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
    }

    public function updateAction()
    {
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
    }

    public function deleteAction()
    {
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
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function setRestResponse()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
    }

    /**
     * Get current username
     */
    private function currentUser()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $identity['username'];
    }
}
