<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Details Order</title>
</head>
<body>
<?php
// Kết nối đến cơ sở dữ liệu
include_once("connection.php");

// Lấy ID của đơn hàng từ GET parameter
$invoice_id = $_GET["id"];

// Truy vấn thông tin của đơn hàng
$sql = "SELECT * FROM Invoice WHERE id = $invoice_id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Truy vấn thông tin người dùng
$user_id = $row["user_id"];
$user_sql = "SELECT * FROM User WHERE id = $user_id";
$user_result = $conn->query($user_sql);
$user_row = $user_result->fetch_assoc();

// Truy vấn danh sách sản phẩm trong đơn hàng
$product_sql = "SELECT * FROM InvoiceDetail WHERE invoice_id = $invoice_id";
$product_result = $conn->query($product_sql);
?>

<!-- Hiển thị thông tin đơn hàng -->
<h1>Details Order</h1>
<div style="margin-left: 5%;">

<h3>Order ID #<?php echo $row["invoice_number"]; ?></h3>
<p>Customer name: <?php echo $user_row["username"]; ?></p>
<p>Address: <?php echo $row["address"]; ?></p>
<p>Phone: <?php echo $row["phone"]; ?></p>
<p>Order Date: <?php echo $row["order_date"]; ?></p>
<p>Delivery Date: <?php echo $row["delivery_date"]; ?></p>
<p>Status: <?php echo $row["status"]; ?></p>
<p>Payment method: <?php echo $row["transaction"]; ?></p>


<!-- Hiển thị danh sách sản phẩm trong đơn hàng -->
<h4>List of product:</h4>



    </div>
    <table>
    <tr>
        <th>Product name</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
    </tr>
    <?php 
    $total = 0;
    while ($product_row = $product_result->fetch_assoc()) {
        $pro_id = $product_row['product_id']; 
        $query="select name from product where id = $pro_id";
        $res= $conn->query($query);
        $name=$res->fetch_assoc();
        $subtotal = $product_row["quantity"] * $product_row["price"];
        $total += $subtotal;
        $total=($total/160>3)? $total+$total/160:$total+3;
    ?>
        <tr>
            <td><?php echo $name['name']; ?></td>
            <td><?php echo $product_row["quantity"]; ?></td>
            <td><?php echo $product_row["price"]; ?></td>
            <td><?php echo number_format($subtotal, 2); ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="3" style="text-align: right;">Total:</td>
        <td><?php echo number_format($total, 2); ?></td>
    </tr>
</table>
</body>
</html>
<style>
    table {
        border-collapse: collapse;
        width: 90%;
        margin:0 auto 0;
    }
    
    th, td {
        text-align: left;
        padding: 8px;
    }
    
    th {
        background-color: #61caf3;
        color: white;
    }
    
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
</style>
<script src="script.js"></script>
