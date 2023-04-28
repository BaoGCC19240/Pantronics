<?php
    // Load PHPExcel library
    require_once '../PhpSpeadsheet/vendor/autoload.php';

    // Create new PHPExcel object
    $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Your Name")
                                     ->setLastModifiedBy("Your Name")
                                     ->setTitle("Product Data")
                                     ->setSubject("Product Data")
                                     ->setDescription("Product Data")
                                     ->setKeywords("Product Data")
                                     ->setCategory("Product Data");

    // Add data to worksheet
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'ID')
                ->setCellValue('B1', 'Name')
                ->setCellValue('C1', 'Description')
                ->setCellValue('D1', 'Price')
                ->setCellValue('E1', 'Quantity')
                ->setCellValue('F1', 'Category ID');

    // Connect to MySQL database
    include_once('connection.php');

    // Select all products from Product table
    $sql = "SELECT * FROM Product";
    $result = mysqli_query($conn, $sql);

    // Loop through all rows and add product data to worksheet
    $i = 2;
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)){
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $row['id'])
                        ->setCellValue('B'.$i, $row['name'])
                        ->setCellValue('C'.$i, $row['description'])
                        ->setCellValue('D'.$i, $row['price'])
                        ->setCellValue('E'.$i, $row['quantity'])
                        ->setCellValue('F'.$i, $row['category_id']);
            $i++;
        }
    }

    // Set active sheet index to the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Set headers to force download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="product_data.xlsx"');
    header('Cache-Control: max-age=0');

    // Write to Excel file
    $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
    $objWriter->save('php://output');
    exit;
?>
