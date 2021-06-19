<?php

class Default_Model_LogOperation extends Zend_Db_Table_Abstract
{
    protected $_name = 'log_operation';
    protected $_primary = 'log_id';

    /**
     * Write operation log.
     *
     * @param  string $action     User action.
     * @param  string $username   Current username.
     * @return mixed              Log id.
     */
    public function writeLog($action, $username)
    {
        try {
            $staffModel = new Default_Model_Staff();
            $staffObj = $staffModel->getStaffByUsername($username);

            if ($staffObj) {
                $staffId = $staffObj['staff_id'];
            } else {
                throw new Exception('Invalid username.');
            }

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