<?php
// Customers controller 
class CategoryTrainingController extends Zend_Controller_Action
{
    public function init(){
        $this->view->BaseUrl=$this->_request->getBaseUrl();
        $this->view->sBasePath = $this->_request->getBaseUrl()."/library/FCKeditor/" ;
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

         /*insert log action*/
         $this->auth = Zend_Auth::getInstance();
         $this->identity = $this->auth->getIdentity();
         $currentdate = new Zend_Date();
         $useradminlog = new Default_Model_UserAdminLog();
         $datalog = array(
             'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
             'useradmin_username' => $this->identity->user_username,
             'action' => 'Xem danh sách chủ đề bồi dưỡng',
             'page' => $this->_request->getControllerName(),
             'useradmin_id' => $this->identity->user_id,
             'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss')
         );
         $useradminlog->insert($datalog);   

    }

     public function detailAction(){   
        $this->_helper->layout('homelayout')->disableLayout();
        $cattraining = new Default_Model_CategoryTraining();   
        $cat_id = $this->getRequest()->getParam('cat_id');
        $data = $cattraining->loadCategoryTrainingByCatId($cat_id);
        $this->view->categorydata = $data;

         /*insert log action*/
         $this->auth = Zend_Auth::getInstance();
         $this->identity = $this->auth->getIdentity();
         $currentdate = new Zend_Date();
         $useradminlog = new Default_Model_UserAdminLog();
         $datalog = array(
             'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
             'useradmin_username' => $this->identity->user_username,
             'action' => 'Xem chi tiết chủ đề bồi dưỡng',
             'page' => $this->_request->getControllerName(),
             'useradmin_id' => $this->identity->user_id,
             'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
             'access_object' => $cat_id
         );
         $useradminlog->insert($datalog);

    }

    public function detailcatpageAction(){   
        //$this->_helper->layout('homelayout')->disableLayout();
        $cattraining = new Default_Model_CategoryTraining();   
        $cat_id = $this->getRequest()->getParam('cat_id');
        $data = $cattraining->loadCategoryTrainingByCatId($cat_id);
        $this->view->categorydata = $data;

         /*insert log action*/
         $this->auth = Zend_Auth::getInstance();
         $this->identity = $this->auth->getIdentity();
         $currentdate = new Zend_Date();
         $useradminlog = new Default_Model_UserAdminLog();
         $datalog = array(
             'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
             'useradmin_username' => $this->identity->user_username,
             'action' => 'Xem chi tiết chủ đề bồi dưỡng',
             'page' => $this->_request->getControllerName(),
             'useradmin_id' => $this->identity->user_id,
             'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
             'access_object' => $cat_id
         );
         $useradminlog->insert($datalog);

    }

    /*update information of customer*/
    public function updateAction(){
        $this->_helper->layout('layout')->disableLayout();
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
 
        $username= $this->identity->user_username;
        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;
                $date = new Zend_Date();

                $cat_train_fromdate = $filter->filter($arrInput['cat_train_fromdate']);
                    $cat_train_fromdate = str_replace('/', '-', $cat_train_fromdate);
                    $cat_train_fromdate =  date('Y-m-d', strtotime($cat_train_fromdate));

                if($this->view->parError == ''){ 
                    $lawyer = new Default_Model_CategoryTraining();
                    $data = array(
                        'cat_train_address' => $filter->filter($arrInput['cat_train_address']),
                        'cat_train_number'=> $filter->filter($arrInput['cat_train_number']),
                        'cat_train_name'=> $filter->filter($arrInput['cat_train_name']),  
                        'cat_train_active'=> $filter->filter($arrInput['cat_train_active']),
                        'updatedate' => $date->toString('YYYY-MM-dd HH:mm:ss'),
                        'updated_username' => $username,
                        'cat_train_quantity' => $filter->filter($arrInput['cat_train_hours']),
                        'cat_train_fromdate' =>  $cat_train_fromdate,  
                        'cat_trainer' => $filter->filter($arrInput['cat_trainer'])                
                    );
                    //$this->view->data = $data;
                    $lawyer->update($data, 'cat_train_id = '. (int)($filter->filter($arrInput['cat_train_id'])));                   
                    
                    /*insert log action*/
                    // $this->auth = Zend_Auth::getInstance();
                    // $this->identity = $this->auth->getIdentity();
                    $currentdate = new Zend_Date();
                    $useradminlog = new Default_Model_UserAdminLog();
                    $datalog = array(
                        'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                        'useradmin_username' => $this->identity->user_username,
                        'action' => 'Cập nhật chủ đề bồi dưỡng',
                        'page' => $this->_request->getControllerName(),
                        'useradmin_id' => $this->identity->user_id,
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'access_object' => $filter->filter($arrInput['cat_train_id'])
                    );
                    $useradminlog->insert($datalog);
                }        
            }
        }    

    }

      /*create new customer*/
    public function createAction(){
        $this->_helper->layout('layout')->disableLayout();
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
 
        $username= $this->identity->user_username;
        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
            
                $arrInput = $this->_request->getParams();
                
                $this->view->arrInput = $arrInput;

                $date = new Zend_Date();

                if($this->view->parError == ''){

                    $cat_train_fromdate = $filter->filter($arrInput['cat_train_fromdate']);
                    $cat_train_fromdate = str_replace('/', '-', $cat_train_fromdate);
                    $cat_train_fromdate =  date('Y-m-d', strtotime($cat_train_fromdate));

                    $cat = new Default_Model_CategoryTraining();
                    $data = array(                       
                        'cat_train_name' => $arrInput['cat_train_name'],
                        'cat_train_fromdate' =>  $cat_train_fromdate,
                        'cat_train_number' => $arrInput['cat_train_number'],
                        'cat_train_address' =>  $filter->filter($arrInput['cat_train_address']),
                        'createdate' => $date->toString('YYYY-MM-dd HH:mm:ss'),
                        'cat_trainer' => $filter->filter($arrInput['cat_trainer']),
                        'cat_train_active' => '1',
                        'cat_train_quantity' => $filter->filter($arrInput['cat_train_hours']),
                        'created_username' => $username
                        
                    );
                  
                    $catnew = $cat->insert($data);                   
                    $this->view->data = $data;

                    /*insert log action*/
                    // $this->auth = Zend_Auth::getInstance();
                    // $this->identity = $this->auth->getIdentity();
                    $currentdate = new Zend_Date();
                    $useradminlog = new Default_Model_UserAdminLog();
                    $datalog = array(
                        'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                        'useradmin_username' => $this->identity->user_username,
                        'action' => 'Tạo mới chủ đề bồi dưỡng',
                        'page' => $this->_request->getControllerName(),
                        'useradmin_id' => $this->identity->user_id,
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'access_object' => $catnew
                    );
                    $useradminlog->insert($datalog);
                    //exit;
               }        
            }
        }    
    }
}

