<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://www.gstatic.com/charts/loader.js"></script>
<?php include_once('connection.php');?>
  <h1>Order in this month</h1>
  <div class="container">
  <div class="col-6 col-md-3">
  <div class="card">
  <i class="fa fa-chart-line fa-3x text-primary"></i>
  <div class="card-body">
  <h5 class="card-title">Total Order</h5>
  <?php
  $sql="SELECT COUNT(*) as count_result FROM invoice";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $totalOrder = $row['count_result'];
?>

<p class="card-text"><?php echo $totalOrder; ?></p>
  </div>
  </div>
  </div>
  <div class="col-6 col-md-3">
  <div class="card">
  <i class="fa fa-bell fa-3x text-primary"></i>
  <div class="card-body">
  <h5 class="card-title">New Order</h5>
  <?php
  $sql="SELECT COUNT(*) as count_result FROM invoice where status ='Not Confirm'";
  $result = mysqli_query($conn, $sql);
  $nrow = mysqli_fetch_assoc($result);
  $newOrder = $nrow['count_result'];
?>

<p class="card-text"><?php echo $newOrder; ?></p>
  </div>
  </div>
  </div>
  <div class="col-6 col-md-3">
  <div class="card">
  <i class="fa fa-plane fa-3x text-primary"></i>
  <div class="card-body">
  <h5 class="card-title">Orders being delivered</h5>
  <?php
  $sql="SELECT COUNT(*) as count_result FROM invoice where status ='shipping'";
  $result = mysqli_query($conn, $sql);
  $srow = mysqli_fetch_assoc($result);
  $shipOrder = $srow['count_result'];
?>

<p class="card-text"><?php echo $shipOrder; ?></p>
  </div>
  </div>
  </div>
  <div class="col-6 col-md-3">
  <div class="card">
  <i class="fa fa-check-square fa-3x text-primary"></i>
  <div class="card-body">
  <h5 class="card-title">Orders delivered</h5>
  <?php
  $sql="SELECT COUNT(*) as count_result FROM invoice where status ='delivered'";
  $result = mysqli_query($conn, $sql);
  $drow = mysqli_fetch_assoc($result);
  $delOrder = $drow['count_result'];
?>

<p class="card-text"><?php echo $delOrder; ?></p>
  </div>
  </div>
  </div>
  <div class="col-6 col-md-3">
  <div class="card">
  <i class="fa fa-window-close fa-3x text-primary"></i>
  <div class="card-body">
  <h5 class="card-title">Cancel Order</h5>
  <?php
  $sql="SELECT COUNT(*) as count_result FROM invoice where status ='canceled'";
  $result = mysqli_query($conn, $sql);
  $crow = mysqli_fetch_assoc($result);
  $cOrder = $crow['count_result'];
?>

<p class="card-text"><?php echo $cOrder; ?></p>
  </div>
  </div>
  </div>
  </div>
  <?php
include_once('connection.php');

// Thực hiện truy vấn SQL
$sql = "SELECT COUNT(*) AS total_invoices, MONTH(order_date) AS month
  FROM invoice
  WHERE order_date BETWEEN DATE_SUB(NOW(), INTERVAL 12 MONTH) AND NOW()
  GROUP BY MONTH(order_date)
  ORDER BY MONTH(order_date) ASC;";
$result = $conn->query($sql);

// Tạo bảng dữ liệu cho biểu đồ
$dataTable = array();
$dataTable[] = array('Month', 'Number of invoices');
while ($row = mysqli_fetch_assoc($result)) {
    $month = date("M", strtotime("2000-" . $row['month'] . "-01"));
    $dataTable[] = array($month, (int)$row['total_invoices']);
}

$jsonTable = json_encode($dataTable);
?>

<div id="chart_month"></div>

<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.arrayToDataTable(<?php echo $jsonTable; ?>);

        var options = {
            title: 'Number of Orders in the last 12 months',
            titleTextStyle: {fontSize: 18},
            legend: { position: 'none' },
            vAxis: {
                title: 'Number of orders'
            },
            hAxis: {
                title: 'Month'
            },
            backgroundColor: '#e6f4f9'
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_month'));
        chart.draw(data, options);

    }
</script>
<div class="top_cate">
<?php
include_once('connection.php');

// Lấy top 10 sản phẩm bán chạy nhất trong tháng hiện tại
$current_month = date('Y-m');
$category_sql = "SELECT c.name, SUM(d.quantity) AS total_quantity
FROM Product p
INNER JOIN ProductCategory c ON p.category_id = c.id
INNER JOIN InvoiceDetail d ON p.id = d.product_id
INNER JOIN Invoice i ON d.invoice_id = i.id
WHERE MONTH(i.order_date) = MONTH(NOW()) AND YEAR(i.order_date) = YEAR(NOW())
GROUP BY c.id
ORDER BY total_quantity DESC
LIMIT 5;";
$category_result = $conn->query($category_sql);

