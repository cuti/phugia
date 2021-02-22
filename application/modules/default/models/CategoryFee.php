<?php

class Default_Model_CategoryFee  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'category_fee';

    protected $_primary = 'category_fee_id'; 

    protected $_sequence = true;

    public function loadCategoryFee($type)
    {
        $select = $this->select()
                       ->from($this,array(
                        'category_fee_id',
                        'name',
                        'type',
                        'year',
                        'baseondocument',
                        'mooney',
                        // 'createddate',
                        // 'modifieddate',
                        'status'))
                       ->where('type = ?', $type)
                       ->order('year desc');       
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    public function loadCategoryFeeById($id){
        $db = Zend_Db_Table::getDefaultAdapter();
        // $select = new Zend_Db_Select($db);
        $query = $db->select()
            ->from("category_fee", array(
                'category_fee_id'
                ,'name'
                ,'mooney'
                ,'type'
                ,'year'
                ,'baseondocument'
                ,'status'
                ,'createddate'))           
            ->where('category_fee.category_fee_id = ?',$id);                     
        return $db->fetchRow($query);
    }

}