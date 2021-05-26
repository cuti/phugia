<?php

class Default_Model_Customer extends Zend_Db_Table_Abstract
{
    protected $_name = 'customer';
    protected $_primary = 'cus_id';

    public function loadCustomer()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $customerFields = array(
            'cus_id',
            'cus_code',
            'cus_name',
            'cus_address',
            'cus_tax_code',
            'cus_phone',
            'cus_mobile',
            'cus_fax',
            'cus_email',
            'cus_website',
            'cus_citizen_identity_card_number',
            'cus_citizen_identity_card_date',
            'cus_citizen_identity_card_issued_by',
            'cus_payment_terms',
            'cus_owed_days',
            'cus_max_owed',
            'cus_staff',
            'cus_staff_name',
            'cus_bank_account',
            'cus_bank_name',
            'cus_bank_branch_name',
            'cus_bank_city_id',
            'cus_country_id',
            'cus_city_id',
            'cus_district_id',
            'cus_ward_id',
            'cus_title',
            'cus_contact_person',
            'cus_contact_position',
            'cus_contact_mobile1',
            'cus_contact_mobile2',
            'cus_contact_phone',
            'cus_contact_email',
            'cus_contact_address',
            'cus_delivery_location',
            'cus_is_organization',
            'cus_is_supplier',
            'cus_active',
            'cus_created',
            'cus_created_by_user_id',
            'cus_last_updated',
            'cus_last_updated_by_user_id',
        );

        $select->from(array('cus' => 'customer'), $customerFields)
            ->joinLeft(array('bcity' => 'city'), 'cus.cus_bank_city_id = bcity.city_id', array('cus_bank_city_name' => 'city_name'))
            ->joinLeft(array('ct' => 'country'), 'cus.cus_country_id = ct.country_id', array('cus_country_name' => 'country_name'))
            ->joinLeft(array('ccity' => 'city'), 'cus.cus_city_id = ccity.city_id', array('cus_city_name' => 'city_name'))
            ->joinLeft(array('dis' => 'district'), 'cus.cus_district_id = dis.district_id', array('cus_district_name' => 'district_name'))
            ->joinLeft('ward', 'cus.cus_ward_id = ward.ward_id', array('cus_ward_name' => 'ward_name'))
            ->joinLeft(array('uc' => 'user'), 'cus.cus_created_by_user_id = uc.user_id', array('cus_created_by_username' => 'user_username'))
            ->joinLeft(array('um' => 'user'), 'cus.cus_last_updated_by_user_id = um.user_id', array('cus_last_updated_by_username' => 'user_username'))
            ->where('cus_deleted = 0')
            ->order('cus_code ASC');

        $customers = $db->fetchAll($select);

        if (count($customers) > 0) {
            $this->appendCustomerTypes($customers);
        }

