<?php
include_once('connection.php');

if(isset($_GET['cat_Month'])){
    $selected_month = $_GET['cat_Month'];
} else {
    $selected_month = date('Y-m');
}

$category_sql = "SELECT c.name, SUM(d.quantity) AS total_quantity
FROM Product p
INNER JOIN ProductCategory c ON p.category_id = c.id
INNER JOIN InvoiceDetail d ON p.id = d.product_id
INNER JOIN Invoice i ON d.invoice_id = i.id
WHERE i.order_date LIKE '$selected_month%' 
GROUP BY c.id
ORDER BY total_quantity DESC
LIMIT 5";
$category_result = $conn->query($category_sql);

$category_data = array();
while ($category_row = $category_result->fetch_assoc()) {
    $category_data[] = $category_row;
}
$category_json = json_encode($category_data);

header('Content-Type: application/json');
echo $category_json;
?>