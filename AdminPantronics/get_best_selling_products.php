<?php
include_once('connection.php');

// Lấy tháng được chọn bởi người dùng từ yêu cầu ajax
if(isset($_GET['month'])){
    $selected_month = $_GET['month'];
} else {
    $selected_month = date('Y-m');
}

// Lấy top 10 sản phẩm bán chạy nhất trong tháng được chọn
$product_sql = "SELECT p.name, SUM(d.quantity) AS total_quantity, SUM(d.quantity * d.price) AS total_revenue
FROM Product p 
JOIN InvoiceDetail d ON p.id = d.product_id 
JOIN Invoice i ON d.invoice_id = i.id 
WHERE i.order_date LIKE '$selected_month%' 
GROUP BY p.id 
ORDER BY total_quantity DESC 
LIMIT 10";
$product_result = $conn->query($product_sql);

// Chuyển đổi kết quả thành mảng JSON
$product_array = array();
while ($product_row = $product_result->fetch_assoc()) {
    $product_array[] = $product_row;
}
$product_json = json_encode($product_array);

// Trả về kết quả dưới dạng JSON
header('Content-type: application/json');
echo $product_json;
?>
