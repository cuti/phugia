<?php

class Default_Model_Attachments  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'attachments';

    protected $_primary = 'attachment_id'; 

    protected $_sequence = true;

    public function loadAttachmentsByCusId($cus_id)
    {
        $select = $this->select()
                       ->from($this,array('attachment_id',
                       'attachment_name',
                       'createddate','updateddate','cus_id','type'))
                       ->where('cus_id = ?', $cus_id);       
                        
        $row = $this->fetchAll($select);
        return $row;
    }

}