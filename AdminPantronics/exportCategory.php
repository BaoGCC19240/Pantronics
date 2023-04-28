<?php
    // Load PhpSpreadsheet library
    require_once '../PhpSpeadsheet/vendor/autoload.php';

    // Create new Spreadsheet object
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

    // Set document properties
    $spreadsheet->getProperties()->setCreator("Your Name")
                                 ->setLastModifiedBy("Your Name")
                                 ->setTitle("Category Data")
                                 ->setSubject("Category Data")
                                 ->setDescription("Category Data")
                                 ->setKeywords("Category Data")
                                 ->setCategory("Category Data");

    // Add data to worksheet
    $worksheet = $spreadsheet->getActiveSheet();
    $worksheet->setCellValue('A1', 'ID')
              ->setCellValue('B1', 'Category Name')
              ->setCellValue('C1', 'Description');

    // Connect to MySQL database
    include_once('connection.php');

    // Select all categories from ProductCategory table
    $sql = "SELECT * FROM ProductCategory";
    $result = mysqli_query($conn, $sql);

    // Loop through all rows and add category data to worksheet
    $i = 2;
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)){
            $worksheet->setCellValue('A'.$i, $row['id'])
                      ->setCellValue('B'.$i, $row['name'])
                      ->setCellValue('C'.$i, $row['description']);
            $i++;
        }
    }

    // Set active sheet index to the first sheet
    $spreadsheet->setActiveSheetIndex(0);

    // Set headers to force download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="category_data.xlsx"');
    header('Cache-Control: max-age=0');

    // Write to Excel file
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
?>
