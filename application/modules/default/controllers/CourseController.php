<?php
// Customers controller 
class CourseController extends Zend_Controller_Action
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
        $cattraining = new Default_Model_CategoryTraining();
        $this->view->categories = $cattraining->loadCategoryTraining();

        $course = new Default_Model_Course();
        $this->view->courses = $course->loadCourse();
         
     }

     public function detailAction(){   
        $this->_helper->layout('homelayout')->disableLayout();
        $course = new Default_Model_Course();   
        $course_id = $this->getRequest()->getParam('course_id');
        $data = $course->loadCourseById($course_id);
        $this->view->coursedata = $data;

    }

     public function updateAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if($this->view->parError == ''){ 
                    $lawyer = new Default_Model_Course();
                    $data = array(
                        'course_status'=> $filter->filter($arrInput['course_status'])
                                           
                    );
                    //$this->view->data = $data;
                    $lawyer->update($data, 'course_id = '. (int)($filter->filter($arrInput['course_id'])));                   
                }        
            }
        }    

    }

      /*create new customer*/
    public function createAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                
                    $arrInput = $this->_request->getParams();
                    $this->view->arrInput = $arrInput;

                    //    /* covert date startdate*/
                    // $startdate = $filter->filter($arrInput['course_startdate']);
                    // $date_startdate = str_replace('/', '-', $startdate);
                    // $final_startdate =  date('Y-m-d', strtotime($date_startdate));

                    // /* covert date endate*/
                    // $course_enddate = $filter->filter($arrInput['course_enddate']);
                    // $date_course_enddate = str_replace('/', '-', $course_enddate);
                    // $final_enddate =  date('Y-m-d', strtotime($date_course_enddate));

                if($this->view->parError == ''){
                    $course = new Default_Model_Course();
                    $data = array(                       
                        'cat_train_id' => $filter->filter($arrInput['cat_train_id']),
                        'course_name' => $filter->filter($arrInput['course_name']),
                        'course_status' => '1',                        
                        'course_startdate' => $filter->filter($arrInput['course_startdate']),
                        'course_enddate' => $filter->filter($arrInput['course_enddate'])
                    );

                    $course->insert($data);                   
                    $this->view->data = $data;
                    //exit;
               }        
            }
        }    
    }
}
