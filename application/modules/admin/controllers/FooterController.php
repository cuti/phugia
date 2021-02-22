<?php
class Admin_FooterController extends Zend_Controller_Action
{
    public function init(){
        $this->view->BaseUrl=$this->_request->getBaseUrl();
        //Zend_Loader::loadClass('Menusm');
    }

    public function indexAction()
    {
    }
}