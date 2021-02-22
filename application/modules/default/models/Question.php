<?php

class Default_Model_Question  extends Zend_Db_Table_Abstract{
    
    protected $_name = 'question';

    protected $_primary = 'question_id'; 

    protected $_sequence = true;

    //load list questions
    public function loadQuestions()
    {

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from("question", array(
            "question_id",
            "question_content",
            "status",
            "createddate",
            "createdby"))
        ->joinInner(
            "customers",
            "customers.cus_id = question.createdby",
           array("cus_firstname"=>"cus_firstname",
           "cus_lastname"=>"cus_lastname"
            ,"cus_cellphone"=>"cus_cellphone"
           ))   
       ->where("question.status = ?","0")
       ->order("question.createddate desc");

    
        $resultSet = $db->fetchAll($select); 
        return $resultSet;
    }

    //load question by id
    public function loadQuestionById($id)
    {

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from("question", array(
            "question_id",
            "question_content",
            "status",
            "createddate",
            "createdby"))
        ->joinInner(
            "customers",
            "customers.cus_id = question.createdby",
           array("cus_firstname"=>"cus_firstname",
           "cus_lastname"=>"cus_lastname"
            ,"cus_cellphone"=>"cus_cellphone"
           ))   
       ->where("question.question_id = ?",$id)
       ->order("question.createddate desc");

    
        $resultSet = $db->fetchRow($select); 
        return $resultSet;
    }

}