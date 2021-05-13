<?php

// Fee controller
class GroupController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->_request->getBaseUrl();
    }

    public function preDispatch()
    {

        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();

        $username = $this->identity->user_username;
        $password = $this->identity->user_password;

        $users2 = new Default_Model_UserAdmin();
        if ($users2->num($username, $password) > 0) {

        } else {
            $this->_redirect('/default/login');
            exit;
        }
    }

    public function indexAction()
    {
        $model = new Default_Model_Group();
        $data = $model->loadGroups();
        $this->view->groups = $data;

    }

    public function addAction()
    {
        $modelCustomers = new Default_Model_Customer();
        $customers = $modelCustomers->fetchAll();
        $this->view->customers = $customers;

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $modelGroups = new Default_Model_Group();
            $customersListPost = $data['customers'];
            $data['customers'] = implode(",", $customersListPost);

            $result = $modelGroups->insert($data);
            if ($result) {
                $modelPersonSms = new Default_Model_PersonSms();
                foreach ($customersListPost as $index => $value) {
                    $customer = $modelCustomers->fetchRow('cus_id = ' . $value);
                    $modelPersonSms->insert(array(
                        'person_name' => $customer->cus_fullname,
                        'person_email' => $customer->cus_email,
                        'person_mobile' => ($customer->cus_cellphone[0] == 0) ? '84'.substr($customer->cus_cellphone,1,strlen($customer->cus_cellphone)) : $customer->cus_cellphone,
                        'person_datebirthdate' => $customer->cus_birthday,
                        'person_code' => $customer->cus_identity_card,
                        'person_creatdate' => date('Y-m-d H:i:s'),
                        'group_id' => $result,
                    ));
                }
            }
            $this->_redirect('/group');
        }
    }

    public function detailAction(){

        $this->_helper->layout('homelayout')->disableLayout();
        $modelGroup = new Default_Model_Group();
        $group_id = $this->getRequest()->getParam('group_id');
        $data = $modelGroup->loadGroupById($group_id);
        $this->view->group = $data;

        $modelCustomers = new Default_Model_Customer();
        $customersExisted = $modelCustomers->loadCustomersByIds($data['customers']);
        $customers = $modelCustomers->fetchAll();

        $this->view->customers = $customers;

        $temp = array();
        if($customersExisted != null && sizeof($customersExisted) > 0){
            foreach($customersExisted as $cus){
                array_push($temp, $cus['cus_id']);
            }
        }
        $this->view->existed = $temp != null
         && sizeof($temp) > 0 ? $temp : null;


    }


    /*update information of customer*/
    public function updateAction(){
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

               if($this->view->parError == ''){
                    $modelGroup = new Default_Model_Group();
                    $data = array(
                       'group_name'=> $filter->filter($arrInput['group_name'])
                    );

                    $modelGroup->update($data, 'group_id = '. (int)($filter->filter($arrInput['group_id'])));
                }
            }
        }

    }


}