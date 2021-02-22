<?php

class Default_Model_Group extends Zend_Db_Table_Abstract{
    
    protected $_name = 'group';

    protected $_primary = 'group_id';

    protected $_sequence = true;

    public function loadGroups(){
        $select = $this->select()
        ->from($this,array(
        'group_id'
        ,'group_name'
        ,'group_creatdate'
        ,'group_update'
        ,'group_active'
        ,'user_id'
        ,'customers'
        ));             
                
        $row = $this->fetchAll($select);
        return $row;
    }

    public function loadGroupById($id){
        $select = $this->select()
        ->from($this,array(
        'group_id'
        ,'group_name'
        ,'group_creatdate'
        ,'group_update'
        ,'group_active'
        ,'user_id'
        ,'customers'
        ))
        ->where('group_id = ?',$id);      
                
        $row = $this->fetchRow($select);
        return $row;
    }


}    