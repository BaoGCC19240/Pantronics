<!DOCTYPE html>
<html>
<head>
	<title>Supplier Sales Report</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

</head>
<body>
	<h1>Supplier Sales Report</h1>
	<table>
		<tr>
			<th>Supplier ID</th>
			<th>Supplier Name</th>
			<th>Total Quantity Sold</th>
			<th>Total Sales Amount</th>
		</tr>
		<?php
			// Thực hiện kết nối tới cơ sở dữ liệu
			include_once('connection.php');

			// Lấy danh sách các nhà cung cấp và thông tin thống kê của từng nhà cung cấp
			$sql = 'SELECT Supplier.id, Supplier.name, SUM(invoicedetail.quantity) AS total_quantity_sold, SUM(invoicedetail.quantity * Product.price) AS total_sales_amount FROM Supplier INNER JOIN Product ON Supplier.id = Product.supplier_id INNER JOIN invoicedetail ON Product.id = invoicedetail.product_id GROUP BY Supplier.id';
			$result = mysqli_query($conn, $sql);

			if (mysqli_num_rows($result) > 0) {
			    // Hiển thị dữ liệu thống kê lấy được từ cơ sở dữ liệu
			    while($row = mysqli_fetch_assoc($result)) {
			        echo "<tr>";
			        echo "<td>" . $row['id'] . "</td>";
			        echo "<td>" . stripslashes($row['name']) . "</td>";
			        echo "<td>" . $row['total_quantity_sold'] . "</td>";
			        echo "<td>" . number_format($row['total_sales_amount'], 2) . "</td>";
			        echo "</tr>";
			    }
			} else {
			    echo "<tr><td colspan='4'>No data available</td></tr>";
			}
		?>
	</table>
    <div id="chart_div"></div>
    <script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Supplier');
  data.addColumn('number', 'Quantity');

  <?php
    // Thực hiện kết nối tới cơ sở dữ liệu
    include_once('connection.php');

    // Lấy dữ liệu số lượng sản phẩm bán của từng nhà cung cấp
    $sql = 'SELECT Supplier.name, SUM(invoicedetail.quantity) AS total_quantity_sold FROM Supplier INNER JOIN Product ON Supplier.id = Product.supplier_id INNER JOIN invoicedetail ON Product.id = invoicedetail.product_id GROUP BY Supplier.id';
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Thêm dữ liệu vào biểu đồ
        while($row = mysqli_fetch_assoc($result)) {
            echo "data.addRow(['" . $row['name'] . "', " . $row['total_quantity_sold'] . "]);";
        }
    }
  ?>

  var options = {
    title: 'Product Quantity by Supplier',
    width: 600,
    height: 400
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
  chart.draw(data, options);
}

</script>

</body>
</html>
