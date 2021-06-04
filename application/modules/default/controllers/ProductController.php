<?php

require_once 'ExcelReaderWriter.php';

class ProductController extends Zend_Controller_Action
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
        $this->view->pageTitle = 'Quản Lý Vật Tư, Hàng Hóa';
    }

    public function getAllAction()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {
            $product = new Default_Model_Product();
            $data = $product->loadProduct();
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

                $product = new Default_Model_Product();
                $result = $product->importProduct($fileData, $this->currentUser());
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
            $product = json_decode(json_encode($data->product), true);

            $productModel = new Default_Model_Product();
            $result = $productModel->insertProduct($product, $this->currentUser());

            if ($result === 'product_code') {
                $result = array(
                    'message' => 'PRD_CODE_DUP',
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
            $product = json_decode(json_encode($data->product), true);
            $prdId = $data->prdId;

            $productModel = new Default_Model_Product();
            $result = $productModel->updateProduct($prdId, $product, $this->currentUser());

            if ($result === 'product_code') {
                $result = array(
                    'message' => 'PRD_CODE_DUP',
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
                $prdId = $data->prdId;
                $prdCode = $data->prdCode;
                $prdName = $data->prdName;

                $productModel = new Default_Model_Product();
                $result = $productModel->deleteProduct($prdId, $prdCode, $prdName, $this->currentUser());

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
