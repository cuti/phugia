<?php
class Admin_LogController extends Zend_Controller_Action{
	
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

    }

    public function listlogdatatableAction(){
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $search = $this->getRequest()->getParam('search');

        $logtable = new Admin_Model_UserAdminLog();
        $datalog = $logtable->loadLogByFilter($search,$start,$length);

        $results = array(            
        );

        if($datalog != null && sizeof($datalog)){
            foreach($datalog as $log){                
                array_push($results,
                [
                    $log['page'],
                    $log['action'],
                    $log['useradmin_username'],
                    $log['ip'],
                    $log['createddate'] 
                ]); 
            }
        }


        $totalrecords = $logtable->countLogByFilter();

        $json_data = array(
            "draw"            => intval( $_REQUEST['draw'] ),
            "recordsTotal"    => intval(count($totalrecords)),
            "recordsFiltered" => intval(count($totalrecords)),
            "data"            => $results
        );
       //$cattraining = new Default_Model_CategoryTraining();
     
        echo json_encode($json_data);
        exit;
     }

    

}