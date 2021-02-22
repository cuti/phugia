<?php

class Default_Model_Course  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'course';

    protected $_primary = 'course_id'; 

    protected $_sequence = true;

    public function loadCourseById($course_id){
        $row = $this->fetchRow('course_id = ' .(int) $course_id);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }

    public function loadCourse()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->distinct()->from('course', array('course_name','course_id','course_status','course_startdate',
        'course_enddate','cat_train_id')) 
        ->joinLeft(
            'category_training',
            'category_training.cat_train_id = course.cat_train_id',
            array('cat_train_name'=>'cat_train_name'))       
        ->order('course.course_id');                        
        return $resultSet = $db->fetchAll($select);
    }
}