        return $customers;
    }

    /**
     * Import customers to DB.
     *
     * @param  array $data          Array of rows, each row is array of columns.
     * @param  string $username     Current username.
     * @return object               Import result.
     */
    public function importCustomer($data, $username)
    {
        $dataCount = count($data);
        $cusCodeExists = array();
        $cusCodeError = array();
        $successCount = 0;

        for ($i = 0; $i < $dataCount; $i++) {
            if ($this->isCustomerExists($data[$i][0])) {
                array_push($cusCodeExists, $data[$i][0]);
            } else {
                try {
                    $city = new Default_Model_City();
                    $country = new Default_Model_Country();
                    $district = new Default_Model_District();
                    $ward = new Default_Model_Ward();
                    $user = new Default_Model_User();

                    $bank_city_id = null;
                    $country_id = null;
                    $cus_city_id = null;
                    $district_id = null;
                    $ward_id = null;
                    $userObj = $user->getUserByUsername($username);

                    if ($data[$i][21]) {
                        $bank_city_id = $city->getCityIdByName(trim($data[$i][21]));
                    }

                    if ($data[$i][22]) {
                        $country_id = $country->getCountryIdByName(trim($data[$i][22]));
                    }

                    if ($data[$i][23]) {
                        $cus_city_id = $city->getCityIdByName(trim($data[$i][23]));
                    }

                    if ($data[$i][24]) {
                        $district_id = $district->getDistrictIdByName(trim($data[$i][24]));
                    }

                    if ($data[$i][25]) {
                        $ward_id = $ward->getWardIdByName(trim($data[$i][25]));
                    }

                    if ($userObj) {
                        $userId = $userObj['user_id'];
                    }

                    $importRow = array(
                        'cus_code' => strtoupper($data[$i][0]),
                        'cus_name' => $data[$i][1],
                        'cus_address' => $data[$i][2],
                        'cus_tax_code' => $data[$i][4],
                        'cus_phone' => $data[$i][5],
                        'cus_mobile' => $data[$i][6],
                        'cus_fax' => $data[$i][7],
                        'cus_email' => $data[$i][8],
                        'cus_website' => $data[$i][9],
                        'cus_citizen_identity_card_number' => $data[$i][10],
                        'cus_citizen_identity_card_date' => $data[$i][11],
                        'cus_citizen_identity_card_issued_by' => $data[$i][12],
                        'cus_payment_terms' => $data[$i][13],
                        'cus_owed_days' => $data[$i][14],
                        'cus_max_owed' => $data[$i][15],
                        'cus_staff' => $data[$i][16],
                        'cus_staff_name' => $data[$i][17],
                        'cus_bank_account' => $data[$i][18],
                        'cus_bank_name' => $data[$i][19],
                        'cus_bank_branch_name' => $data[$i][20],
                        'cus_bank_city_id' => $bank_city_id,
                        'cus_country_id' => $country_id,
                        'cus_city_id' => $cus_city_id,
                        'cus_district_id' => $district_id,
                        'cus_ward_id' => $ward_id,
                        'cus_title' => $data[$i][26],
                        'cus_contact_person' => $data[$i][27],
                        'cus_contact_position' => $data[$i][28],
                        'cus_contact_mobile1' => $data[$i][29],
                        'cus_contact_mobile2' => $data[$i][30],
                        'cus_contact_phone' => $data[$i][31],
                        'cus_contact_email' => $data[$i][32],
                        'cus_contact_address' => $data[$i][33],
                        'cus_delivery_location' => $data[$i][34],
                        'cus_is_organization' => $data[$i][35] === 'Tổ chức' ? 1 : 0,
                        'cus_is_supplier' => $data[$i][36],
                        'cus_active' => !$data[$i][37],
                        'cus_created' => date('Y-m-d H:i:s'),
                        'cus_created_by_user_id' => $userId,
                        'cus_deleted' => 0,
                    );

                    $cus_id = $this->insert($importRow);

                    if ($data[$i][3]) {
                        $cusTypeCodes = explode(';', $data[$i][3]);

                        if (count($cusTypeCodes) > 0) {
                            $cusType = new Default_Model_CustomerType();
                            $cusCustomerType = new Default_Model_CustomerCustomerType();

                            foreach ($cusTypeCodes as $code) {
                                $cusTypeId = $cusType->getCustomerTypeIdByCode(strtoupper(trim($code)));

                                if ($cusTypeId) {
                                    $cusCustomerType->insertNew($cus_id, $cusTypeId);
                                }
                            }
                        }
                    }

                    $successCount++;
                } catch (Exception $err) {
                    array_push($cusCodeError, $data[$i][0]);
                }
            }
        }

        $status = 0; // All error

        if ($successCount === $dataCount) {
            $status = 1; // All success
        } else if ($successCount > 0) {
            $status = 2; // Partial success
        }

        return array(
            'customerCodeExists' => $cusCodeExists,
            'customerCodeError' => $cusCodeError,
            'successCount' => $successCount,
            'status' => $status,
        );
    }

    /**
     * Insert customers to DB.
     *
     * @param  array $data              Data row.
     * @param  array $customerTypes     Customer type ids.
     * @param  string $username         Current username.
     * @return bool                     Insert result.
     */
    public function insertCustomer($data, $customerTypes, $username)
    {
        try {
            if (!$this->isCustomerExists($data['cus_code'])) {
                $user = new Default_Model_User();
                $userObj = $user->getUserByUsername($username);

                if ($userObj) {
                    $userId = $userObj['user_id'];
                }

                $data['cus_created'] = date('Y-m-d H:i:s');
                $data['cus_created_by_user_id'] = $userId;
                $data['cus_deleted'] = 0;
                $cus_id = $this->insert($data);

                if (count($customerTypes) > 0) {
                    $cusCustomerType = new Default_Model_CustomerCustomerType();

                    foreach ($customerTypes as $cusTypeId) {
                        $cusCustomerType->insertNew($cus_id, $cusTypeId);
                    }
                }

                return $cus_id;
            } else {
                return 'cus_code';
            }
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Update customer information.
     *
     * @param  string $cusId            Customer id.
     * @param  array $data              Data row.
     * @param  array $customerTypes     Customer type ids.
     * @param  string $username         Current username.
     * @return bool                     Update result.
     */
    public function updateCustomer($cusId, $data, $customerTypes, $username)
    {
        try {
            if (!$this->isCustomerExists($data['cus_code'], $cusId)) {
                $user = new Default_Model_User();
                $userObj = $user->getUserByUsername($username);

                if ($userObj) {
                    $userId = $userObj['user_id'];
                }

                $data['cus_last_updated'] = date('Y-m-d H:i:s');
                $data['cus_last_updated_by_user_id'] = $userId;
                $affectedCount = $this->update($data, 'cus_id = ' . $cusId);

                $cusCustomerType = new Default_Model_CustomerCustomerType();
                $cusCustomerType->deleteByCusId($cusId);

                if (count($customerTypes) > 0) {
                    foreach ($customerTypes as $cusTypeId) {
                        $cusCustomerType->insertNew($cusId, $cusTypeId);
                    }
                }

                return $affectedCount;
            } else {
                return 'cus_code';
            }
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Delete customer.
     *
     * @param  string $cusId      Customer id.
     * @param  string $cusCode    Customer code.
     * @param  string $cusName    Customer name.
     * @param  string $username   Current username.
     * @return int                The number of rows deleted.
     */
    public function deleteCustomer($cusId, $cusCode, $cusName, $username)
    {
        try {
            $user = new Default_Model_User();
            $userObj = $user->getUserByUsername($username);

            if ($userObj) {
                $userId = $userObj['user_id'];
            }

            // Also delete data in CUSTOMER__CUSTOMER_TYPE b/c of delete cascade
            $affectedCount = $this->update(
                array(
                    'cus_deleted' => 1
                ),
                array(
                    'cus_id = ' . $cusId,
                    "cus_code = '" . $cusCode . "'",
                    "cus_name = N'" . $cusName . "'",
                )
            );

            $logger = new Default_Model_LogOperation();
            $logger->writeLog('Xóa khách hàng (Mã KH: ' . $cusCode . ', Tên KH: ' . $cusName, $username);

            return $affectedCount;
        } catch (Exception $err) {
            throw $err;
        }
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function appendCustomerTypes(&$customers)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $customerCount = count($customers);

        for ($i = 0; $i < $customerCount; $i++) {
            $select = new Zend_Db_Select($db);
            $select->from('customer__customer_type', array('customer_type_id'))
                ->joinInner('customer_type', 'customer__customer_type.customer_type_id = customer_type.customer_type_id', array('customer_type_name'))
                ->where('customer_id = ?', $customers[$i]['cus_id'])
                ->order('customer_type_name ASC');

            $customerTypes = $db->fetchAll($select);

            if (count($customerTypes) > 0) {
                $ct = array();
                $ctIds = array();

                foreach ($customerTypes as $cusType) {
                    array_push($ct, $cusType['customer_type_name']);
                    array_push($ctIds, $cusType['customer_type_id']);
                }

                $ct = implode(', ', $ct);
                $ctIds = implode(',', $ctIds);

                $customers[$i]['cus_types'] = $ct;
                $customers[$i]['cus_type_ids'] = $ctIds;
            } else {
                $customers[$i]['cus_types'] = '';
                $customers[$i]['cus_type_ids'] = '';
            }
        }
    }

    private function isCustomerExists($customerCode, $customerId = null)
    {
        if ($customerId === null) {
            $select = $this->select()->from('customer', array('cus_id'))->where('UPPER(cus_code) = UPPER(?)', $customerCode);
        } else {
            $select = $this->select()
                ->from('customer', array('cus_id'))
                ->where('UPPER(cus_code) = UPPER(?)', $customerCode)
                ->where('cus_id <> ?', $customerId);
        }

        $result = $this->fetchAll($select);

        return count($result) > 0;
    }
}
