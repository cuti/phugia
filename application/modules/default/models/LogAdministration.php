<?php

class Default_Model_LogAdministration extends Zend_Db_Table_Abstract
{
    protected $_name = 'log_administration';
    protected $_primary = 'log_id';

    /**
     * Write administration log.
     *
     * @param  string $action     User action.
     * @param  string $username   Current username.
     * @return mixed              Log id.
     */
    public function writeLog($action, $username)
    {
        try {
            $user = new Default_Model_User();
            $userObj = $user->getUserByUsername($username);

            if ($userObj) {
                $userId = $userObj['user_id'];
            } else {
                throw new Exception('Invalid username.');
            }

            $data = array(
                'log_action' => $action,
                'log_time' => date('Y-m-d H:i:s'),
                'log_user_id' => $userId,
            );

            $log_id = $this->insert($data);

            return $log_id;
        } catch (Exception $err) {
            throw $err;
        }
    }
}