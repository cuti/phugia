<?php

class Admin_Model_ContentPages  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'content_pages';

    protected $_primary = 'content_id'; 

    protected $_sequence = true;

    /** get page */
    public function loadContentPageById($id){
        $row = $this->fetchRow('content_id = ' .(int)$id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function loadContentPageByCode($code){
        $row = $this->fetchRow('code = ' .$code);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function loadContentPage()
    {
        $select = $this->select()
                       ->from($this,array(
                        'content_id'
                        ,'description'
                        ,'title'
                        ,'status'
                        ,'createddate'
                        ,'modifieddate'
                        ,'startdate'
                        ,'enddate'
                        ,'order'));                      
                        
        $row = $this->fetchAll($select);
        return $row;
    }

}