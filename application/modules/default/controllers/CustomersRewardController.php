<?php
// Customers controller 
class CustomersRewardController extends Zend_Controller_Action
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

     // load list kỉ luật khen thưởng theo cus_id
     public function listAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $customerreward = new Default_Model_CustomersRewardDiscipline();  
        $cus_id = $this->getRequest()->getParam('cus_id'); 
        $data = $customerreward->loadCustomersRewardByCusId($cus_id);
        $this->view->customerrewards =  $data; 
    }

    // láy thông tin hiển thị trên mục lý lích
    public function listdataAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $customerreward = new Default_Model_CustomersRewardDiscipline();  
        $cus_id = $this->getRequest()->getParam('cus_id'); 
        $data = $customerreward->loadCustomersRewardByCusId($cus_id);
        
        array_walk ( $data, function (&$key) {             
            if($key["type"] == "Kỷ luật"){
                $key["discipline_date"] = ($key["discipline_date"] != null && $key["discipline_date"] != '' &&
                $key["discipline_date"] != '1900-01-01 00:00:00') ? date('d/m/Y',strtotime($key["discipline_date"])):'';
            }else{
                $key["reward_date"] = ($key["reward_date"] != null && $key["reward_date"] != '' &&
                $key["reward_date"] != '1900-01-01 00:00:00')  ? date('d/m/Y',strtotime($key["reward_date"])) : '';
            }
        } );
        echo json_encode($data);
         exit; 
    }

    public function detailAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $id = $this->getRequest()->getParam('id');
        
        $model = new Default_Model_CustomersRewardDiscipline();
        $this->view->detailReward = $model->loadCustomersRewardById($id);
    }

    public function updateAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                 if($this->view->parError == ''){
                    $currentdate = new Zend_Date();
                    $model = new Default_Model_CustomersRewardDiscipline();
                   
                    if($arrInput['cus_reward_discipline'] == 'khenthuong'){
                        $data = array(                           
                            'reward_reason' => $arrInput['cus_reason'],                            
                            'reward_date' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'type' => 'Khen thưởng'
                        );
                        $model->update($data, 'id = '. (int)($filter->filter($arrInput['id'])));
                    }else{
                        $data = array(                          
                            'discipline_reason' => $arrInput['cus_reason'],                           
                            'discipline_date' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'type' => 'Kỷ luật'
                        );
                        $model->update($data, 'id = '. (int)($filter->filter($arrInput['id'])));                   
                    }                 
                }

            }
        }
    }
    
}
