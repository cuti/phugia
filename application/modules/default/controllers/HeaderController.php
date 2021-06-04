<?php

class HeaderController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->getRequest()->getBaseUrl();
    }

    public function indexAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();

        if ($identity['display_name']) {
            $this->view->display_name = $identity['display_name'];
        } else if ($identity['fullname']) {
            $this->view->display_name = $identity['fullname'];
        } else {
            $this->view->display_name = $identity['username'];
        }
    }
}
