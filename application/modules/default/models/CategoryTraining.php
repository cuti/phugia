<?php

class Default_Model_CategoryTraining  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'category_training';

    protected $_primary = 'cat_train_id'; 

    protected $_sequence = true;

    public function loadCategoryTrainingByCatId($cat_id){
        $row = $this->fetchRow('cat_train_id = ' .(int) $cat_id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function loadCategoryTraining()
    {
        $select = $this->select()
                       ->from($this,array('cat_train_id','cat_train_name','cat_train_active','cat_train_number',
                    'cat_train_fromdate','cat_train_number','cat_train_address'));
                       //->where('cat_train_active = ?', '1');
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    public function loadCategoryTrainingActive()
    {
        $select = $this->select()
                       ->from($this,array('cat_train_id','cat_train_name','cat_train_active','cat_train_number',
                    'cat_train_fromdate','cat_train_number','cat_train_address','createdate'))
                    ->where('cat_train_active = ?', '1')
                    ->order('createdate DESC');
                        
        $row = $this->fetchAll($select);
        return $row;
    }

    public function loadCategoryTrainingActiveAndNotStarted()
    {
        $currentdate = new Zend_Date();
        $datecompare = $currentdate->toString('YYYY-MM-dd HH:mm:ss');

        $select = $this->select()
                       ->from($this,array('cat_train_id'
                       ,'cat_train_name','cat_train_active','cat_train_number',
                    'cat_train_fromdate','cat_train_number','cat_train_address','createdate'))
                    ->where('cat_train_active = ?', '1')
                    //->where('cat_train_fromdate > ?', $datecompare)
                    ->order('cat_train_id DESC');
                        
        $row = $this->fetchAll($select);
        return $row;
    }
}
