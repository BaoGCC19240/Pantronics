<?php
// Kết nối đến cơ sở dữ liệu
include_once("connection.php");

// Lấy thông tin trạng thái mới và id đơn hàng cần cập nhật từ yêu cầu
$newStatus = $_POST['status'];
$orderId = $_POST['order_id'];

// Cập nhật trạng thái đơn hàng trong cơ sở dữ liệu
$updateSql = "UPDATE Invoice SET status = '$newStatus' WHERE id = $orderId";
if ($conn->query($updateSql)) {
  // Trả về phản hồi 200 OK nếu cập nhật thành công
  http_response_code(200);
} else {
  // Trả về phản hồi lỗi nếu có lỗi xảy ra
  http_response_code(500);
}
?>
