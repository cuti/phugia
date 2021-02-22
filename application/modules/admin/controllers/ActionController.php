<?php
class Admin_ActionController extends Zend_Controller_Action{
	
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

        //get user by user name
        // $acl = new Zend_Acl();
        // $acl->addRole(new Zend_Acl_Role($username));

        // //get list role_id from table acl by user id
        // $modelacl = new Admin_Model_Acl();
        // $dataacl = $modelacl->loadAclByUserId($this->identity->user_id);

        // // load list roles
        // $role = new Admin_Model_Role();
        // if($dataacl != null && sizeof($dataacl)>0){
        //     foreach($dataacl as $idrole){
        //         //load role by role id
        //         $datarole = $role->loadRoleByRoleId($idrole['role_id']);

        //         //cái này là lấy hết role của user đó
        //         if($datarole['module_controller'] != null){ 
        //             // kiểm tra nếu là controller này thì mới add quyền còn không thì thôi    
        //             if($datarole['module_controller'] == $this->_request->getControllerName()){
        //                 $acl->add(new Zend_Acl_Resource($datarole['module_controller']));
        //                 if($datarole['action_actname'] == $this->_request->getActionName()){
        //                     $acl->allow($username, $datarole['module_controller'],$this->_request->getActionName());
        //                 }                        
        //             }
        //         }                              
        //     }
        // }

