<?php
// Login controller 
class Admin_CategoryfeeController extends Zend_Controller_Action
{
    public function init(){ 
        $this->view->BaseUrl=$this->_request->getBaseUrl();
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
          $model = new Admin_Model_CategoryFee();
          $data = $model->loadCategoryFees();
          $this->view->data =  $data;
    }

    public function detailAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $categoryModel = new Default_Model_CategoryFee();   
        $category_fee_id = $this->getRequest()->getParam('category_fee_id');
        $data = $categoryModel->loadCategoryFeeById($category_fee_id);
        $this->view->categoryfee =  $data; 
    }

    public function updateAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if($this->view->parError == ''){ 
                    $payment = new Default_Model_CategoryFee();
                    $data = array(                 
                        'status'=> $filter->filter($arrInput['status']),
                        'name'=> $filter->filter($arrInput['name']),  
                        'year'=> $filter->filter($arrInput['year']),
                        'baseondocument'=> $filter->filter($arrInput['baseondocument'])                        
                    );
                    $payment->update($data, 'category_fee_id = '. (int)($filter->filter($arrInput['category_fee_id'])));                   
                    
                     /*insert log action*/
                    //  $this->auth = Zend_Auth::getInstance();
                    //  $this->identity = $this->auth->getIdentity();
                    //  $currentdate = new Zend_Date();
                    //  $useradminlog = new Default_Model_UserAdminLog();
                    //  $datalog = array(
                    //      'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                    //      'useradmin_username' => $this->identity->user_username,
                    //      'action' => 'Cập nhật biên nhận phí thành viên',
                    //      'page' => $this->_request->getControllerName(),
                    //      'useradmin_id' => $this->identity->user_id,
                    //      'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                    //      'access_object' => $filter->filter($arrInput['payment_lawyer_off_id'])
                    //  );
                    //  $useradminlog->insert($datalog);
                
                
                }        
            }
        }    
    }
}