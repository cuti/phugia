<?php
class Admin_ContentpagesController extends Zend_Controller_Action{
	
	public function init(){ 
        $this->view->BaseUrl=$this->_request->getBaseUrl();
        $this->view->sBasePath = $this->_request->getBaseUrl()."/library/FCKeditor/" ;
    } 

    public function  preDispatch(){
 	
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
 
        $username= $this->identity->user_username;
        $password= $this->identity->user_password;
 
        $users2 = new Admin_Model_UserAdmin();  
        if ($users2->num($username, $password)>0) {                     
        
        }else{
              $this->_redirect('/admin/login');exit;
        }
    }

    public function indexAction(){ 
        $contentPagesModel = new Admin_Model_ContentPages();
        $data = $contentPagesModel->loadContentPage();
        $this->view->list =  $data;
    } 

    public function editAction(){ 
        $content_id = $this->getRequest()->getParam('content_id');

        $modelContentPages = new Admin_Model_ContentPages();
        $contentdata = $modelContentPages->loadContentPageById($content_id);
        $this->view->contentdata = $contentdata;
    } 

    public function updateAction(){ 
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();             

                $this->view->arrInput = $arrInput;

                if($this->view->parError == ''){ 
                    $contentModel = new Admin_Model_ContentPages();
                    $data = array(
                        'status' => $filter->filter($arrInput['status']),
                        'title'=> $filter->filter($arrInput['title']),
                        'description'=> $filter->filter($arrInput['description']) 
                       
                    );
                    //$this->view->data = $data;
                    $contentModel->update($data, 'content_id = '. (int)($filter->filter($arrInput['content_id'])));                   
                    
                    /*insert log action*/
                    $this->auth = Zend_Auth::getInstance();
                    $this->identity = $this->auth->getIdentity();
                    $currentdate = new Zend_Date();
                    $useradminlog = new Default_Model_UserAdminLog();
                    $datalog = array(
                        'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                        'useradmin_username' => $this->identity->user_username,
                        'action' => 'Cập nhật nội dung',
                        'page' => $this->_request->getControllerName(),
                        'useradmin_id' => $this->identity->user_id,
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'access_object' => $filter->filter($arrInput['content_id'])
                    );
                    $useradminlog->insert($datalog);
                }        
            }
        }    

    } 
}