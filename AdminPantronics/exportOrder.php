<?php
// Load PhpSpreadsheet library
require_once '../PhpSpeadsheet/vendor/autoload.php';

// Create new Spreadsheet object
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator("Your Name")
                             ->setLastModifiedBy("Your Name")
                             ->setTitle("Order Data")
                             ->setSubject("Order Data")
                             ->setDescription("Order Data")
                             ->setKeywords("Order Data")
                             ->setCategory("Order Data");

// Add data to worksheet
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->setCellValue('A1', 'Order ID')
          ->setCellValue('B1', 'Customer Name')
          ->setCellValue('C1', 'Address')
          ->setCellValue('D1', 'Phone')
          ->setCellValue('E1', 'Product Name')
          ->setCellValue('F1', 'Quantity')
          ->setCellValue('G1', 'Price')
          ->setCellValue('H1', 'Total Amount');

// Connect to MySQL database
include_once('connection.php');

// Select all orders from Order table
$sql = "SELECT o.id as order_id, o.address, o.phone, c.username as customer_name, p.name as product_name, ind.quantity, ind.price, o.total 
FROM `invoice` o 
INNER JOIN user c ON o.user_id = c.id 
INNER JOIN invoicedetail ind ON o.id = ind.invoice_id 
INNER JOIN product p ON ind.product_id = p.id
";
$result = mysqli_query($conn, $sql);

// Loop through all rows and add order data to worksheet
$i = 2;
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)){
        $worksheet->setCellValue('A'.$i, $row['order_id'])
                  ->setCellValue('B'.$i, $row['customer_name'])
                  ->setCellValue('C'.$i, $row['address'])
                  ->setCellValue('D'.$i, $row['phone'])
                  ->setCellValue('E'.$i, $row['product_name'])
                  ->setCellValue('F'.$i, $row['quantity'])
                  ->setCellValue('G'.$i, $row['price'])
                  ->setCellValue('H'.$i, $row['total']);
        $i++;
    }
}

// Set active sheet index to the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Set headers to force download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="order_data.xlsx"');
header('Cache-Control: max-age=0');

// Write to Excel file
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
?>