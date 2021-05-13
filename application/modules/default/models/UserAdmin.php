<?php

class Default_Model_UserAdmin  extends Zend_Db_Table_Abstract{

    protected $_name = 'user_admin';

    protected $_primary = 'user_id';

    function num($username, $password)
    {

        $result = count($this->getAdapter()->fetchAll("select * from user where
        user_username='".$username."' and
        user_password='".$password."' and
        user_status=1 "));

        return $result;

    }

    public function getUserAdminByUsername($username)
    {
        $select = $this->select()
        ->from($this)
        ->where('user_username = ?', $username);
        $row = $this->fetchRow($select);
        if (!$row) {
            return null;
        }
        return $row->toArray();
    }




    // public function indexSearch($q)
    // {
    //     $select = $this->select()
    //                    ->from($this)
    //                    ->where('phone_number LIKE ?', '%' . $q . '%');

    //     $row = $this->fetchAll($select);
    //     return $row;
    // }


    // public function searchCustomer($q)
    // {
    //     $db = Zend_Db_Table::getDefaultAdapter();
    //     $select = new Zend_Db_Select($db);
    //     $select->from('acc_customer')
    //                 ->where('phone_number LIKE ?', '%' . $q . '%');

    //     // $db = Zend_Db_Table::getDefaultAdapter(); //set in my config file
    //     // $select = new Zend_Db_Select($db);
    //     // $select->from('trips', array('pickup_address', 'status')) //the array specifies which columns I want returned in my result set
    //     //     ->joinInner(
    //     //         'vehicle',
    //     //         'vehicle.id_vehicle = trips.id_vehicle',
    //     //         array()) //by specifying an empty array, I am saying that I don't care about the columns from this table
    //     //     ->where('phone_number_customer = ?', '0915928139');
    //     $resultSet = $db->fetchAll($select);



    //     //  $select = $this->select();
    //     // $adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
    //     // return $adapter;
    // }

    // public function getCustomer($phone_number)
    // {
    //     $phone_number = (int)$phone_number;
    //     //$select = $this->select() //select from usertable and memberdetail
    //     //->from(array('memberdetail', 'usertable')) //join memberdetail and usertable through memberid = username
    //     //->where('phone_number = ' . $phone_number or);
    //     $row = $this->fetchRow('phone_number = ' . $phone_number);
    //     if (!$row) {
    //         throw new Exception("Could not find row $phone_number");
    //     }
    //     return $row->toArray();
    // }

    // public function fetchPaginatorAdapter ()
    // {
    //     // $db = Zend_Db_Table::getDefaultAdapter(); //set in my config file
    //     // $select = new Zend_Db_Select($db);
    //     // $select->from('trips', array('pickup_address', 'status')) //the array specifies which columns I want returned in my result set
    //     //     ->joinInner(
    //     //         'vehicle',
    //     //         'vehicle.id_vehicle = trips.id_vehicle',
    //     //         array()) //by specifying an empty array, I am saying that I don't care about the columns from this table
    //     //     ->where('phone_number_customer = ?', '0915928139');
    //     // $resultSet = $db->fetchAll($select);
    //     // echo "<prev>";
    //     //         print_r($resultSet);
    //     // echo "</prev>";
    //     // add any filters which are set
    //     // if (count($filters) > 0) {
    //     //     foreach ($filters as $field => $filter) {
    //     //         $select->where($field . ' = ?', $filter);
    //     //     }
    //     // }
    //     // // add the sort field is it is set
    //     // if (null != $sortField) {
    //     //     $select->order($sortField);
    //     // }
    //     // create a new instance of the paginator adapter and return it
    //     $select = $this->select();
    //     $adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
    //     return $adapter;
    // }

    // public function deleteCustomer($id)
    // {
    //     $this->delete('phone_number =' . (int)$id);
    // }

    // public function updateCustomer($phone, $payment, $pass,$fullname)
    // {
    //     $data = array(
    //         'payment' => $payment,
    //         'pass' => $pass,
    //         'full_name'=> $fullname
    //     );
    //     $this->update($data, 'phone_number = '. (int)$phone);
    // }

}