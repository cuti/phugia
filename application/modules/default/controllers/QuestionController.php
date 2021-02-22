
<?php

class QuestionController extends Zend_Controller_Action
{
    public function init(){
        $this->view->BaseUrl=$this->_request->getBaseUrl();
    }

    public function  preDispatch(){
 	
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
 
        $username= $this->identity->user_username;
        $password= $this->identity->user_password;
 
        $users2 = new Default_Model_UserAdmin();  
        if ($users2->num($username, $password)>0) {                     
        
        }else{
              $this->_redirect('/default/login');exit;
        }
    }

    public function indexAction(){
        $modelQuestion = new Default_Model_Question();
        $data = $modelQuestion->loadQuestions();
        $this->view->questions = $data;
    }

    public function detailAction(){
        $this->_helper->layout('layout')->disableLayout();
        $modelQuestion = new Default_Model_Question();
        $id = $this->getRequest()->getParam('id');
        
        $data = $modelQuestion->loadQuestionById($id);
        $this->view->question = $data;
    }

    public function createAction(){
        
    }

}