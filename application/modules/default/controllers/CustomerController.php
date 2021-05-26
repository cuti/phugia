<?php

require_once 'ExcelReaderWriter.php';

class CustomerController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->_request->getBaseUrl();
    }

    public function preDispatch()
    {
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();

        if ($this->identity) {
            $username = $this->identity->user_username;
            $password = $this->identity->user_password;

            $users2 = new Default_Model_User();

            if ($users2->num($username, $password) > 0) {

            } else {
                $this->_redirect('/login');
                exit;
            }
        } else {
            $this->_redirect('/login');
            exit;
        }
    }

    public function indexAction()
    {
        $this->view->pageTitle = 'Quản Lý Khách Hàng';
    }

    public function getAllAction()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {
            $customer = new Default_Model_Customer();
            $data = $customer->loadCustomer();
        } else {
            $data = array();
        }

        echo json_encode(array('data' => $data));
        exit;
    }

    public function importAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $content = base64_decode($req->getParam('fileContent'));
            $fileNameWithExt = $req->getParam('fileName');
            $fileName = substr($fileNameWithExt, 0, strrpos($fileNameWithExt, '.')) . rand(1000000000, 9999999999);
            $fileExt = substr($fileNameWithExt, strrpos($fileNameWithExt, '.'));
            $fileDir = ROOT_PATH . '/upload';

            if (!is_dir($fileDir)) {
                mkdir($fileDir);
            }

            $success = file_put_contents($fileDir . '/' . $fileName . $fileExt, $content);

            if ($success) {
                $fileData = ExcelReaderWriter::read($fileDir . '/' . $fileName . $fileExt, $fileExt);

                $customer = new Default_Model_Customer();
                $result = $customer->importCustomer($fileData, $this->currentUser());
            }
        } else {
            $result = array(
                'message' => 'Invalid request',
                'status' => -1,
            );
        }

        echo json_encode($result);
        exit;
    }

    public function insertAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $body = $req->getRawBody();
            $data = json_decode($body);
            $customer = json_decode(json_encode($data->customer), true);
            $cusTypes = $data->customerTypes;

            $customerModel = new Default_Model_Customer();
            $result = $customerModel->insertCustomer($customer, $cusTypes, $this->currentUser());

            if ($result === 'cus_code') {
                $result = array(
                    'message' => 'CUS_CODE_DUP',
                    'status' => 0,
                );
            } else {
                $result = array(
                    'data' => $result,
                    'status' => 1,
                );
            }
        } else {
            $result = array(
                'message' => 'Invalid request',
                'status' => 0,
            );
        }

        echo json_encode($result);
        exit;
    }

    public function updateAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $body = $req->getRawBody();
            $data = json_decode($body);
            $customer = json_decode(json_encode($data->customer), true);
            $cusId = $data->cusId;
            $cusTypes = $data->customerTypes;

            $customerModel = new Default_Model_Customer();
            $result = $customerModel->updateCustomer($cusId, $customer, $cusTypes, $this->currentUser());

            if ($result === 'cus_code') {
                $result = array(
                    'message' => 'CUS_CODE_DUP',
                    'status' => 0,
                );
            } else {
                $result = array(
                    'data' => $result,
                    'status' => 1,
                );
            }
        } else {
            $result = array(
                'message' => 'Invalid request',
                'status' => 0,
            );
        }

        echo json_encode($result);
        exit;
    }

    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest() && $req->isPost()) {
            $body = $req->getRawBody();
            $data = json_decode($body);
            $cusId = $data->cusId;

            $customerModel = new Default_Model_Customer();
            $result = $customerModel->deleteCustomer($cusId, $this->currentUser());

            $result = array(
                'data' => $result,
                'status' => 1,
            );
        } else {
            $result = array(
                'message' => 'Invalid request',
                'status' => 0,
            );
        }

        echo json_encode($result);
        exit;
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    /**
     * Get current username
     */
    private function currentUser()
    {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        return $identity->user_username;
    }

    /* ***************************************************************************************************** */

    public function updatecustomerssupportAction()
    {
        $this->_helper->layout('layout')->disableLayout();
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();

        $username = $this->identity->user_username;
        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if ($this->view->parError == '') {
                    $currentdate = new Zend_Date();
                    $model = new Default_Model_CustomerSupport();

                    $data = array(
                        //'cus_id' => $arrInput['cus_id'],
                        'reason' => $arrInput['reason'],
                        'modifieddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'hours' => $arrInput['hours'],
                        'year' => $arrInput['year'],
                        'updated_username' => $username,
                    );

                    $model->update($data, 'support_id = ' . (int) ($filter->filter($arrInput['support_id'])));
                }

            }
        }
    }

    public function detailcustomerssupportAction()
    {
        $this->_helper->layout('homelayout')->disableLayout();
        $customerModel = new Default_Model_CustomerSupport();
        $id = $this->getRequest()->getParam('id');
        $this->view->data = $customerModel->loadCustomerSupportWithData($id);
    }

    public function deletecustomersupportAction()
    {
        $this->_helper->layout('homelayout')->disableLayout();
        $customerModel = new Default_Model_CustomerSupport();
        $id = $this->getRequest()->getParam('id');
        $where = $customerModel->getAdapter()->quoteInto('support_id = ?', $id);
        $customerModel->delete($where);
    }

    public function rewardisciplineAction()
    {
        $this->_helper->layout('layout')->disableLayout();
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();

        $username = $this->identity->user_username;

        $filter = new Zend_Filter();
    }

    public function deleterewarddesciplineAction()
    {
        $this->_helper->layout('homelayout')->disableLayout();
        $customerModel = new Default_Model_CustomersRewardDiscipline();
        $id = $this->getRequest()->getParam('id');
        $where = $customerModel->getAdapter()->quoteInto('id = ?', $id);
        $customerModel->delete($where);
    }

    public function detailrewarddesciplineAction()
    {
        $this->_helper->layout('homelayout')->disableLayout();
        $customerModel = new Default_Model_CustomersRewardDiscipline();
        $id = $this->getRequest()->getParam('id');
        //$data = $customerModel->loadCustomersRewardWithDataById($id);
        $this->view->data = $customerModel->loadCustomersRewardWithDataById($id);
    }

    public function updaterewarddisciplineAction()
    {
        $this->_helper->layout('layout')->disableLayout();
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();

        $username = $this->identity->user_username;

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                // if(!Zend_Validate::is($arrInput['cus_id'],'NotEmpty')){
                //     $this->view->parError = 'Bạn phải chọn luật sư để tạo khen thưởng kỉ luật!';
                // }

                if ($this->view->parError == '') {
                    $currentdate = new Zend_Date();
                    $model = new Default_Model_CustomersRewardDiscipline();

                    if ($arrInput['type'] == 'khenthuong') {
                        $data = array(
                            'reward_reason' => $arrInput['reward_reason'],
                            'cus_reward_document' => $arrInput['cus_reward_document'],
                            'year' => $arrInput['year'],
                            'updatedate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'updated_username' => $username,
                            //'created_username' => $username
                        );
                        $model->update($data, 'id = ' . (int) ($filter->filter($arrInput['id'])));
                    } else {
                        $data = array(
                            'discipline_reason' => $arrInput['discipline_reason'],
                            'law_help' => $filter->filter($arrInput['law_help']),
                            'people_problem' => $filter->filter($arrInput['people_problem']),
                            'cus_reward_discipline_document' => $filter->filter($arrInput['cus_reward_discipline_document']),
                            'year' => $arrInput['year'],
                            'updatedate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'updated_username' => $username,
                        );
                        $model->update($data, 'id = ' . (int) ($filter->filter($arrInput['id'])));
                    }
                }

            }
        }
    }

    public function createrewarddisciplineAction()
    {

        $this->_helper->layout('layout')->disableLayout();
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();

        $username = $this->identity->user_username;

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if (!Zend_Validate::is($arrInput['cus_id'], 'NotEmpty')) {
                    $this->view->parError = 'Bạn phải chọn luật sư để tạo khen thưởng kỉ luật!';
                }

                if ($this->view->parError == '') {
                    $currentdate = new Zend_Date();
                    $model = new Default_Model_CustomersRewardDiscipline();

                    if ($arrInput['cus_reward_discipline'] == 'khenthuong') {
                        $data = array(
                            'cus_id' => $arrInput['cus_id'],
                            'reward_reason' => $arrInput['cus_reason'],
                            'cus_reward_document' => $arrInput['cus_reward_document'],
                            'createdate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'reward_date' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'type' => 'khenthuong',
                            'year' => $arrInput['year'],
                            'created_username' => $username,
                        );
                        $model->insert($data);
                    } else {
                        $data = array(
                            'cus_id' => $arrInput['cus_id'],
                            'discipline_reason' => $arrInput['cus_reason'],
                            'createdate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'discipline_date' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'type' => 'kiluat',
                            'law_help' => $filter->filter($arrInput['law_help']),
                            'people_problem' => $filter->filter($arrInput['people_problem']),
                            'cus_reward_discipline_type' => $filter->filter($arrInput['cus_reward_discipline_type']),
                            'cus_reward_discipline_document' => $filter->filter($arrInput['cus_reward_discipline_document']),
                            'cus_reward_discipline_month' => $filter->filter($arrInput['cus_reward_discipline_month']),
                            'year' => $arrInput['year'],
                            'created_username' => $username,
                        );
                        $model->insert($data);
                    }
                }

            }
        }
    }

    //tạo trợ giúp pháp lý

    public function createsupportAction()
    {

        $this->_helper->layout('layout')->disableLayout();
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();

        $username = $this->identity->user_username;
        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if (!Zend_Validate::is($arrInput['cus_id'], 'NotEmpty')) {
                    $this->view->parError = 'Bạn phải chọn luật sư để tạo trợ giúp pháp lý!';
                }

                if ($this->view->parError == '') {
                    $currentdate = new Zend_Date();
                    $model = new Default_Model_CustomerSupport();

                    $data = array(
                        'cus_id' => $arrInput['cus_id'],
                        'reason' => $arrInput['reason'],
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'hours' => $arrInput['hours'],
                        'year' => $arrInput['year'],
                        'created_username' => $username,
                    );

                    $model->insert($data);
                }

            }
        }
    }

    public function indexoldAction()
    {
        $city = new Default_Model_City();
        $data = $city->loadCity();
        $this->view->cities = $data;

        // load thông tin của tchn ls
        // $organizationDetail = new Default_Model_OrganizationLawDetails();
        // $dataOrganLawDetails = $organizationDetail->loadOrganzationsLaw();
        // $this->view->organizations_data = $dataOrganLawDetails;

        //load khen thuong ki luat
        // $modelCustomersRewardDiscipline = new Default_Model_CustomersRewardDiscipline();
        // $dataCustomersRewardDiscipline = $modelCustomersRewardDiscipline->loadCustomersRewardDiscipline();
        // $this->view->dataCustomersRewardDiscipline = $dataCustomersRewardDiscipline;

        // //load tro giup phap ly
        // $modelCustomersSupport = new Default_Model_CustomerSupport();
        // $dataCustomersSupport = $modelCustomersSupport->loadCustomerSupports();
        // $this->view->dataCustomersSupport = $dataCustomersSupport;

        // //load thong tin ngon ngu
        // $modelLanguages = new Default_Model_Languages();
        // $this->view->languages = $modelLanguages->loadLanguages();

        /*insert log action*/
        // $this->auth = Zend_Auth::getInstance();
        // $this->identity = $this->auth->getIdentity();
        // $currentdate = new Zend_Date();
        // $useradminlog = new Default_Model_UserAdminLog();
        // $datalog = array(
        //     'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
        //     'useradmin_username' => $this->identity->user_username,
        //     'action' => 'Xem danh sách thành phố',
        //     'page' => $this->_request->getControllerName(),
        //     'useradmin_id' => $this->identity->user_id,
        //     'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
        //     'access_object' => '',
        // );
        // $useradminlog->insert($datalog);

    }

    /*update password of useradmin*/
    public function updatepasswordAction()
    {
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if (Zend_Validate::is($arrInput['currentPass'], 'NotEmpty')) {
                    if (md5(trim($filter->filter($arrInput['currentPass']))) != ($filter->filter($arrInput['passwordold']))) {
                        $this->view->parError = 'Mật khẩu hiện tại không trùng với hệ thống ';
                    }
                }

                if ($this->view->parError == '') {
                    $customer = new Default_Model_UserAdmin();
                    $data = array(
                        'user_password' => md5(trim($filter->filter($arrInput['password']))),
                    );

                    $customer->update($data, 'user_id = ' . (int) ($filter->filter($arrInput['user_id'])));

                    /*insert log action*/
                    $this->auth = Zend_Auth::getInstance();
                    $this->identity = $this->auth->getIdentity();
                    $currentdate = new Zend_Date();
                    $useradminlog = new Default_Model_UserAdminLog();
                    $datalog = array(
                        'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                        'useradmin_username' => $this->identity->user_username,
                        'action' => 'Cập nhật mật khẩu user đăng nhập',
                        'page' => $this->_request->getControllerName(),
                        'useradmin_id' => $this->identity->user_id,
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'access_object' => $filter->filter($arrInput['user_id']),
                    );
                    $useradminlog->insert($datalog);
                }
            }
        }

    }

    /*update status*/

    public function updatestatuscustomeronlineAction()
    {
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if ($this->view->parError == '') {
                    $customer = new Default_Model_Customer();
                    $status = 0;
                    if ($arrInput['cus_status_online'] != '' && $arrInput['cus_status_online'] == 'active') {
                        $status = 1;
                    }
                    $data = array(
                        'deleted_online' => $status,
                    );

                    $customer->update($data, 'cus_id = ' . (int) ($filter->filter($arrInput['cus_id_lock'])));

                    /*insert log action*/
                    $this->auth = Zend_Auth::getInstance();
                    $this->identity = $this->auth->getIdentity();
                    $currentdate = new Zend_Date();
                    $useradminlog = new Default_Model_UserAdminLog();
                    $datalog = array(
                        'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                        'useradmin_username' => $this->identity->user_username,
                        'action' => 'Cập nhật trạng thái trực tuyến luật sư',
                        'page' => $this->_request->getControllerName(),
                        'useradmin_id' => $this->identity->user_id,
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'access_object' => $filter->filter($arrInput['cus_id_lock']),
                    );
                    $useradminlog->insert($datalog);

                }
            }
        }
    }

    public function updatestatuscustomerAction()
    {
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if ($this->view->parError == '') {
                    $customer = new Default_Model_Customer();
                    $status = 0;
                    if ($arrInput['cus_status'] != '' && $arrInput['cus_status'] == 'active') {
                        $status = 1;
                    }
                    $data = array(
                        'cus_status' => $status,
                    );

                    $customer->update($data, 'cus_id = ' . (int) ($filter->filter($arrInput['cus_id_lock'])));

                    /*insert log action*/
                    $this->auth = Zend_Auth::getInstance();
                    $this->identity = $this->auth->getIdentity();
                    $currentdate = new Zend_Date();
                    $useradminlog = new Default_Model_UserAdminLog();
                    $datalog = array(
                        'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                        'useradmin_username' => $this->identity->user_username,
                        'action' => 'Cập nhật trạng thái luật sư',
                        'page' => $this->_request->getControllerName(),
                        'useradmin_id' => $this->identity->user_id,
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'access_object' => $filter->filter($arrInput['cus_id_lock']),
                    );
                    $useradminlog->insert($datalog);

                }
            }
        }
    }

    /*send mail*/
    public function sendMail($email = null, $data = [])
    {
        // $config = array(
        //     'ssl' => 'ssl',
        //     'auth'=>'login',
        //     'username'=>'doanluatsu.hochiminh',
        //     'password'=>'Matkhaucap1',
        //     'port' => 465
        // );
        $config = array(
            'ssl' => 'tls',
            'auth' => 'login',
            'username' => 'info@hcba.vn',
            'password' => '^CAXXG#etSWy',
            'port' => 587,
        );
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/mail/');
        $html->assign('content', $data['mail_content']);

        $body = $html->render('_mail.phtml');

        $transport = new Zend_Mail_Transport_Smtp('mail.hcba.vn', $config);
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($body, 'UTF-8');
        $mail->setFrom('info@hcba.vn', 'Đoàn Luật Sư TP.Hồ Chí Minh');
        //$mail->setFrom('doanluatsu.hochiminh@gmail.com', 'Đoàn Luật Sư TP.Hồ Chí Minh');
        $mail->addTo($email, $email);
        $mail->setSubject($data['mail_subject']);
        try {
            $mail->send($transport);
            return true;
        } catch (Exception $exception) {

        }
        return false;
    }

    /*update password*/
    public function updatenewpasswordcustomerAction()
    {
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if ($this->view->parError == '') {
                    $customer = new Default_Model_Customer();
                    $data = array(
                        'cus_password' => md5(trim($filter->filter($arrInput['password']))),
                    );

                    $customer->update($data, 'cus_id = ' . (int) ($filter->filter($arrInput['cus_id'])));

                    $dataContentMail = [
                        "mail_subject" => "[Đoàn Luật Sư TP HCM] Thay đổi mật khẩu",
                        "mail_content" => "Bạn đã đổi mật khẩu. Mật khẩu mới của bạn là " . trim($filter->filter($arrInput['password']))];
                    $customerData = $customer->getCustomerByUserId($arrInput['cus_id']);

                    $this->sendMail($customerData['cus_email'], $dataContentMail);

                    /*insert log action*/
                    $this->auth = Zend_Auth::getInstance();
                    $this->identity = $this->auth->getIdentity();
                    $currentdate = new Zend_Date();
                    $useradminlog = new Default_Model_UserAdminLog();
                    $datalog = array(
                        'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                        'useradmin_username' => $this->identity->user_username,
                        'action' => 'Cập nhật mật khẩu luật sư',
                        'page' => $this->_request->getControllerName(),
                        'useradmin_id' => $this->identity->user_id,
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'access_object' => $filter->filter($arrInput['cus_id']),
                    );
                    $useradminlog->insert($datalog);

                }
            }
        }

    }

    /*update password of customer*/
    public function updatepasswordcustomerAction()
    {
        $this->_helper->layout('layout')->disableLayout();

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                if (Zend_Validate::is($arrInput['currentPass'], 'NotEmpty')) {
                    if (md5(trim($filter->filter($arrInput['currentPass']))) != ($filter->filter($arrInput['passwordold']))) {
                        $this->view->parError = 'Mật khẩu hiện tại không trùng với hệ thống ';
                    }
                }

                if ($this->view->parError == '') {
                    $customer = new Default_Model_Customer();
                    $data = array(
                        'cus_password' => md5(trim($filter->filter($arrInput['password']))),
                    );

                    $customer->update($data, 'cus_id = ' . (int) ($filter->filter($arrInput['cus_id'])));
                }
            }
        }

    }

    /*create new customer*/
    public function createAction()
    {

        $this->_helper->layout('layout')->disableLayout();

        //$datatest = array();

        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();
        $username = $this->identity->user_username;

        $filter = new Zend_Filter();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $arrInput = $this->_request->getParams();
                $this->view->arrInput = $arrInput;

                $customer = new Default_Model_Customer();

                // $datatest['id'] = '';
                // $datatest['error'] = '';
                // $datatest['type'] = ($filter->filter($arrInput['submittype']));

                if (Zend_Validate::is($arrInput['cus_cellphone'], 'NotEmpty')) {
                    if ($customer->validateIndentityCardOrPhoneOrEmail('phone',
                        $filter->filter($arrInput['cus_cellphone'])) > 0) {
                        $this->view->parError = 'Lỗi: Số điện thoại đã tồn tại trong hệ thống!';
                        //$datatest['error']  = $this->view->parError;

                        $datatest = array(
                            'id' => '',
                            'error' => $this->view->parError,
                            'type' => $filter->filter($arrInput['submittype']),
                        );

                        echo json_encode($datatest);
                        exit;
                    }

                }

                if (Zend_Validate::is($arrInput['cus_identity_card'], 'NotEmpty')) {
                    if ($customer->validateIndentityCardOrPhoneOrEmail('ID',
                        $filter->filter($arrInput['cus_identity_card'])) > 0) {
                        $this->view->parError = 'Lỗi: Số CMND đã tồn tại trong hệ thống!';
                        //$datatest['error']  = $this->view->parError;
                        $datatest = array(
                            'id' => '',
                            'error' => $this->view->parError,
                            'type' => $filter->filter($arrInput['submittype']),
                        );
                        echo json_encode($datatest);
                        exit;
                    }

                }

                if (Zend_Validate::is($arrInput['cus_email'], 'NotEmpty')) {
                    if ($customer->validateIndentityCardOrPhoneOrEmail('email',
                        $filter->filter($arrInput['cus_email'])) > 0) {
                        $this->view->parError = 'Lỗi: Email đã tồn tại trong hệ thống!';
                        //$datatest['error']  = $this->view->parError;
                        $datatest = array(
                            'id' => '',
                            'error' => $this->view->parError,
                            'type' => $filter->filter($arrInput['submittype']),
                        );
                        echo json_encode($datatest);
                        exit;
                    }
                }

                if (Zend_Validate::is($arrInput['cus_identity_card'], 'NotEmpty')) {
                    if ($customer->validateIndentityCardOrPhoneOrEmail('username',
                        $filter->filter($arrInput['cus_identity_card'])) > 0) {
                        $this->view->parError = 'Lỗi : Username đã tồn tại trong hệ thống!';
                        //$datatest['error']  = $this->view->parError;
                        $datatest = array(
                            'id' => '',
                            'error' => $this->view->parError,
                            'type' => $filter->filter($arrInput['submittype']),
                        );
                        echo json_encode($datatest);
                        exit;
                    }
                }

                /* covert date cmnd*/
                $final_cmnd = '';
                $cmnd = $filter->filter($arrInput['cus_identity_date']);
                if ($cmnd != null && $cmnd != '') {
                    $date_cmnd = str_replace('/', '-', $cmnd);
                    $final_cmnd = date('Y-m-d', strtotime($date_cmnd));
                }

                /* covert date birthday*/
                $final_birthday = '';
                $birthday = $filter->filter($arrInput['cus_birthday']);
                if ($birthday != null && $birthday != '') {
                    $date_birthday = str_replace('/', '-', $birthday);
                    $final_birthday = date('Y-m-d', strtotime($date_birthday));
                }

                /* covert date passport*/
                // $passport = $filter->filter($arrInput['cus_passport_date']);
                // $date_passport = str_replace('/', '-', $passport);
                // $final_passport =  date('Y-m-d', strtotime($date_passport));

                /* covert date cus_joining_communist_youth*/
                // $cus_joining_communist_youth = $filter->filter($arrInput['cus_joining_communist_youth']);
                // $date_cus_joining_communist_youth = str_replace('/', '-', $cus_joining_communist_youth);
                // $final_cus_joining_communist_youth =  date('Y-m-d', strtotime($date_cus_joining_communist_youth));

                /* covert date cus_joining_communist_prepare*/
                $final_cus_joining_communist_prepare = '';
                $cus_joining_communist_prepare = $filter->filter($arrInput['cus_joining_communist_prepare']);
                if ($cus_joining_communist_prepare != null && $cus_joining_communist_prepare != '') {
                    $date_cus_joining_communist_prepare = str_replace('/', '-', $cus_joining_communist_prepare);
                    $final_cus_joining_communist_prepare = date('Y-m-d', strtotime($date_cus_joining_communist_prepare));
                }

                /* covert date cus_joining_communist*/
                $final_cus_joining_communist = '';
                $cus_joining_communist = $filter->filter($arrInput['cus_joining_communist']);
                if ($cus_joining_communist != null && $cus_joining_communist != '') {
                    $date_cus_joining_communist = str_replace('/', '-', $cus_joining_communist);
                    $final_cus_joining_communist = date('Y-m-d', strtotime($date_cus_joining_communist));
                }

                // $image = $filter->filter($arrInput['image']);
                // $attachment = $filter->filter($arrInput['attachment']);
                $gioitinh = 'Nam';
                if ($filter->filter($arrInput['cus_sex']) == "nu") {
                    $gioitinh = "Nữ";
                }

                if ($this->view->parError == '') {

                    $currentdate = new Zend_Date();

                    $data = array(
                        'cus_firstname' => ltrim($filter->filter($arrInput['cus_firstname'])),
                        'cus_lastname' => rtrim($filter->filter($arrInput['cus_lastname'])),
                        'cus_sex' => $gioitinh,
                        // 'cus_country' => $filter->filter($arrInput['cus_country']),
                        'cus_birthday' => $final_birthday,
                        // 'cus_joining_communist_youth' => $final_cus_joining_communist_youth,
                        'cus_joining_communist_prepare' => $final_cus_joining_communist_prepare,
                        'cus_joining_communist' => $final_cus_joining_communist,
                        'cus_nation' => $filter->filter($arrInput['cus_nation']),
                        // 'cus_birthplace' => $filter->filter($arrInput['cus_birthplace']),
                        'cus_cellphone' => $filter->filter($arrInput['cus_cellphone']),
                        // 'cus_homephone' => $filter->filter($arrInput['cus_homephone']),
                        'cus_identity_card' => $filter->filter($arrInput['cus_identity_card']),
                        'cus_identity_place' => $filter->filter($arrInput['cus_identity_place']),
                        'cus_identity_date' => $final_cmnd,
                        // 'cus_passport_card' => $filter->filter($arrInput['cus_passport_card']),
                        // 'cus_passport_place' => $filter->filter($arrInput['cus_passport_place']),
                        // 'cus_passport_date' => $final_passport,
                        'cus_address_resident' => $filter->filter($arrInput['cus_address_resident']),
                        'cus_educations' => $filter->filter($arrInput['cus_educations']),
                        //'cus_language_level' => $filter->filter($arrInput['cus_language_level']),
                        'cus_date_created' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'city_id' => $filter->filter($arrInput['city_id']),
                        'cus_username' => $filter->filter($arrInput['cus_identity_card']),
                        'cus_password' => md5(trim($filter->filter($arrInput['cus_identity_card']))),
                        'cus_fullname' => ($filter->filter($arrInput['cus_firstname']) . ' ' . $filter->filter($arrInput['cus_lastname'])),
                        'cus_email' => $filter->filter($arrInput['cus_email']),
                        //'cus_major' => $filter->filter($arrInput['cus_major']),
                        'cus_address_resident_now' => $filter->filter($arrInput['cus_address_resident_now']),
                        'cus_member' => '0',
                        'cus_status' => '1',
                        'cus_active' => '1',
                        'cus_religion' => $filter->filter($arrInput['cus_religion']),
                        'cus_nation' => $filter->filter($arrInput['cus_nation']),
                        'language_id' => $filter->filter($arrInput['language_id']),
                        'cus_users_created' => $username,
                        //type = 0 tap su - 1 luat su
                        //'cus_type'=> $filter->filter($arrInput['cus_type'])
                        //'cus_organization' => $filter->filter($arrInput['cus_organization']),
                        //'cus_lawyer_number' =>  $filter->filter($arrInput['cus_lawyer_number'])
                    );

                    $customernewid = $customer->insert($data);

                    $cmndluuhinh = $filter->filter($arrInput['cus_identity_card']);

                    $attachments = new Default_Model_Attachments();

                    //anh 3x4
                    if ($_FILES['image']['name'] != '') {
                        $imagefile = $_FILES['image']['name'];
                        $folder = './files/upload/anh/';
                        //if(!is_dir($folder)){
                        //    mkdir($folder);
                        //}else{
                        if (file_exists($folder . $_FILES['image']['name'])) {
                            //unlink('./files/upload/ly-lich/' . $data['attachment_name']);
                            unlink($folder . $_FILES['image']['name']);
                        }

                        //}

                        // Upload file
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $folder . $_FILES['image']['name'])) {
                            // if(mime_content_type($folder.$_FILES['image']['name']) == 'image/jpeg'){
                            $newName = $cmndluuhinh . '_anh_1' . '.jpg';
                            rename($folder . $_FILES['image']['name'], $folder . $newName);
                            $imagefile = $newName;
                            // }else{
                            //     $newName = '1_anh_'.$customernewid.'.png';
                            //     rename($folder.$_FILES['image']['name'],$folder.$newName);
                            //     $imagefile =  $newName;
                            // }
                        }
                        //move_uploaded_file($_FILES['image']['tmp_name'], './files/upload/anh/'.$_FILES['image']['name']);
                        //$imagefile =  $_FILES["image"]["name"];
                        $dataacttachment = array(
                            'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'cus_id' => $customernewid,
                            'attachment_name' => $imagefile,
                            'type' => 'anh',
                        );
                        $attachments->insert($dataacttachment);

                    }

                    //ly lich
                    if ($_FILES['attachment']['name']) {

                        $attachmentfile = $_FILES['attachment']['name'];
                        $folder = './files/upload/ly-lich/';
                        //$folder = './files/upload/ly-lich/'.$customernewid.'/';
                        //if(!is_dir($folder)){
                        //    mkdir($folder);
                        //}else{
                        if (file_exists($folder . $_FILES['attachment']['name'])) {
                            //unlink('./files/upload/ly-lich/' . $data['attachment_name']);
                            unlink($folder . $_FILES['attachment']['name']);
                        }

                        //}
                        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $folder . $_FILES['attachment']['name'])) {
                            //if(mime_content_type($folder.$_FILES['attachment']['tmp_name']) == 'application/pdf'){
                            $newName = $cmndluuhinh . '.pdf';
                            rename($folder . $_FILES['attachment']['name'], $folder . $newName);
                            $attachmentfile = $newName;

                            //}
                        }

                        // Upload file
                        //move_uploaded_file($_FILES['attachment']['tmp_name'], './files/upload/ly-lich/'.$_FILES['attachment']['name']);
                        $dataacttachmentlylich = array(
                            'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'cus_id' => $customernewid,
                            'attachment_name' => $attachmentfile,
                            'type' => 'lylich',

                        );
                        $attachments->insert($dataacttachmentlylich);

                    }

                    //ly lich
                    if ($_FILES['attachment2']['name']) {

                        $attachmentfile = $_FILES['attachment2']['name'];
                        $folder = './files/upload/ly-lich-1/';
                        //$folder = './files/upload/ly-lich/'.$customernewid.'/';
                        //if(!is_dir($folder)){
                        //    mkdir($folder);
                        //}else{
                        if (file_exists($folder . $_FILES['attachment2']['name'])) {
                            //unlink('./files/upload/ly-lich/' . $data['attachment_name']);
                            unlink($folder . $_FILES['attachment2']['name']);
                        }

                        //}
                        if (move_uploaded_file($_FILES['attachment2']['tmp_name'], $folder . $_FILES['attachment2']['name'])) {
                            //if(mime_content_type($folder.$_FILES['attachment']['tmp_name']) == 'application/pdf'){
                            $newName = $cmndluuhinh . '.pdf';
                            rename($folder . $_FILES['attachment2']['name'], $folder . $newName);
                            $attachmentfile = $newName;

                            //}
                        }

                        // Upload file
                        //move_uploaded_file($_FILES['attachment']['tmp_name'], './files/upload/ly-lich/'.$_FILES['attachment']['name']);
                        $dataacttachmentlylich = array(
                            'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                            'cus_id' => $customernewid,
                            'attachment_name' => $attachmentfile,
                            'type' => 'khac',

                        );
                        $attachments->insert($dataacttachmentlylich);

                    }

                    //$idcustomer = $customer->insert($data);
                    //$this->view->idcustomer = $idcustomer;
                    if ($filter->filter($arrInput['submittype']) == 'intership') {
                        $this->view->link = 'fee/intership';
                        $this->view->linkName = 'Đi đến trang tạo phí tập sự';

                    } else if ($filter->filter($arrInput['submittype']) == 'joining') {
                        $this->view->link = 'fee/joining';
                        $this->view->linkName = 'Đi đến trang tạo phí gia nhập';
                    }

                    /*insert log action*/
                    $currentdate = new Zend_Date();
                    $useradminlog = new Default_Model_UserAdminLog();
                    $datalog = array(
                        'ip' => $this->getRequest()->getServer('REMOTE_ADDR'),
                        'useradmin_username' => $this->identity->user_username,
                        'action' => 'Tạo mới thông tin luật sư',
                        'page' => $this->_request->getControllerName(),
                        'useradmin_id' => $this->identity->user_id,
                        'createddate' => $currentdate->toString('YYYY-MM-dd HH:mm:ss'),
                        'access_object' => $customernewid,
                    );
                    $useradminlog->insert($datalog);
                    //exit;

                    //$datatest['id'] = $customernewid;
                    $datatest = array(
                        'id' => $customernewid,
                        'error' => '',
                        'type' => $filter->filter($arrInput['submittype']),
                    );
                    echo json_encode($datatest);
                    exit;

                }
            }
        }

    }

    /*search customer*/
    public function searchAction()
    {
        $this->_helper->layout('homelayout')->disableLayout();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $q = $this->getRequest()->getParam('searchword');
                $field = $this->getRequest()->getParam('searchfield');
                $search = new Default_Model_Customer();
                $result = $search->searchByCellPhoneOrIdentityCardOrName($q, $field);
                $this->view->resultSearch = $result;
                $this->view->q = $q;

            }
        }

    }

    /* saerch ls tinh*/
    public function searchlstinhAction()
    {
        $this->_helper->layout('homelayout')->disableLayout();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

                $q = $this->getRequest()->getParam('searchword');
                $field = $this->getRequest()->getParam('searchfield');
                $search = new Default_Model_Customer();
                $result = $search->searchByCellPhoneOrIdentityCardOrNameTinh($q, $field);
                $this->view->resultSearch = $result;
                $this->view->q = $q;

            }
        }

    }

    /*page update information of customer*/
    public function detailsAction()
    {
        $city = new Default_Model_City();
        $data = $city->loadCity();
        $this->view->cities = $data;

        $customer = new Default_Model_Lawyer();
        $data = $customer->loadLawyerStatusNotActive();
        $this->view->data = $data;

        // lấy tô chức hành nghề LS
        $organizationDetail = new Default_Model_OrganizationLawDetails();
        $dataOrganLawDetails = $organizationDetail->loadOrganzationsLaw();
        $this->view->organizations_data = $dataOrganLawDetails;

        //load thong tin ngon ngu
        $modelLanguages = new Default_Model_Languages();
        $this->view->languages = $modelLanguages->loadLanguages();

    }

    public function detailAction()
    {
        $this->_helper->layout('homelayout')->disableLayout();
        $customer = new Default_Model_Customer();
        $q = $this->getRequest()->getParam('searchword');
        $data = $customer->getCustomerWithLawyerFee($q);

        //select max id to get endmonth
        $data["endmonth"] = $customer->getEndMonthByCusId($data['cus_id']);

        if ($data["endmonth"] == null || $data["endmonth"] == '') {
            $db = Zend_Db_Table::getDefaultAdapter();
            $checkquery = $db->select()->from('history_joining',
                array('createddate', 'cus_id', 'history_joining_id', 'law_id', 'payment_joining_status'))
                ->where('history_joining.cus_id = ?', $data['cus_id'])
                ->order('history_joining_id desc')
                ->limit(1);
            $dataMemberPayment = $db->fetchRow($checkquery);

            if ($dataMemberPayment != null && $dataMemberPayment['payment_joining_status'] == 1) {
                $data["endmonth"] = date('m/Y', strtotime($dataMemberPayment['createddate']));
            }
        } else {

            $dataMonthYear = explode("/", $data["endmonth"]);
            // $month = date('n',strtotime($data["endmonth"]));
            // $year = date('Y',strtotime($data["endmonth"]));
            $month = $dataMonthYear[0];
            $year = $dataMonthYear[1];

            $db = Zend_Db_Table::getDefaultAdapter();
            $checkquery = $db->select()->from('history_joining',
                array('createddate', 'cus_id', 'history_joining_id', 'law_id', 'payment_joining_status'))
                ->where('history_joining.cus_id = ?', $data['cus_id'])
                ->order('history_joining_id desc')
                ->limit(1);
            $dataMemberPayment = $db->fetchRow($checkquery);

            if ($dataMemberPayment != null && $dataMemberPayment['payment_joining_status'] == 1) {
                $monthPayment = date('n', strtotime($dataMemberPayment["createddate"]));
                $yearPayment = date('Y', strtotime($dataMemberPayment["createddate"]));

                if ($year >= $yearPayment) {
                    if ($month >= $monthPayment) {

                    } else {
                        //$data["endmonth"] = $monthPayment+"/"+;
                    }
                } else {
                    $data["endmonth"] = date('m/Y', strtotime($dataMemberPayment['createddate']));
                }

            }
        }

        $attachments = new Default_Model_Attachments();
        $dataAttachments = $attachments->loadAttachmentsByCusId($data['cus_id']);

        $dateEmpty = '1900-01-01 00:00:00';
        $data["law_certification_createdate"] = ($data['law_certification_createdate'] != null && $data['law_certification_createdate'] != '' && $data['law_certification_createdate'] != $dateEmpty) ? date('d/m/Y', strtotime($data['law_certification_createdate'])) : ''; // works
        $data['cus_sex'] = trim($data['cus_sex']);
        $data["cus_birthday"] = ($data['cus_birthday'] != null && $data['cus_birthday'] != '' && $data['cus_birthday'] != $dateEmpty) ? date('d/m/Y', strtotime($data['cus_birthday'])) : ''; // works
        $data["cus_identity_date"] = ($data['cus_identity_date'] != null && $data['cus_identity_date'] != '' && $data['cus_identity_date'] != $dateEmpty) ? date('d/m/Y', strtotime($data['cus_identity_date'])) : '';
        $data["cus_date_lawyer_number"] = ($data['cus_date_lawyer_number'] != null && $data['cus_date_lawyer_number'] != '' && $data['cus_date_lawyer_number'] != $dateEmpty) ? date('d/m/Y', strtotime($data['cus_date_lawyer_number'])) : '';
        $data["cus_joining_communist_youth"] = ($data['cus_joining_communist_youth'] != null && $data['cus_joining_communist_youth'] != '' && $data['cus_joining_communist_youth'] != $dateEmpty) ? date('d/m/Y', strtotime($data['cus_joining_communist_youth'])) : '';
        $data["cus_joining_communist_prepare"] = ($data['cus_joining_communist_prepare'] != null && $data['cus_joining_communist_prepare'] != '' && $data['cus_joining_communist_prepare'] != $dateEmpty) ? date('d/m/Y', strtotime($data['cus_joining_communist_prepare'])) : '';
        $data["cus_joining_communist"] = ($data['cus_joining_communist'] != null && $data['cus_joining_communist'] != '' && $data['cus_joining_communist'] != $dateEmpty) ? date('d/m/Y', strtotime($data['cus_joining_communist'])) : '';

        if ($data != null) {
            if ($dataAttachments != null && sizeof($dataAttachments) > 0) {
                foreach ($dataAttachments as $atc) {
                    if ($atc['type'] == 'anh') {
                        if ($atc['attachment_name'] != null && $atc['attachment_name'] != '') {
                            $data['attachment_name_image'] = $this->_request->getBaseUrl() . '/files/upload/anh/' . $atc['attachment_name'];
                            $data['type_image'] = $atc['type'];
                        }
                    } else if ($atc['type'] == 'lylich') {
                        if ($atc['attachment_name'] != null && $atc['attachment_name'] != '') {
                            $data['attachment_name_pdf'] = $this->_request->getBaseUrl() . '/files/upload/ly-lich/' . $atc['attachment_name'];
                            $data['type_pdf'] = $atc['type'];
                        }

                    } else if ($atc['type'] == 'khac') {
                        if ($atc['attachment_name'] != null && $atc['attachment_name'] != '') {
                            $data['attachment_name_khac'] = $this->_request->getBaseUrl() . '/files/upload/ly-lich-1/' . $atc['attachment_name'];
                            $data['type_khac'] = $atc['type'];
                        }
                    }
                }
            }
        }

        $modelInternship = new Default_Model_Intership();
        $dataIntership = $modelInternship->getLastIntership($data['cus_id']);
        if ($dataIntership != null && $dataIntership['inter_number_name'] != null &&
            $dataIntership['inter_number_name'] != '') {
            $data["inter_number_name"] = $dataIntership['inter_number_name'];
            $data["inter_id"] = $dataIntership['inter_id'];
        }

        // check if history more than 2 times . it rejoining.
        $dbHistory = Zend_Db_Table::getDefaultAdapter();
        $historyQuery = $dbHistory->select()->from('history_joining',
            array('history_joining_id'
                , 'cus_id'
                , 'law_id'
                , 'law_joining_number'
                , 'createddate'
                , 'created_username'
                , 'law_joining_note'
                , 'payment_joining_status'))
            ->where('history_joining.cus_id = ?', $data['cus_id']);
        // ->order('payment_inter_off_id desc')
        // ->limit(1);
        $dataHistory = $dbHistory->fetchAll($historyQuery);

        if ($dataHistory != null) {
            if (sizeof($dataHistory) > 0) {
                $data['numberjoining'] = sizeof($dataHistory);
            }
        } else {
            $data['numberjoining'] = 0;
        }

        // check mooney intership
        $db = Zend_Db_Table::getDefaultAdapter();
        $checkquery = $db->select()->from('payment_intership_offline', array('amount', 'cus_id', 'payment_inter_off_id'))
            ->where('payment_intership_offline.cus_id = ?', $data['cus_id'])
            ->order('payment_inter_off_id desc')
            ->limit(1);
        $dataInterAmount = $db->fetchRow($checkquery);

        if ($dataInterAmount != null && $dataInterAmount['amount'] != null) {
            $data['inter_amount'] = $dataInterAmount['amount'];
        } else {
            $data['inter_amount'] = null;
        }

        echo json_encode($data);
        exit;
    }

    public function detailLSTinhAction()
    {
        $this->_helper->layout('homelayout')->disableLayout();
        $customer = new Default_Model_Customer();
        $q = $this->getRequest()->getParam('searchword');
        $data = $customer->getCustomerWithLawyerFee($q);

        //select max id to get endmonth
        $data["endmonth"] = $customer->getEndMonthByCusId($data['cus_id']);

        $attachments = new Default_Model_Attachments();
        $dataAttachments = $attachments->loadAttachmentsByCusId($data['cus_id']);

        $dateEmpty = '1900-01-01 00:00:00';
        $data["law_certification_createdate"] = ($data['law_certification_createdate'] != null && $data['law_certification_createdate'] != '' && $data['law_certification_createdate'] != $dateEmpty) ? date('d/m/Y', strtotime($data['law_certification_createdate'])) : ''; // works
        $data['cus_sex'] = trim($data['cus_sex']);
        $data["cus_birthday"] = ($data['cus_birthday'] != null && $data['cus_birthday'] != '' && $data['cus_birthday'] != $dateEmpty) ? date('d/m/Y', strtotime($data['cus_birthday'])) : ''; // works
        $data["cus_identity_date"] = ($data['cus_identity_date'] != null && $data['cus_identity_date'] != '' && $data['cus_identity_date'] != $dateEmpty) ? date('d/m/Y', strtotime($data['cus_identity_date'])) : '';
        $data["cus_joining_communist_youth"] = ($data['cus_joining_communist_youth'] != null && $data['cus_joining_communist_youth'] != '' && $data['cus_joining_communist_youth'] != $dateEmpty) ? date('d/m/Y', strtotime($data['cus_joining_communist_youth'])) : '';
        $data["cus_joining_communist_prepare"] = ($data['cus_joining_communist_prepare'] != null && $data['cus_joining_communist_prepare'] != '' && $data['cus_joining_communist_prepare'] != $dateEmpty) ? date('d/m/Y', strtotime($data['cus_joining_communist_prepare'])) : '';
        $data["cus_joining_communist"] = ($data['cus_joining_communist'] != null && $data['cus_joining_communist'] != '' && $data['cus_joining_communist'] != $dateEmpty) ? date('d/m/Y', strtotime($data['cus_joining_communist'])) : '';

        if ($data != null) {
            if ($dataAttachments != null && sizeof($dataAttachments) > 0) {
                foreach ($dataAttachments as $atc) {
                    if ($atc['type'] == 'anh') {
                        if ($atc['attachment_name'] != null && $atc['attachment_name'] != '') {
                            $data['attachment_name_image'] = $this->_request->getBaseUrl() . '/files/upload/anh/' . $atc['attachment_name'];
                            $data['type_image'] = $atc['type'];
                        }
                    } else if ($atc['type'] == 'lylich') {
                        if ($atc['attachment_name'] != null && $atc['attachment_name'] != '') {
                            $data['attachment_name_pdf'] = $this->_request->getBaseUrl() . '/files/upload/ly-lich/' . $atc['attachment_name'];
                            $data['type_pdf'] = $atc['type'];
                        }

                    } else if ($atc['type'] == 'khac') {
                        if ($atc['attachment_name'] != null && $atc['attachment_name'] != '') {
                            $data['attachment_name_khac'] = $this->_request->getBaseUrl() . '/files/upload/ly-lich-1/' . $atc['attachment_name'];
                            $data['type_khac'] = $atc['type'];
                        }
                    }
                }
            }
        }

        $modelInternship = new Default_Model_Intership();
        $dataIntership = $modelInternship->getLastIntership($data['cus_id']);
        if ($dataIntership != null && $dataIntership['inter_number_name'] != null &&
            $dataIntership['inter_number_name'] != '') {
            $data["inter_number_name"] = $dataIntership['inter_number_name'];
            $data["inter_id"] = $dataIntership['inter_id'];
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $checkquery = $db->select()->from('payment_intership_offline', array('amount', 'cus_id', 'payment_inter_off_id'))
            ->where('payment_intership_offline.cus_id = ?', $data['cus_id'])
            ->order('payment_inter_off_id desc')
            ->limit(1);
        $dataInterAmount = $db->fetchRow($checkquery);

        if ($dataInterAmount != null && $dataInterAmount['amount'] != null) {
            $data['inter_amount'] = $dataInterAmount['amount'];
        } else {
            $data['inter_amount'] = null;
        }

        echo json_encode($data);
        exit;
    }

    /* show form add new customer*/
    public function addAction()
    {
        //$this->_helper->layout('homelayout')->disableLayout();
    }

    /*validate phone and id card number and fullname*/
    public function validateAction()
    {
        $this->_helper->layout('homelayout')->disableLayout();
        $customer = new Default_Model_Customer();
        $type = $this->getRequest()->getParam('type');
        $data = $this->getRequest()->getParam('data');

        if ($type == 'username') {
            echo $customer->validateUsernameCustomer($type, $data);
            exit;
        }

        $result = $customer->validateCustomer($type, $data);
        $this->view->result = $result;

        if ($result != null && $result['cus_cellphone'] != null) {
            // echo $result['cus_cellphone'];
            echo json_encode($result);
        }
        exit;
    }

    /* change password for login user*/
    public function changepassAction()
    {
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();

        $username = $this->identity->user_username;
        $adminuser = new Default_Model_UserAdmin();

        $data = $adminuser->getUserAdminByUsername($username);

        $this->view->data = $data;
    }

    /*load list customer*/
    public function listcustomerAction()
    {
        $customer = new Default_Model_Customer();
        $data = $customer->loadCustomers();
        $this->view->data = $data;
    }

    public function listcustomerdatatableAction()
    {
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        //$search = $this->getRequest()->getParam('search');

        $dataSearch = $this->getRequest()->getParam('search');

        $searchValue = '';
        if ($dataSearch != null && sizeof($dataSearch) > 0) {
            $searchValue = $dataSearch['value'];
        }

        $results = array(
        );

        $customer = new Default_Model_Customer();

        $data = $customer->loadCustomersFilter($searchValue, $start, $length);
        if ($data != null && sizeof($data)) {
            foreach ($data as $cus) {
                $status = '';
                if ($cus['cus_status'] == 0) {
                    $status = 'Ngưng hoạt động';
                } else {
                    $status = 'Hoạt động';
                }

                $dateBirthdate = ($cus['cus_birthday'] != null && $cus['cus_birthday'] != ''
                    && $cus['cus_birthday'] != '1900-01-01 00:00:00') ? date('d/m/Y', strtotime($cus['cus_birthday'])) : '';

                array_push($results, array(
                    'cus_id' => $cus['cus_id'],
                    'cus_name' => $cus['cus_firstname'] . ' ' . $cus['cus_lastname'],
                    'cus_lawyer_number' => $cus['cus_lawyer_number'],
                    'cus_sex' => trim($cus['cus_sex']),
                    'cus_identity_card' => $cus['cus_identity_card'],
                    'cus_identity_place' => $cus['cus_identity_place'],
                    'cus_cellphone' => $cus['cus_cellphone'],
                    'cus_birthday' => $dateBirthdate,
                    'cus_status_text' => $status,
                    'cus_status' => $cus['cus_status'],
                    'deleted_online' => $cus['deleted_online'],
                ));
            }
        }

        $total = count($customer->loadCustomersFilter($searchValue, '', ''));

        $json_data = array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($total),
            "recordsFiltered" => intval($total),
            "data" => $results,
        );

        echo json_encode($json_data);
        exit;

    }

    /* change password for customer*/
    public function changepasswordAction()
    {
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();

        $username = $this->identity->user_username;
        $adminuser = new Default_Model_UserAdmin();

        $data = $adminuser->getUserAdminByUsername($username);

        $this->view->data = $data;
    }

}
