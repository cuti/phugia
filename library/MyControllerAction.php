<?php

class MyControllerAction extends Zend_Controller_Action {
  public function init() {
    $this->view->BaseUrl = $this->getRequest()->getBaseUrl();
  }

  public function preDispatch() {
    if (!Zend_Auth::getInstance()->hasIdentity()) {
      if ($this->getRequest()->isXmlHttpRequest()) {
        echo json_encode(array('message' => 'SESSION_END'));
        exit;
      } else {
        if ($this->getRequest()->getModuleName() === 'admin') {
          $this->_redirect('/admin/login');
        } else {
          $this->_redirect('/login');
        }
      }
    }
  }

  // --------------- Protected Functions ---------------

  /**
   * Disable layout and view rendered, set content type to json.
   */
  protected function setRestResponse() {
    $this->_helper->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    $this->getResponse()->setHeader('Content-Type', 'application/json', true);
  }

  /**
   * Set http response code.
   *
   * @param int $code Response code.
   */
  protected function setStatusCode($code) {
    $this->getResponse()->setHttpResponseCode($code);
  }

  /**
   * Get current username
   */
  protected function currentUser() {
    $identity = Zend_Auth::getInstance()->getIdentity();
    return $identity['username'];
  }
}
