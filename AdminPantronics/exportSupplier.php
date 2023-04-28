<?php
// Kết nối tới cơ sở dữ liệu
include_once('connection.php');

require_once '../PhpSpeadsheet/vendor/autoload.php';

// Tạo đối tượng Spreadsheet mới
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

// Lấy danh sách các nhà cung cấp từ cơ sở dữ liệu
$sql = 'SELECT * FROM supplier';
$result = mysqli_query($conn, $sql);

// Thêm tiêu đề cho các cột dữ liệu
$spreadsheet->getActiveSheet()->setCellValue('A1', 'ID');
$spreadsheet->getActiveSheet()->setCellValue('B1', 'Name');
$spreadsheet->getActiveSheet()->setCellValue('C1', 'Address');
$spreadsheet->getActiveSheet()->setCellValue('D1', 'Phone');

// Lấy dữ liệu từ cơ sở dữ liệu và thêm vào file Excel
if (mysqli_num_rows($result) > 0) {
    $rowIndex = 2;
    while($row = mysqli_fetch_assoc($result)) {
        $spreadsheet->getActiveSheet()->setCellValue('A' . $rowIndex, $row['id']);
        $spreadsheet->getActiveSheet()->setCellValue('B' . $rowIndex, $row['name']);
        $spreadsheet->getActiveSheet()->setCellValue('C' . $rowIndex, $row['address']);
        $spreadsheet->getActiveSheet()->setCellValue('D' . $rowIndex, $row['phone']);
        $rowIndex++;
    }
}

// Thiết lập định dạng file Excel
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$fileName = 'Supplier.xlsx';

// Thiết lập header để tải file Excel về máy người dùng
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

// Ghi dữ liệu vào file Excel
$writer->save('php://output');
?>
