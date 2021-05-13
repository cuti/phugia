<?php

class MailController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->_request->getBaseUrl();
        $this->view->sBasePath = $this->_request->getBaseUrl()."/library/FCKeditor/" ;
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
        $mailModel = new Default_Model_Mail();
        $mail = $mailModel->fetchAll();
        $dateEmpty = '1900-01-01 00:00:00';
        if($mail != null && sizeof($mail)){
            foreach($mail as $key => $value)
            {
                $mail[$key]['mail_datesend'] = ($value["mail_datesend"] != null && $value["mail_datesend"] != ''
                && $value["mail_datesend"] != $dateEmpty) ? date('d/m/Y',strtotime($value['mail_datesend'])) : '';
            }
        }
        $this->view->mail = $mail;
    }

    public function addAction()
    {
        $modelGroups = new Default_Model_Group();
        $groups = $modelGroups->fetchAll();
        $this->view->groups = $groups;
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($data['type_mail'] == 'send_email') {
                $customerModel = new Default_Model_Customer();
                $customer = $customerModel->fetchRow(['cus_email = ?' => $data['person_email']]);
                $mailModel = new Default_Model_Mail();
                if(empty($customer->cus_id)){
                    $this->_redirect('/mail/add');
                }
                $idMail = $mailModel->insert([
                    'mail_subject' => $data['mail_subject'],
                    'mail_content' => $data['mail_content'],
                    'mail_status' => 0,
                    'mail_datesend' => date('Y-m-d H:i:s'),
                    'mail_type' => 0,
                    'customer_id' => $customer->cus_id
                ]);
                $result = $this->sendMail($customer->cus_email, $data);
                if (!empty($result)) {
                    if ($result) {
                        $mailModel->update([
                            'mail_status' => 1,
                        ],'mail_id = '.$idMail);
                    }
                }
                $this->_redirect('/mail');
            } else {
                $modelPersonSms = new Default_Model_PersonSms();
                $persons = $modelPersonSms->fetchAll('group_id = ' . $data['group_id']);
                foreach ($persons as $index => $person) {
                    $mailModel = new Default_Model_Mail();
                    $idMail = $mailModel->insert([
                        'mail_subject' => $data['mail_subject'],
                        'mail_content' => $data['mail_content'],
                        'mail_status' => 0,
                        'mail_datesend' => date('Y-m-d H:i:s'),
                        'mail_type' => 1,
                        'person_id' => $person->person_id
                    ]);
                    $result = $this->sendMail($person->person_email, $data);
                    if (!empty($result)) {
                        if ($result) {
                            $mailModel->update([
                                'mail_status' => 1,
                            ],'mail_id = '.$idMail);
                        }
                    }
                }
                $this->_redirect('/mail');
            }
        }
    }

    public function sendMail($email = null,$data = []) {
        // $config = array(
        //     'ssl' => 'ssl',
        //     'auth'=>'login',
        //     'username'=>'doanluatsu.hochiminh',
        //     'password'=>'Matkhaucap1',
        //     'port' => 465
        // );
        $config = array(
            //'ssl' => 'tls',
            'auth'=>'login',
            'username'=>'info@hcba.vn',
            'password'=>'^CAXXG#etSWy',
            'port' => 587
        );
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/mail/');
        $html->assign('content',$data['mail_content']);

        $body = $html->render('_mail.phtml');

        $transport = new Zend_Mail_Transport_Smtp('mail.hcba.vn', $config);
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($body,'UTF-8');
        // $mail->setFrom('doanluatsu.hochiminh@gmail.com', 'Đoàn Luật Sư TP.Hồ Chí Minh');
        $mail->setFrom('info@hcba.vn', 'Đoàn Luật Sư TP.Hồ Chí Minh');
        $mail->addTo($email, $email);
        $mail->setSubject($data['mail_subject']);
        try {
            $mail->send($transport);
            return true;
        } catch (Exception $exception) {
            return false;
        }
        return false;
    }
}
