<?php

class Default_Model_CategoryFeeTraining  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'category_fee_training';

    protected $_primary = 'category_fee_training_id'; 

    protected $_sequence = true;

    public function loadCategoryFeeTrainingByCatId($cat_id){
        $row = $this->fetchRow('category_fee_training_id = ' .(int) $cat_id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function loadCategoryFeeTraining()
    {
        $select = $this->select()
                       ->from($this,array('category_fee_training_id','name','mooney'));
                       //->where('cat_train_active = ?', '1');
                        
        $row = $this->fetchAll($select);
        return $row;
    }
}
