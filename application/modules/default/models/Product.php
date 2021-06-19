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
            'product_unit_measure',
            'product_manufacturer',
            'product_created',
            'product_created_by_staff_id',
            'product_last_updated',
            'product_last_updated_by_staff_id',
        );

        $select->from(array('prd' => 'product_catalog'), $productFields)
            ->joinLeft(array('sc' => 'staff'), 'prd.product_created_by_staff_id = sc.staff_id', array('product_created_by_username' => 'staff_username'))
            ->joinLeft(array('sm' => 'staff'), 'prd.product_last_updated_by_staff_id = sm.staff_id', array('product_last_updated_by_username' => 'staff_username'))
            ->where('product_deleted = 0')
            ->order('product_code ASC');

        return $db->fetchAll($select);
    }

    /**
     * Import products to DB.
     *
     * @param  array  $data         Array of rows, each row is array of columns.
     * @param  string $username     Current username.
     * @return object               Import result.
     */
    public function importProduct($data, $username)
    {
        $dataCount = count($data);
        $productCodeExists = array();
        $productCodeError = array();
        $successCount = 0;

        for ($i = 0; $i < $dataCount; $i++) {
            if ($this->isProductExists($data[$i][0])) {
                array_push($productCodeExists, $data[$i][0]);
            } else {
                try {
                    $staffModel = new Default_Model_Staff();
                    $staffObj = $staffModel->getStaffByUsername($username);

                    if ($staffObj) {
                        $staffId = $staffObj['staff_id'];
                    }

                    $importRow = array(
                        'product_code' => strtoupper($data[$i][0]),
                        'product_name' => $data[$i][1],
                        'product_unit_measure' => $data[$i][2],
                        'product_manufacturer' => $data[$i][3],
                        'product_created' => date('Y-m-d H:i:s'),
                        'product_created_by_staff_id' => $staffId,
                        'product_deleted' => 0,
                    );

                    $product_id = $this->insert($importRow);
                    $successCount++;
                } catch (Exception $err) {
                    array_push($productCodeError, $data[$i][0]);
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
            'productCodeExists' => $productCodeExists,
            'productCodeError' => $productCodeError,
            'successCount' => $successCount,
            'status' => $status,
        );
    }

    /**
     * Insert products to DB.
     *
     * @param  array  $data             Data row.
     * @param  string $username         Current username.
     * @return bool                     Insert result.
     */
    public function insertProduct($data, $username)
    {
        try {
            if (!$this->isProductExists($data['product_code'])) {
                $staffModel = new Default_Model_Staff();
                $staffObj = $staffModel->getStaffByUsername($username);

                if ($staffObj) {
                    $staffId = $staffObj['staff_id'];
                }

                $data['product_created'] = date('Y-m-d H:i:s');
                $data['product_created_by_staff_id'] = $staffId;
                $data['product_deleted'] = 0;
                $product_id = $this->insert($data);

                return $product_id;
            } else {
                return 'product_code';
            }
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Update product information.
     *
     * @param  string $productId        Product id.
     * @param  array  $data             Data row.
     * @param  string $username         Current username.
     * @return bool                     Update result.
     */
    public function updateProduct($productId, $data, $username)
    {
        try {
            if (!$this->isProductExists($data['product_code'], $productId)) {
                $staffModel = new Default_Model_Staff();
                $staffObj = $staffModel->getStaffByUsername($username);

                if ($staffObj) {
                    $staffId = $staffObj['staff_id'];
                }

                $data['product_last_updated'] = date('Y-m-d H:i:s');
                $data['product_last_updated_by_staff_id'] = $staffId;
                $affectedCount = $this->update($data, 'product_id = ' . $productId);

                return $affectedCount;
            } else {
                return 'product_code';
            }
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Delete product.
     *
     * @param  string $productId      Product id.
     * @param  string $productCode    Product code.
     * @param  string $productName    Product name.
     * @param  string $username       Current username.
     * @return int                    The number of rows deleted.
     */
    public function deleteProduct($productId, $productCode, $productName, $username)
    {
        try {
            $staffModel = new Default_Model_Staff();
            $staffObj = $staffModel->getStaffByUsername($username);

            if ($staffObj) {
                $staffId = $staffObj['staff_id'];
            }

            $affectedCount = $this->update(
                array(
                    'product_deleted' => 1
                ),
                array(
                    'product_id = ' . $productId,
                    "product_code = '" . $productCode . "'",
                    "product_name = N'" . $productName . "'",
                )
            );

            $logger = new Default_Model_LogOperation();
            $logger->writeLog('Xóa vật tư (Mã VT: ' . $productCode . ', Tên VT: ' . $productName, $username);

            return $affectedCount;
        } catch (Exception $err) {
            throw $err;
        }
    }

    // --------------- PRIVATE FUNCTIONS ---------------

    private function isProductExists($productCode, $productId = null)
    {
        if ($productId === null) {
            $select = $this->select()
                ->from('product_catalog', array('product_id'))
                ->where('UPPER(product_code) = UPPER(?)', $productCode)
                ->where('product_deleted = 0');
        } else {
            $select = $this->select()
                ->from('product_catalog', array('product_id'))
                ->where('UPPER(product_code) = UPPER(?)', $productCode)
                ->where('product_id <> ?', $productId)
                ->where('product_deleted = 0');
        }

        $result = $this->fetchAll($select);

        return count($result) > 0;
    }
}
