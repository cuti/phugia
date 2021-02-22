<?php

class Default_Model_Notification  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'notification';

    protected $_primary = 'notification_id'; 

    protected $_sequence = true;

    public function loadNotifications(){
        $select = $this->select()
            ->from($this,array(
                'notification_id',
            'title',
            'description',
            'activationdate',
            'expiredate',
            'createddate'))            
            ->order('createddate DESC');
                
        $row = $this->fetchAll($select);
        return $row;
    }

    /*get notification by id*/
    public function getNotificationById($id)
    {
        $row = $this->fetchRow('notification_id = '. $id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

}