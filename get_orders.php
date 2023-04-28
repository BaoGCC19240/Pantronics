<?php
	// Connect to MySQL database
	include_once('connection.php');
	// Get user ID from request
		$userId = $_GET["user_id"];
	// Select orders for user with specified ID
	$sql = "SELECT * FROM invoice WHERE user_id = " . $userId;
	$result = mysqli_query($conn, $sql);
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
	        $orderListHtml .= "<tr><td>" . $orderId . "</td><td>" . $productName . "</td><td>" . $quantity . "</td><td>" . $deliver . "</td><td>" . $price . "</td><td>" . $total . "</td></tr>";
	    }
	} else {
	    $orderListHtml .= "<tr><td colspan='5'>No orders found</td></tr>";
	}
	$orderListHtml .="</table>";
	// Return HTML for order list
	echo $orderListHtml;

?>
