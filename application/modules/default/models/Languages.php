<?php

class Default_Model_Languages  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'languages';

    protected $_primary = 'language_id'; 

    protected $_sequence = true;  

    public function loadLanguageById($language_id){
        $row = $this->fetchRow('language_id = ' .(int) $language_id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function loadLanguages()
    {
        $select = $this->select()
                       ->from($this,array('language_id',
                       'language_name','status'
                       ));                     
                        
        $row = $this->fetchAll($select);
        return $row;
    }    
}