// Hiển thị kết quả ra trang web
?>
<h3 style="padding: 20px;">Top 5 best selling product category of the month</h3>

<table>
    <thead>
        <tr>
            <th></th>
            <th><label for="month">Select a month and year:</label>
            <select name="month" id="cat_month" style="width:unset;">
    <?php
        // Lặp qua danh sách các năm từ 2000 đến năm hiện tại
        for ($year = 2020; $year <= date("Y"); $year++) {
            // Lặp qua danh sách các tháng từ 1 đến 12
            for ($month = 1; $month <= 12; $month++) {
                // Tạo giá trị cho thẻ option
                $value = date("Y-m", strtotime($year . '-' . $month . '-01'));
                // Tạo nội dung cho thẻ option
                $text = date("F Y", strtotime($year . '-' . $month . '-01'));
                // Kiểm tra nếu là tháng năm hiện tại thì thêm thuộc tính selected vào thẻ option
                if ($value == date('Y-m')) {
                    echo "<option value='{$value}' selected>{$text}</option>";
                } else {
                    echo "<option value='{$value}'>{$text}</option>";
                }
            }
        }
    ?>
</select></th>
        </tr>
        <tr>
            <th>Category name</th>
            <th>Sell ​​number</th>
        </tr>
    </thead>
    <tbody id="category_table_body">
        <?php while ($categoryt_row = $category_result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $categoryt_row['name']; ?></td>
                <td><?php echo $categoryt_row['total_quantity']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function() {
      $('#cat_month').on('change', function() {
        var selectedMonth = $(this).val();
        $.ajax({
            url: 'get_best_selling_category.php',
            data: {cat_Month: selectedMonth},
            dataType: 'json',
            success: function(data) {
                var tableRows = '';
                $.each(data, function(index, category) {
                    tableRows += '<tr>';
                    tableRows += '<td>' + category.name + '</td>';
                    tableRows += '<td>' + category.total_quantity + '</td>';
                    tableRows += '</tr>';
                });
                $('#category_table_body').html(tableRows);
            }
        });
    });
});
</script>
</div>
<div class="top_product">
<?php
include_once('connection.php');

// Lấy top 10 sản phẩm bán chạy nhất trong tháng hiện tại
$current_month = date('Y-m');
$product_sql = "SELECT p.name, SUM(d.quantity) AS total_quantity, SUM(d.quantity * p.price) AS total_revenue
                FROM Product p 
                JOIN InvoiceDetail d ON p.id = d.product_id 
                JOIN Invoice i ON d.invoice_id = i.id 
                WHERE i.order_date LIKE '$current_month%' 
                GROUP BY p.id 
                ORDER BY total_quantity DESC 
                LIMIT 10";
$product_result = $conn->query($product_sql);

// Hiển thị kết quả ra trang web
?>
<h3 style="padding:20px;">Top 10 best selling products of the month</h3>

<table>
    <thead>
        <tr>
            <th></th>
            <th></th>
            <th><label for="month">Select a month and year:</label>
            <select name="month" id="product_month" style="width:unset;">
    <?php
        // Lặp qua danh sách các năm từ 2000 đến năm hiện tại
        for ($year = 2020; $year <= date("Y"); $year++) {
            // Lặp qua danh sách các tháng từ 1 đến 12
            for ($month = 1; $month <= 12; $month++) {
                // Tạo giá trị cho thẻ option
                $value = date("Y-m", strtotime($year . '-' . $month . '-01'));
                // Tạo nội dung cho thẻ option
                $text = date("F Y", strtotime($year . '-' . $month . '-01'));
                // Kiểm tra nếu là tháng năm hiện tại thì thêm thuộc tính selected vào thẻ option
                if ($value == date('Y-m')) {
                    echo "<option value='{$value}' selected>{$text}</option>";
                } else {
                    echo "<option value='{$value}'>{$text}</option>";
                }
            }
        }
    ?>
</select></th>
        </tr>
        <tr>
            <th>Product name</th>
            <th>Sell ​​number</th>
            <th>Total revenue</th>
        </tr>
    </thead>
    <tbody id="product_table_body">
        <?php while ($product_row = $product_result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $product_row['name']; ?></td>
                <td><?php echo $product_row['total_quantity']; ?></td>
                <td><?php echo $product_row['total_revenue']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function() {
      $('#product_month').on('change', function() {
            var selectedMonth = $(this).val();
            $.ajax({
                url: 'get_best_selling_products.php',
                data: {month: selectedMonth},
                dataType: 'json',
                success: function(data) {
                    var tableRows = '';
                    $.each(data, function(index, product) {
                        tableRows += '<tr>';
                        tableRows += '<td>' + product.name + '</td>';
                        tableRows += '<td>' + product.total_quantity + '</td>';
                        tableRows += '<td>' + product.total_revenue + '</td>';
                        tableRows += '</tr>';
                    });
                    $('#product_table_body').html(tableRows);
                }
            });
        });
    });
</script>
</div>