        // Zend_Registry::set('acl', $acl);
        // if($acl->isAllowed($username,$this->_request->getControllerName(),$this->_request->getActionName()))
        // {
        //     //được quyền
        // }
        // else
        // {
        //     $this->_redirect('admin/temp/denied');exit;
        // }
    }

    public function indexAction(){ 
         $action = new Admin_Model_Action();
         $data = $action->loadAction();
         $this->view->actions =  $data;
    } 
    
    public function createnewroleAction(){
        //$this->_helper->layout('homelayout')->disableLayout();
        $action = new Admin_Model_Action();
        $data = $action->loadActiveAction();
        $this->view->actions =  $data;

        $module = new Admin_Model_Module();
        $data = $module->loadActiveModule();
        $this->view->modules =  $data;

        $user = new Admin_Model_UserAdmin();
        $data = $user->loadActiveUserAdmin();
        $this->view->users = $data;
    }

    public function test1Action(){
        //$this->_helper->layout('homelayout')->disableLayout();
        $action = new Admin_Model_Action();
        $data = $action->loadActiveAction();
        $this->view->actions =  $data;

        $module = new Admin_Model_Module();
        $data = $module->loadActiveModule();
        $this->view->modules =  $data;

        $user = new Admin_Model_UserAdmin();
        $data = $user->loadActiveUserAdmin();
        $this->view->users = $data;
    }

    public function listroleAction(){
        $model = new Admin_Model_Role();
        $data = $model->loadRoles();
        $this->view->roles = $data;
    }

    public function listrlnameAction(){
        $model = new Admin_Model_Rlname();
        $data = $model->loadRlnames();
        $this->view->roles = $data;
    }

    //view page assign role
    public function assignroleAction(){
        $userid = $this->getRequest()->getParam('userid');
        $modelRole = new Admin_Model_Rlname();
        $this->view->roles = $modelRole->loadActiveRlnames();
        $this->view->user_id = $userid;

        $modelRole = new Admin_Model_Rlname();
        $this->view->rldetails = $modelRole->loadListRlNameAssignUserId($userid);

    }

    public function editroleAction(){
        $rl_id = $this->getRequest()->getParam('rl_id');
        $this->view->rl_id = $rl_id;
        $modelRole = new Admin_Model_Rlname();
        $this->view->rldetails = $modelRole->loadByRlId($rl_id);

        $model = new Admin_Model_Role();
        $this->view->roles = $model->loadRolesByRlId($rl_id);

        $action = new Admin_Model_Action();
        $data = $action->loadActiveAction();
        $this->view->actions =  $data;

        $this->view->actionsassigns = $action->loadlistactionassignrlid($rl_id);

        $module = new Admin_Model_Module();
        $data = $module->loadActiveModule();
        $this->view->modules =  $data;

        $this->view->modulersassgins = $module->loadlistmoduleassignrlid($rl_id);       
        
    }

    //assign role to user
    public function assignAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if(!Zend_Validate::is($arrInput['user_id'],'NotEmpty')){
                    $this->view->parError = 'Bạn chưa chọn tài khoản để gắn vai trò';        
                }

                if(!Zend_Validate::is($arrInput['tomodule'],'NotEmpty')){
                    $this->view->parError = 'Bạn chưa chọn vai trò để gắn vào tài khoản';        
                }

                if($this->view->parError == ''){
                    // tao acl voi rl_id and user_id
                    $aclmodel = new Admin_Model_Acl();
                    $user_id = (int) trim($arrInput['user_id']);
                    if($arrInput['tomodule'] != null && sizeof($arrInput['tomodule']) >0){
                        //delet all acl of user before add
                        $checkExitsted = $aclmodel->loadAclByUserId($user_id);
                        if($checkExitsted != null && sizeof($checkExitsted) > 0){
                            $where = $aclmodel->getAdapter()->quoteInto('user_id = ?',$user_id);
                            $aclmodel->delete($where);
                        }                      

                        foreach($arrInput['tomodule'] as $rl){
                            $data = array(
                                //add new role
                                'rl_id'=> $rl,
                                'user_id'=> $user_id       
                            );
                            $aclmodel->insert($data); 
                        }
                    }          

                }
            }
        }

    }


    /**
     * create  new role ( add role and permission on menu)
     */
    public function createAction(){
        $this->_helper->layout('layout')->disableLayout();

        $role_name = $this->getRequest()->getParam('role_name');
        $role_code = $this->getRequest()->getParam('role_code');
        $modules = $this->getRequest()->getParam('tomodule');
        $actions = $this->getRequest()->getParam('toaction');
        $users = $this->getRequest()->getParam('touser');

        $myArray = array();

        $modeRlname = new Admin_Model_Rlname();

        if(!Zend_Validate::is($role_code,'NotEmpty')){
            $this->view->parError = 'Mã vai trò không được để trống';        
        }

        if(!Zend_Validate::is($role_name,'NotEmpty')){
            $this->view->parError = 'Tên vai trò không để trống ';        
        }

        if(!Zend_Validate::is($modules,'NotEmpty')){
            $this->view->parError = 'Menu không để trống ';        
        }

        if(!Zend_Validate::is($actions,'NotEmpty')){
           $this->view->parError = 'Hành động không để trống ';        
        }

        if(Zend_Validate::is($role_code,'NotEmpty')){
            $result = $modeRlname->loadRlByCode($role_code);
            if($result != null && sizeof($result) >0){
                $this->view->parError = 'Đã tồn tại vai trò với mã code bạn nhập';        
            }            
        }     

        if($this->view->parError == ''){
            // them role
            $dataRlname = array(
                //add new role
                'rl_name' => $role_name,
                'rl_code' => trim($role_code),
                'rl_status' =>'1'
            );
            $idrlid = $modeRlname->insert($dataRlname);
                  
            $modelModule = new Admin_Model_Module();
            //thêm role với action
            $role = new Admin_Model_Role();
            if($actions != null && sizeof($actions)){
                foreach($actions as $act){
                    if($modules != null && sizeof($modules)>0){
                        foreach($modules as $modu){
                            //add parent
                            $dataModule = $modelModule->loadModuleByModuleId($modu);
                            if($dataModule != null && $dataModule['module_parent_id'] != null){
                                $dataParent = array(
                                    //add new role
                                    'role_name' => $role_name,
                                    'role_action' => $role_code,
                                    'role_status' =>'1',
                                    'action_id'=> $act,
                                    'module_id' => $dataModule['module_parent_id'],
                                    'rl_id' => $idrlid

                                );                               
                                $role->insert($dataParent);
                            }
                            
                            //add children
                            $data = array(
                                //add new role
                                'role_name' => $role_name,
                                'role_action' => $role_code,
                                'role_status' =>'1',
                                'action_id'=> $act,
                                'module_id' => $modu,
                                'rl_id' => $idrlid

                            );
                            $idrole = $role->insert($data);
                            //array_push($myArray, $idrole);                             
                        }
                    }
                }
            }
        }
 
    }

    /**
     * edit role 
     */
    public function updateroleAction(){
        $this->_helper->layout('layout')->disableLayout();

        $rl_id = $this->getRequest()->getParam('rl_id');
        $modules = $this->getRequest()->getParam('tomodule');
        $actions = $this->getRequest()->getParam('toaction');
        
        $this->view->modules = $modules;
        $this->view->actions = $actions;
        //$this->view->users = $users;

        if(!Zend_Validate::is($modules,'NotEmpty')){
             $this->view->parError = 'Menu không để trống ';        
        }

        if(!Zend_Validate::is($actions,'NotEmpty')){
            $this->view->parError = 'Hành động không để trống ';        
        }

        if(!Zend_Validate::is($rl_id,'NotEmpty')){
            $this->view->parError = 'Id của vai trò không thể trống ';        
        }       

        //$myArray = array();
        $modeRlname = new Admin_Model_Rlname();
        $modelModule = new Admin_Model_Module();
        if($this->view->parError == ''){
            if($rl_id != null && $rl_id != ''){
                
                // them role
                $role = new Admin_Model_Role();
                if($actions != null && sizeof($actions)){
                    //delet all role of rl_id before add
                    $where = $role->getAdapter()->quoteInto('rl_id = ?',$rl_id );
                    $role->delete($where);
                    
                    foreach($actions as $act){
                        if($modules != null && sizeof($modules)>0){
                            foreach($modules as $modu){
                                //add parent menu id
                                $dataModule = $modelModule->loadModuleByModuleId($modu);
                                if($dataModule != null && $dataModule['module_parent_id'] != null){
                                    $data = array(
                                        //add new role
                                        'role_name' => 'test',
                                        'role_action' => 'test',
                                        'role_status' =>'1',
                                        'action_id'=> $act,
                                        'module_id' => $dataModule['module_parent_id'],
                                        'rl_id' => $rl_id
    
                                    );                               
                                    $role->insert($data);
                                }

                                $data = array(
                                    //add new role
                                    'role_name' => 'test',
                                    'role_action' => 'test',
                                    'role_status' =>'1',
                                    'action_id'=> $act,
                                    'module_id' => $modu,
                                    'rl_id' => $rl_id

                                );                               
                                $role->insert($data);
                                //array_push($myArray, $idrole);                             
                            }
                        }
                    }
                }
            }
        }
 
    }

}