//     public function exportreportexcelAction()
//     {
    	
//     	$this->_helper->layout()->disableLayout();
    	
      
// 	$this->view->pageTitle = 'Báo cáo';	
// 	// $id =  $this->_request->getParam('id',0);
			
// 	// Zend_Loader::loadClass('Ordersm');
// 	// Zend_Loader::loadClass('Provincem');
// 	// Zend_Loader::loadClass('Districtm');	
	
// 	// $this->province = new Provincem();
// 	// $this->district = new Districtm();
		
//     //   	$this->product = new Productm();
//     //   	$this->customers = new Customersm();
	
//     // 	$obj = new Buym();	
//     // 	$orders = new Ordersm();
    

				 							

// 	// 	$where = null;
// 	// 	$order= "id desc";
//     // 	$resulte = $orders->fetchAll($where,$order); 
    
//     $action = new Default_Model_Action();
//     $dataaction = $action->loadAction();
    


// 	$xls = new Excel_XML('UTF-8', true, 'Reports'); 
	
// 	$data = array ('Báo cáo');
// 	$xls->addRow($data);
// 	   $data = array ("");

// 	$xls->addRow($data);
	
	
// 	$data = array ('No.', 'Mã ','Tên', 'Ngày tạo', 'Trạng thái', 'Ngày cập nhật');
// 	$xls->addRow($data);   
// 	$i=1;      
// foreach($dataaction as $record){



// $data = array($i,$record['action_name'],$record['action_actname'], $record['action_createdate'],$record['action_status'],$record['action_updatedate']); 
//         	$xls->addRow($data); 
// $i++;}
// // generate file (constructor parameters are optional) 


// $xls->generateXML('Bao_cao');
// exit; 
	

//         }

//            /**
//          * Generate the excel file
//          * @param string $filename Name of excel file to generate (...xls)
//          */
//         public function generateXML ($filename = 'excel-export')
//         {
//                 // correct/validate filename
//                 $filename = preg_replace('/[^aA-zZ0-9\_\-]/', '', $filename);
    	
//                 // deliver header (as recommended in php manual)
//                 header("Content-Type: application/vnd.ms-excel; charset=" . $this->sEncoding);
//                 header("Content-Disposition: inline; filename=\"" . $filename . ".xls\"");

//                 // print out document to the browser
//                 // need to use stripslashes for the damn ">"
//                 echo stripslashes (sprintf($this->header, $this->sEncoding));
//                 echo "\n<Worksheet ss:Name=\"" . $this->sWorksheetTitle . "\">\n<Table>\n";
//                 foreach ($this->lines as $line)
//                         echo $line;

//                 echo "</Table>\n</Worksheet>\n";
//                 echo $this->footer;
//         }
// }

// /**
//  * Simple excel generating from PHP5
//  *
//  * @package Utilities
//  * @license http://www.opensource.org/licenses/mit-license.php
//  * @author Oliver Schwarz <oliver.schwarz@gmail.com>
//  * @version 1.0
//  */

