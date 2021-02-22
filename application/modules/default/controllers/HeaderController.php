<?php
class HeaderController extends Zend_Controller_Action
{
    public function init(){
        // $this->view->url = $this->_request->getBaseUrl();
        $this->view->BaseUrl=$this->_request->getBaseUrl();
        //Zend_Loader::loadClass('Menusm');
    }

    public function indexAction()
    {
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
 
        $username = $this->identity->user_username;

        $menus = new Default_Model_Module();
        $data = $menus->getModulesByUsername($username);

        $s = '';
        foreach ($data as $module) {
            
               //echo $this->BaseUrl;
                //$mod =($this->controller==$module['module_controller'])?'active':'';    
                if($module['module_type'] != null && $module['module_type'] != '' && $module['module_type'] == 'dieuhanh'){        
                    $s .='<li><a href="'.$this->_request->getBaseUrl().'/'.$module['module_controller'].'" >'.'<i class="icon icon-th-list"></i> <span>'.$module['module_name'].'</span>'.'</a>';
                    if($module['module_id'] != null){                  
                        $submenus = $menus->getSubcategoryByUsernameAndParentId($username,$module['module_id']);
                        if($submenus != null && count($submenus) > 0 ){
                            $s.= '<ul>';
                            foreach ($submenus as $submodule) { 
                                $s.='<li><a class="'.count($submenus).'" href="'.$this->_request->getBaseUrl().'/'.$submodule['module_controller'].'">'.'<span>'.$submodule['module_name'].'</span></a></li>';
                            }   
                            $s.= '</ul>';
                            
                        }   
                        unset($submenus); 
                    }
                   
                    $s.='</li>'; 
                }
      
        }

      
        $this->view->menu =  $data;
        $this->view->s =  $s;      

    }

    public function logoutAction() 
    { 
        $auth = Zend_Auth::getInstance(); 
        $auth->clearIdentity();                                  
        $this->_redirect('default/login');                                   
    } 
}