<?php
// Customers controller 
class CustomerSupportController extends Zend_Controller_Action
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

     public function listAction(){
        $this->_helper->layout('homelayout')->disableLayout();
        $customerSupport = new Default_Model_CustomerSupport();  
        $cus_id = $this->getRequest()->getParam('cus_id'); 
        $data = $customerSupport->loadCustomerSupportByCusId($cus_id);
        $this->view->customersupports =  $data; 
    }

    public function indexAction(){

    }

    public function listcustomersupportdatatableAction(){

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
  
        $startdate = $this->getRequest()->getParam('startdate');
        $enddate = $this->getRequest()->getParam('enddate');

        $results = array(            
        );

        //offline
        $model = new Default_Model_CustomerSupport();
        
        $dateEmpty = '1900-01-01 00:00:00';
        $index = 0;         
        $data = $model->loadCustomerSupportByFilter($start, $length,$startdate,$enddate);
        if($data != null && sizeof($data)){            
            foreach($data as $pay){ 
                $index += 1;
                array_push($results,[
                    $index,
                    $pay['cus_firstname'].' '.$pay['cus_lastname'],
                    $pay['cus_identity_card'],                
                    date("d/m/Y", strtotime($pay['cus_birthday'])),
                    $pay['cus_lawyer_number'],
                    ($pay['cus_date_lawyer_number'] != null && $pay['cus_date_lawyer_number'] != '' && 
                    $pay['cus_date_lawyer_number'] != $dateEmpty) ? date('d/m/Y', strtotime($pay['cus_date_lawyer_number'])) : '',
                    $pay['cus_address_resident'],
                    $pay['cus_address_resident_now'],
                    $pay['hours'],                   
                    $pay['year'],
                    $pay['reason']                  
                ]);                 
            }
        }
        
        
        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($results)),
            "recordsFiltered" => intval(count($results)),
            "data"            => $results
        );
        
        echo json_encode($json_data);
        exit;
    }

    // public function listdataAction(){
    //     $this->_helper->layout('homelayout')->disableLayout();
    //     $customerreward = new Default_Model_CustomersRewardDiscipline();  
    //     $cus_id = $this->getRequest()->getParam('cus_id'); 
    //     $data = $customerreward->loadCustomersRewardByCusId($cus_id);
        
    //     array_walk ( $data, function (&$key) {             
    //         if($key["type"] == "Kỷ luật"){
    //             $key["discipline_date"] = date('d/m/Y',strtotime($key["discipline_date"]));
    //         }else{
    //             $key["reward_date"] = date('d/m/Y',strtotime($key["reward_date"]));
    //         }
    //     } );
    //     echo json_encode($data);
    //      exit; 
    // }

    // public function detailAction(){
    //     $this->_helper->layout('homelayout')->disableLayout();
    //     $id = $this->getRequest()->getParam('id');
        
    //     $model = new Default_Model_CustomersRewardDiscipline();
    //     $this->view->detailReward = $model->loadCustomersRewardById($id);
    // }

    // public function updateAction(){
    //     $this->_helper->layout('layout')->disableLayout();

    //     $filter = new Zend_Filter();
    //     if ($this->getRequest()->isXmlHttpRequest()) {
    //         if ($this->getRequest()->isPost()) {

    //             $arrInput = $this->_request->getParams();
    //             $this->view->arrInput = $arrInput;

    //              if($this->view->parError == ''){
    //                 $currentdate = new Zend_Date();
    //                 $model = new Default_Model_CustomersRewardDiscipline();
                   
    //                 if($arrInput['cus_reward_discipline'] == 'khenthuong'){
    //                     $data = array(                           
    //                         'reward_reason' => $arrInput['cus_reason'],                            
    //                         'reward_date' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
    //                         'type' => 'Khen thưởng'
    //                     );
    //                     $model->update($data, 'id = '. (int)($filter->filter($arrInput['id'])));
    //                 }else{
    //                     $data = array(                          
    //                         'discipline_reason' => $arrInput['cus_reason'],                           
    //                         'discipline_date' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
    //                         'type' => 'Kỷ luật'
    //                     );
    //                     $model->update($data, 'id = '. (int)($filter->filter($arrInput['id'])));                   
    //                 }                 
    //             }

    //         }
    //     }
    // }
    
}
