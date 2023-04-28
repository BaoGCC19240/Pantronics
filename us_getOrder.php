<h2>Your orders</h2>
<?php
    // Connect to MySQL database
    include_once('connection.php');
    // Get user ID from request
    if (isset($_GET["user_id"])) {
        $userId = $_GET["user_id"];
    } else {
        $userId = $_GET['id'];
    }
	// Retrieve phone number associated with the user ID
$sql = "SELECT phone FROM user WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$phone = $row['phone'];

// Select orders for user with specified ID and phone number
$sql = "SELECT * FROM invoice WHERE user_id = ? OR phone = ? ORDER BY id DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $userId, $phone);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
// Generate HTML for order list table
$orderListHtml = "<table><tr><th>ID</th><th>Invoice Number</th><th>Order Date</th><th>Deliver Date</th><th>Total</th><th>Status</th></tr>";
if (mysqli_num_rows($result) > 0) {
    // Loop through each row and add it to the HTML
    while($row = mysqli_fetch_assoc($result)) {
        $orderId = $row["id"];
        $productName = $row["invoice_number"];
        $quantity = $row["order_date"];
        $deliver = $row["delivery_date"];
        $price = $row["total"];
        $total = $row["status"];
        $orderListHtml .= "<tr><td>" . $orderId . "</td><td><a  style='text-decoration:none;' href='?page=view_invoice&&id=" .$orderId. "'>#" .$productName. "</a></td><td>" . $quantity . "</td><td>" . $deliver . "</td><td>" . $price . "</td><td>" . $total . "</td></tr>";
    }
} else {
    $orderListHtml .= "<tr><td colspan='6'>No orders found</td></tr>";
}
$orderListHtml .="</table>";
// Return HTML for order list
echo $orderListHtml;


?>
<style>
    table {
        border-collapse: collapse;
        width: 60%;
        left:20%;
		margin: 0px auto 20px;
        border: 1px solid black;
        border-radius:15px;
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
    h2{
        text-align:center;
    }
</style>
<script src="script.js"></script>