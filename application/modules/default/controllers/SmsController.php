<?php

// Fee controller
class SmsController extends Zend_Controller_Action
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
        $smsModel = new Default_Model_Sms();
        $sms = $smsModel->fetchAll();
        $dateEmpty = '1900-01-01 00:00:00';
        if($sms != null && sizeof($sms)){
            foreach($sms as $key => $value)
            {
                $sms[$key]['sms_datesend'] = ($value['sms_datesend'] != null && $value['sms_datesend'] != '' &&
                $value['sms_datesend'] != $dateEmpty) ? date('d/m/Y',strtotime($value['sms_datesend'])) :'';
            }
        }
        $this->view->sms = $sms;
    }

    public function addAction()
    {
        $modelGroups = new Default_Model_Group();
        $groups = $modelGroups->fetchAll();
        $this->view->groups = $groups;
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($data['type_sms'] == 'send_mobile') {
                $customerModel = new Default_Model_Customer();
                $customer = $customerModel->fetchRow('cus_cellphone = 0' . substr($data['person_mobile'],2,strlen($data['person_mobile'])));
                $smsModel = new Default_Model_Sms();
                if(empty($customer->cus_id)){
                    $this->_redirect('/sms/add');
                }
                $idSms = $smsModel->insert([
                    'sms_content' => $data['sms_content'],
                    'sms_status' => 0,
                    'sms_datesend' => date('Y-m-d H:i:s'),
                    'sms_type' => 0,
                    'customer_id' => $customer->cus_id
                ]);
                $result = $this->sendSMS($data['sms_content'], [$data['person_mobile']]);
                if (!empty($result)) {
                    if ($result->return->result) {
                        $smsModel->update([
                            'sms_status' => 1,
                        ],'sms_id = '.$idSms);
                    }
                }
                $this->_redirect('/sms');
            } else {
                $modelPersonSms = new Default_Model_PersonSms();
                $persons = $modelPersonSms->fetchAll('group_id = ' . $data['group_id']);
                foreach ($persons as $index => $person) {
                    $mobiles = array();
                    $smsModel = new Default_Model_Sms();
                    $idSms = $smsModel->insert([
                        'sms_content' => $data['sms_content'],
                        'sms_status' => 0,
                        'sms_datesend' => date('Y-m-d H:i:s'),
                        'sms_type' => 1,
                        'person_id' => $person->person_id
                    ]);
                    $mobiles[] = $person->person_mobile;
                    $result = $this->sendSMS($data['sms_content'], $mobiles);
                    if (!empty($result)) {
                        if ($result->return->result) {
                            $smsModel->update([
                                'sms_status' => 1,
                            ],'sms_id = '.$idSms);
                        }
                    }
                }
                $this->_redirect('/sms');
            }
        }
    }

    public function sendSMS($sms_content = '', $mobiles = array())
    {
        $obj = new SmsBrandnameProvider();
        $response = null;
        foreach ($mobiles as $index => $value) {
            $response = $obj->sendBulkSms($sms_content, $value);
        }
        return $response;
    }
}

class SmsBrandnameProvider
{


    const SERVICE_URI = 'http://125.235.4.202:8998/bulkapi?wsdl';

    const TEST_USER = 'smsbrand_vthcm';
    const TEST_PASS = '123456aA@';
    const TEST_CPCODE = 'VTHCM';
    const TEST_ALIAS = 'Bulksmstest';

    /**
     * Function to handle SMS Send operation
     * @param <String> $message
     * @param <String> $toNumbers One number
     */
    public function sendBulkSms($message, $toNumbers)
    {
        $client = new SoapClient(self::SERVICE_URI);
        $params = array("User" => self::TEST_USER, "Password" => self::TEST_PASS, "CPCode" => self::TEST_CPCODE, "RequestID" => "1", "UserID" => $toNumbers, "ReceiverID" => $toNumbers, "ServiceID" => self::TEST_ALIAS, "CommandCode" => "bulksms", "Content" => $message, "ContentType" => "0");
        $response = $client->__soapCall("wsCpMt", array($params));
        return $response;
    }
}