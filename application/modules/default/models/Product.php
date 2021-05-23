<?php

class Default_Model_Product extends Zend_Db_Table_Abstract
{
    protected $_name = 'product_catalog';
    protected $_primary = 'product_id';

    public function loadProduct()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $productFields = array(
            'product_id',
            'product_code',
            'product_name',
            'product_unit_measure_id',
            'product_manufacturer',
        );

        $select->from(array('prod' => 'product'), $productFields)
            ->joinLeft(array('um' => 'unit_measure'), 'prod.product_unit_measure_id = um.ume_id', array('product_unit_name' => 'ume_name'))
            ->order('product_code ASC');

        return $db->fetchAll($select);
    }

    /**
     * Import products to DB.
     *
     * @param  array $data          Array of rows, each row is array of columns.
     * @param  string $username     Current username.
     * @return object               Import result.
     */
    public function importProduct($data, $username)
    {
        $dataCount = count($data);
        $cusCodeExists = array();
        $cusCodeError = array();
        $successCount = 0;

        for ($i = 0; $i < $dataCount; $i++) {
            if ($this->isProductExists($data[$i][0])) {
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
                    $product_city_id = null;
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
                        $product_city_id = $city->getCityIdByName(trim($data[$i][23]));
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
                        'product_code' => strtoupper($data[$i][0]),
                        'product_name' => $data[$i][1],
                        'product_address' => $data[$i][2],
                        'product_tax_code' => $data[$i][4],
                        'product_phone' => $data[$i][5],
                        'product_mobile' => $data[$i][6],
                        'product_fax' => $data[$i][7],
                        'product_email' => $data[$i][8],
                        'product_website' => $data[$i][9],
                        'product_citizen_identity_card_number' => $data[$i][10],
                        'product_citizen_identity_card_date' => $data[$i][11],
                        'product_citizen_identity_card_issued_by' => $data[$i][12],
                        'product_payment_terms' => $data[$i][13],
                        'product_owed_days' => $data[$i][14],
                        'product_max_owed' => $data[$i][15],
                        'product_staff' => $data[$i][16],
                        'product_staff_name' => $data[$i][17],
                        'product_bank_account' => $data[$i][18],
                        'product_bank_name' => $data[$i][19],
                        'product_bank_branch_name' => $data[$i][20],
                        'product_bank_city_id' => $bank_city_id,
                        'product_country_id' => $country_id,
                        'product_city_id' => $product_city_id,
                        'product_district_id' => $district_id,
                        'product_ward_id' => $ward_id,
                        'product_title' => $data[$i][26],
                        'product_contact_person' => $data[$i][27],
                        'product_contact_position' => $data[$i][28],
                        'product_contact_mobile1' => $data[$i][29],
                        'product_contact_mobile2' => $data[$i][30],
                        'product_contact_phone' => $data[$i][31],
                        'product_contact_email' => $data[$i][32],
                        'product_contact_address' => $data[$i][33],
                        'product_delivery_location' => $data[$i][34],
                        'product_is_organization' => $data[$i][35] === 'Tổ chức' ? 1 : 0,
                        'product_is_supplier' => $data[$i][36],
                        'product_active' => !$data[$i][37],
                        'product_created' => date('Y-m-d H:i:s'),
                        'product_created_by_user_id' => $userId,
                    );

                    $product_id = $this->insert($importRow);

                    if ($data[$i][3]) {
                        $cusTypeCodes = explode(';', $data[$i][3]);

                        if (count($cusTypeCodes) > 0) {
                            $cusType = new Default_Model_ProductType();
                            $cusProductType = new Default_Model_ProductProductType();

                            foreach ($cusTypeCodes as $code) {
                                $cusTypeId = $cusType->getProductTypeIdByCode(strtoupper(trim($code)));

                                if ($cusTypeId) {
                                    $cusProductType->insertNew($product_id, $cusTypeId);
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
            'productCodeExists' => $cusCodeExists,
            'productCodeError' => $cusCodeError,
            'successCount' => $successCount,
            'status' => $status,
        );
    }

    /**
     * Insert products to DB.
     *
     * @param  array $data              Data row.
     * @param  array $productTypes     Product type ids.
     * @param  string $username         Current username.
     * @return bool                     Insert result.
     */
    public function insertProduct($data, $productTypes, $username)
    {
        try {
            $user = new Default_Model_User();
            $userObj = $user->getUserByUsername($username);

            if ($userObj) {
                $userId = $userObj['user_id'];
            }

            $data['product_created'] = date('Y-m-d H:i:s');
            $data['product_created_by_user_id'] = $userId;
            $product_id = $this->insert($data);

            if (count($productTypes) > 0) {
                $cusProductType = new Default_Model_ProductProductType();

                foreach ($productTypes as $cusTypeId) {
                    $cusProductType->insertNew($product_id, $cusTypeId);
                }
            }

            return $product_id;
        } catch (Exception $err) {
            throw $err;
        }
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function isProductExists($productCode)
    {
        $select = $this->select()->from('product', array('product_id'))->where('UPPER(product_code) = UPPER(?)', $productCode);
        $result = $this->fetchAll($select);

        return count($result) > 0;
    }
}
