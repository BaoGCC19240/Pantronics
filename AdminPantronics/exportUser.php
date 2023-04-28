<?php
    // Load PHPExcel library
    require_once '../PhpSpeadsheet/vendor/autoload.php';

    // Create new PHPExcel object
    $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Your Name")
                                     ->setLastModifiedBy("Your Name")
                                     ->setTitle("User Data")
                                     ->setSubject("User Data")
                                     ->setDescription("User Data")
                                     ->setKeywords("User Data")
                                     ->setCategory("User Data");

    // Add data to worksheet
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'ID')
                ->setCellValue('B1', 'Username')
                ->setCellValue('C1', 'Email')
                ->setCellValue('D1', 'Address')
                ->setCellValue('E1', 'Phone');

    // Connect to MySQL database
    include_once('connection.php');

    // Select all users from User table
    $sql = "SELECT * FROM User where password!='null'";
    $result = mysqli_query($conn, $sql);

    // Loop through all rows and add user data to worksheet
    $i = 2;
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)){
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $row['id'])
                        ->setCellValue('B'.$i, $row['username'])
                        ->setCellValue('C'.$i, $row['email'])
                        ->setCellValue('D'.$i, $row['address'])
                        ->setCellValue('E'.$i, $row['phone']);
            $i++;
        }
    }

    // Set active sheet index to the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Set headers to force download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="user_data.xlsx"');
    header('Cache-Control: max-age=0');

    // Write to Excel file
    $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
    $objWriter->save('php://output');
    exit;
?>
