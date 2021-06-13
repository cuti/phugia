<?php

class Admin_Model_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'user';
    protected $_primary = 'user_id';

    public function getUserByUserId($userId)
    {
        $where = $this->getAdapter()->quoteInto('user_id = ?', $userId);
        $row = $this->fetchRow($where);
        return $row;
    }

    public function getUserByUsername($username)
    {
        $where = $this->getAdapter()->quoteInto('user_username = ?', $username);
        $row = $this->fetchRow($where);
        return $row;
    }

    public function loadUser()
    {
        $sql = 'SELECT u.user_id
                     , u.user_fullname
                     , u.user_display_name
                     , u.user_username
                     , u.user_department_id
                     , u.user_email
                     , u.user_active
                     , u.user_created
                     , u.user_created_by_user_id
                     , u.user_last_updated
                     , u.user_last_updated_by_user_id
                     , dep.dep_name AS user_department
                     , uc.user_username AS user_created_by_username
                     , um.user_username AS user_last_updated_by_username
                     , r.role_id
                     , r.role_name
                  FROM [user] u
                       LEFT JOIN department dep ON u.user_department_id = dep.dep_id
                       LEFT JOIN [user] uc ON u.user_created_by_user_id = uc.user_id
                       LEFT JOIN [user] um ON u.user_last_updated_by_user_id = um.user_id
                       LEFT JOIN user_role ur ON u.user_id = ur.ur_user_id
                       LEFT JOIN [role] r ON ur.ur_role_id = r.role_id
                 WHERE u.user_deleted = 0
              ORDER BY u.user_username ASC';

        $stm = $this->getAdapter()->query($sql);
        $result = $stm->fetchAll();

        return $result;
    }

    /**
     * Insert user to DB.
     *
     * @param  array $data  Data row.
     * @return bool         Insert result.
     */
    public function insertUser($data, $roleId)
    {
        try {
            if (!$this->isUserExists($data['user_username'])) {
                $data['user_password'] = MD5($data['user_password']);
                $data['user_created'] = date('Y-m-d H:i:s');
                $data['user_created_by_user_id'] = $this->currentUserId();
                $data['user_deleted'] = 0;

                $userId = $this->insert($data);

                $sql = 'INSERT INTO user_role(ur_user_id, ur_role_id)
                             VALUES (?, ?)';
                $this->getAdapter()->query($sql, array($userId, $roleId));

                return $userId;
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
     * @param  string $userId   User id.
     * @param  array  $data     Data row.
     * @return bool             Update result.
     */
    public function updateUser($userId, $data, $roleId)
    {
        try {
            if (!$this->isUserExists($data['user_username'], $userId)) {
                $data['user_last_updated'] = date('Y-m-d H:i:s');
                $data['user_last_updated_by_user_id'] = $this->currentUserId();

                $where = $this->getAdapter()->quoteInto('user_id = ?', $userId);

                $this->update($data, $where);

                $sql = 'UPDATE user_role
                           SET ur_role_id = ?
                         WHERE ur_user_id = ?';
                $this->getAdapter()->query($sql, array($roleId, $userId));

                $logger = new Admin_Model_LogAdministration();
                $logger->writeLog('Cập nhật thông tin người dùng, username: ' . $data['user_username']);

                return 1;
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
     * @param  string $userId   User id to delete.
     * @param  string $username Username to delete.
     * @param  string $email    User's email to delete'.
     * @return int              The number of rows deleted.
     */
    public function deleteUser($userId, $username, $email)
    {
        try {
            $adapter = $this->getAdapter();

            $affectedCount = $this->update(
                array(
                    'user_deleted' => 1,
                ),
                array(
                    $adapter->quoteInto('user_id = ?', $userId),
                    $adapter->quoteInto('user_username = ?', $username),
                    $adapter->quoteInto('user_email = ?', $email),
                )
            );

            $logger = new Admin_Model_LogAdministration();
            $logger->writeLog('Xóa người dùng, username: ' . $username);

            return $affectedCount;
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Change user status.
     *
     * @param  string $userId   User id.
     * @return bool             Update result.
     */
    public function changeStatusUser($userId)
    {
        try {
            $where = $this->getAdapter()->quoteInto('user_id = ?', $userId);

            $affectedCount = $this->update(
                array('user_active' => new Zend_Db_Expr('1 - user_active')),
                $where
            );

            $userObj = $this->getUserByUserId($userId);

            $logger = new Admin_Model_LogAdministration();
            $logger->writeLog('Đổi trạng thái người dùng, username: ' . $userObj['user_username']);

            return $affectedCount;
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Change user status.
     *
     * @param  string $roleId       Role id.
     * @return bool                 Update result.
     */
    public function setInactiveForUserByRoleId($roleId)
    {
        try {
            $sql = "UPDATE [user]
                       SET user_active = 0,
                           user_last_updated = GETDATE(),
                           user_last_updated_by_user_id = ?
                     WHERE user_id IN (SELECT ur_user_id
                                         FROM user_role
                                        WHERE ur_role_id = ?)";

            $bind = array(
                $this->currentUserId(),
                $roleId
            );

            $this->getAdapter()->query($sql, $bind);

            $roleObj = (new Admin_Model_Role())->getRoleByRoleId($roleId);

            $logger = new Admin_Model_LogAdministration();
            $logger->writeLog('Xóa kích hoạt tất cả người dùng của nhóm: ' . $roleObj['role_name']);

            return 1;
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

    /**
     * Get current logged in user_id
     */
    private function currentUserId()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $identity['user_id'];
    }
}
