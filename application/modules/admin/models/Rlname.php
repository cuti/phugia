<?php

class Admin_Model_Rlname  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'rl_name';

    protected $_primary = 'rl_id'; 

    protected $_sequence = true;

    public function loadByRlId($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('rl_id = ' . $id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function loadListRlNameAssignUserId($user_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()
        ->from("rl_name",array(
        'rl_id'=>'rl_id',
        'rl_code'=>'rl_code',
        'rl_name'=>'rl_name',
        'rl_status'=>'rl_status'))
        ->joinInner(
            'acl',
            'acl.rl_id = rl_name.rl_id',
            array()) 
        ->where('rl_name.rl_status = ?',1)
        ->where('acl.user_id = ?',$user_id);                     
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    public function loadRlByCode($code){
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select
        ->from("rl_name",array(
        'rl_id'=>'rl_id',
        'rl_code'=>'rl_code',
        'rl_name'=>'rl_name',
        'rl_status'=>'rl_status'))
        ->where('rl_code = ?',$code);                     
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    public function loadRlnames()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select
        ->from("rl_name",array(
        'rl_id'=>'rl_id',
        'rl_code'=>'rl_code',
        'rl_name'=>'rl_name',
        'rl_status'=>'rl_status'));                     
                        
        $row = $db->fetchAll($select);
        return $row;
    }

    public function loadActiveRlnames()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select
        ->from("rl_name",array(
        'rl_id'=>'rl_id',
        'rl_code'=>'rl_code',
        'rl_name'=>'rl_name',
        'rl_status'=>'rl_status'))
        ->where('rl_status = ?',1);                     
                        
        $row = $db->fetchAll($select);
        return $row;
    }


}