<?php

class Admin_Model_LogAdministration extends Zend_Db_Table_Abstract
{
    protected $_name = 'log_administration';
    protected $_primary = 'log_id';

    /**
     * Write administration log.
     *
     * @param  string $action     User action.
     * @return mixed              Log id.
     */
    public function writeLog($action)
    {
        try {
            $staffId = Zend_Auth::getInstance()->getIdentity()['staff_id'];

            $data = array(
                'log_action' => $action,
                'log_time' => date('Y-m-d H:i:s'),
                'log_staff_id' => $staffId,
            );

            $log_id = $this->insert($data);

            return $log_id;
        } catch (Exception $err) {
            throw $err;
        }
    }
}