// /**
//  * Generating excel documents on-the-fly from PHP5
//  * 
//  * Uses the excel XML-specification to generate a native
//  * XML document, readable/processable by excel.
//  * 
//  * @package Utilities
//  * @subpackage Excel
//  * @author Oliver Schwarz <oliver.schwarz@vaicon.de>
//  * @version 1.1
//  * 
//  * @todo Issue #4: Internet Explorer 7 does not work well with the given header
//  * @todo Add option to give out first line as header (bold text)
//  * @todo Add option to give out last line as footer (bold text)
//  * @todo Add option to write to file
//  */
// class Excel_XML
// {

// 	/**
// 	 * Header (of document)
// 	 * @var string
// 	 */
//         private $header = "<?xml version=\"1.0\" encoding=\"%s\"?\>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";

//         /**
//          * Footer (of document)
//          * @var string
//          */
//         private $footer = "</Workbook>";

//         /**
//          * Lines to output in the excel document
//          * @var array
//          */
//         private $lines = array();

//         /**
//          * Used encoding
//          * @var string
//          */
//         private $sEncoding;
        
//         /**
//          * Convert variable types
//          * @var boolean
//          */
//         private $bConvertTypes;
        
//         /**
//          * Worksheet title
//          * @var string
//          */
//         private $sWorksheetTitle;

//         /**
//          * Constructor
//          * 
//          * The constructor allows the setting of some additional
//          * parameters so that the library may be configured to
//          * one's needs.
//          * 
//          * On converting types:
//          * When set to true, the library tries to identify the type of
//          * the variable value and set the field specification for Excel
//          * accordingly. Be careful with article numbers or postcodes
//          * starting with a '0' (zero)!
//          * 
//          * @param string $sEncoding Encoding to be used (defaults to UTF-8)
//          * @param boolean $bConvertTypes Convert variables to field specification
//          * @param string $sWorksheetTitle Title for the worksheet
//          */
//         public function __construct($sEncoding = 'UTF-8', $bConvertTypes = false, $sWorksheetTitle = 'Table1')
//         {
//                 $this->bConvertTypes = $bConvertTypes;
//         	$this->setEncoding($sEncoding);
//         	$this->setWorksheetTitle($sWorksheetTitle);
//         }
        
//         /**
//          * Set encoding
//          * @param string Encoding type to set
//          */
//         public function setEncoding($sEncoding)
//         {
//         	$this->sEncoding = $sEncoding;
//         }

//         /**
//          * Set worksheet title
//          * 
//          * Strips out not allowed characters and trims the
//          * title to a maximum length of 31.
//          * 
//          * @param string $title Title for worksheet
//          */
//         public function setWorksheetTitle ($title)
//         {
//                 $title = preg_replace ("/[\\\|:|\/|\?|\*|\[|\]]/", "", $title);
//                 $title = substr ($title, 0, 31);
//                 $this->sWorksheetTitle = $title;
//         }

//         /**
//          * Add row
//          * 
//          * Adds a single row to the document. If set to true, self::bConvertTypes
//          * checks the type of variable and returns the specific field settings
//          * for the cell.
//          * 
//          * @param array $array One-dimensional array with row content
//          */
//         public function addRow($array)
//         {
//         	$cells = "";
//                 foreach ($array as $k => $v):
//                         $type = 'String';
//                         if ($this->bConvertTypes === true && is_numeric($v)):
//                                 $type = 'Number';
//                         endif;
//                         $v = htmlentities($v, ENT_COMPAT, $this->sEncoding);
//                         $cells .= "<Cell><Data ss:Type=\"$type\">" . $v . "</Data></Cell>\n"; 
//                 endforeach;
//                 $this->lines[] = "<Row>\n" . $cells . "</Row>\n";
//         }

//         /**
//          * Add an array to the document
//          * @param array 2-dimensional array
//          */
//         public function addArray ($array)
//         {
//                 foreach ($array as $k => $v)
//                         $this->addRow ($v);
//         }


//         /**
//          * Generate the excel file
//          * @param string $filename Name of excel file to generate (...xls)
//          */
//         public function generateXML ($filename = 'excel-export')
//         {
//                 // correct/validate filename
//                 $filename = preg_replace('/[^aA-zZ0-9\_\-]/', '', $filename);
    	
//                 // deliver header (as recommended in php manual)
//                 header("Content-Type: application/vnd.ms-excel; charset=" . $this->sEncoding);
//                 header("Content-Disposition: inline; filename=\"" . $filename . ".xls\"");

//                 // print out document to the browser
//                 // need to use stripslashes for the damn ">"
//                 echo stripslashes (sprintf($this->header, $this->sEncoding));
//                 echo "\n<Worksheet ss:Name=\"" . $this->sWorksheetTitle . "\">\n<Table>\n";
//                 foreach ($this->lines as $line)
//                         echo $line;

//                 echo "</Table>\n</Worksheet>\n";
//                 echo $this->footer;
//         }

// }
