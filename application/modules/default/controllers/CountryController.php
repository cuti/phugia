<?php

class CountryController extends Zend_Controller_Action
{
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

    public function getAllAction()
    {
        if ($this->getRequest()->isGet()) {
            $country = new Default_Model_Country();
            $data = $country->loadCountry();
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
