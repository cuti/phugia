
<?php

class QuestionAnswerController extends Zend_Controller_Action
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
        
    }

    public function createAction(){

        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {           
            
                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                $modelAnswer = new Default_Model_Answers();
                $modelQuestion = new Default_Model_Question();

                if($this->view->parError == ''){                

                    $currentdate = new Zend_Date();                    
                    
                    $dataAnswer = array( 
                        'question_id' => $filter->filter($arrInput['question_id']),
                        'answer_content' => $filter->filter($arrInput['answer']),
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss')
                    );

                    $modelAnswer->insert($dataAnswer);


                    $updateQuestion = array(
                        'status' => '1'
                    );
                    $modelQuestion->update($updateQuestion, 'question_id = '. (int)($filter->filter($arrInput['question_id'])));                   
                } 

            }
        }

    }
}