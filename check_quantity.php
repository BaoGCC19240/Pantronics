<?php
// Lấy id sản phẩm từ tham số truyền vào
$productId = $_GET['id'];

// Kết nối CSDL và thực hiện truy vấn để lấy thông tin số lượng sản phẩm trong kho
include_once('connection.php');

$sql = "SELECT quantity FROM product WHERE id = " . $productId;
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // Nếu tìm thấy sản phẩm trong kho, trả về thông tin số lượng trong kho dưới dạng JSON
  $row = mysqli_fetch_assoc($result);
  $quantityInStock = $row['quantity'];
  $response = array('quantity' => $quantityInStock);
  echo json_encode($response);
} else {
  // Nếu không tìm thấy sản phẩm trong kho, trả về thông báo lỗi dưới dạng JSON
  $response = array('error' => 'Product not found');
  echo json_encode($response);
}
?>
