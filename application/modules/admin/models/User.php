<?php

class Admin_Model_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'user';
    protected $_primary = 'user_id';

    public function getUserByUsername($username)
    {
        $where = $this->getAdapter()->quoteInto('user_username = ?', $username);
        $row = $this->fetchRow($where);
        return $row;
    }

    public function loadUser()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $userFields = array(
            'user_id',
            'user_fullname',
            'user_display_name',
            'user_username',
            'user_department_id',
            'user_email',
            'user_active',
            'user_created',
            'user_created_by_user_id',
            'user_last_updated',
            'user_last_updated_by_user_id',
        );

        $select->from('user', $userFields)
            ->joinLeft(array('dep' => 'department'), '[user].user_department_id = dep.dep_id', array('user_department' => 'dep_name'))
            ->joinLeft(array('uc' => 'user'), '[user].user_created_by_user_id = uc.user_id', array('user_created_by_username' => 'user_username'))
            ->joinLeft(array('um' => 'user'), '[user].user_last_updated_by_user_id = um.user_id', array('user_last_updated_by_username' => 'user_username'))
            ->order('user_username ASC');

        return $db->fetchAll($select);
    }

    /**
     * Insert user to DB.
     *
     * @param  array $data          Data row.
     * @param  string $username     Current username.
     * @return bool                 Insert result.
     */
    public function insertUser($data, $username)
    {
        try {
            if (!$this->isUserExists($data['user_username'])) {
                $userObj = $this->getUserByUsername($username);

                if ($userObj) {
                    $userId = $userObj['user_id'];
                }

                $data['user_password'] = MD5($data['user_password']);
                $data['user_created'] = date('Y-m-d H:i:s');
                $data['user_created_by_user_id'] = $userId;

                return $this->insert($data);
            } else {
                return 'user_username';
            }
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Update user information.
     *
     * @param  string $userId       User id.
     * @param  array  $data         Data row.
     * @param  string $username     Current username.
     * @return bool                 Update result.
     */
    public function updateUser($userId, $data, $username)
    {
        try {
            if (!$this->isUserExists($data['user_username'], $userId)) {
                $userObj = $this->getUserByUsername($username);

                if ($userObj) {
                    $userIdUpdate = $userObj['user_id'];
                }

                $data['user_last_updated'] = date('Y-m-d H:i:s');
                $data['user_last_updated_by_user_id'] = $userIdUpdate;
                $where = $this->getAdapter()->quoteInto('user_id = ?', $userId);

                return $this->update($data, $where);
            } else {
                return 'user_username';
            }
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Delete user.
     *
     * @param  string $userId           User id to delete.
     * @param  string $username         Username to delete.
     * @param  string $departmentId     User's department id to delete'.
     * @param  string $curUsername      Current username.
     * @return int                      The number of rows deleted.
     */
    public function deleteUser($userId, $username, $email, $curUsername)
    {
        try {
            $userObj = $this->getUserByUsername($curUsername);

            if ($userObj) {
                $curUserId = $userObj['user_id'];
            }

            $adapter = $this->getAdapter();

            $affectedCount = $this->delete(
                array(
                    $adapter->quoteInto('user_id = ?', $userId),
                    $adapter->quoteInto('user_username = ?', $username),
                    $adapter->quoteInto('user_email = ?', $email),
                )
            );

            $logger = new Admin_Model_LogAdministration();
            $logger->writeLog('Xóa người dùng (Username: ' . $username, $curUsername);

            return $affectedCount;
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Change user status.
     *
     * @param  string $userId       User id.
     * @param  string $username     Current username.
     * @return bool                 Update result.
     */
    public function changeStatusUser($userId, $username)
    {
        try {
            $userObj = $this->getUserByUsername($username);

            if ($userObj) {
                $userIdUpdate = $userObj['user_id'];
            }

            $data['user_last_updated'] = date('Y-m-d H:i:s');
            $data['user_last_updated_by_user_id'] = $userIdUpdate;
            $where = $this->getAdapter()->quoteInto('user_id = ?', $userId);

            return $this->update(
                array('user_active' => new Zend_Db_Expr('1 - user_active')),
                $where
            );
        } catch (Exception $err) {
            throw $err;
        }
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function isUserExists($username, $userId = null)
    {
        if ($userId === null) {
            $select = $this->select()
                ->from('user', array('user_id'))
                ->where('UPPER(user_username) = UPPER(?)', $username);
        } else {
            $select = $this->select()
                ->from('user', array('user_id'))
                ->where('UPPER(user_username) = UPPER(?)', $username)
                ->where('user_id <> ?', $userId);
        }

        $result = $this->fetchAll($select);

        return count($result) > 0;
    }
}
