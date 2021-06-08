<?php

/**
 * Helper class to read and write Excel files.
 */
class ExcelReaderWriter
{
    /**
     * Read data from Excel file, only first sheet
     *
     * @param  string $fileFullPath   Absolute path to Excel file.
     * @param  string $fileExt        File extension, include dot, default to .xlsx (Excel 2007 format).
     * @param  int    $sheetIndex     Index of sheet to read, default to 0.
     * @return array                  Array of rows, each row is array of columns
     */
    public static function read($fileFullPath, $fileExt = '.xlsx', $sheetIndex = 0)
    {
        $fileType = $fileExt === '.xls' ? 'Excel5' : 'Excel2007';

        $objReader = PHPExcel_IOFactory::createReader($fileType);
        $objReader->setReadDataOnly(true);
        // $objReader->setLoadSheetsOnly(array('Khach_hang'));

        $objPHPExcel = $objReader->load($fileFullPath);
        $sheet = $objPHPExcel->getSheet($sheetIndex);
        $lastRow = $sheet->getHighestRow(); // Row start from 1
        $lastColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn()); // Column start from 0
        $data = array();

        for ($row = 2; $row <= $lastRow; ++$row) { // Start from 2 b/c exclude header row
            $dataRow = array();

            for ($col = 0; $col <= $lastColumn; ++$col) {
                $cellValue = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                array_push($dataRow, $cellValue);
            }

            array_push($data, $dataRow);
        }

        // Release memory
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);

        return $data;
    }
}
