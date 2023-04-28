<div class="export"><button onclick="exportToExcel()"><a style="all: unset;" href="exportOrder.php">Export</a></button></div>
	<script>
		function exportToExcel() {
		// Tạo đối tượng Workbook mới
		var wb = XLSX.utils.book_new();

		// Tạo một đối tượng Worksheet mới
		var ws = XLSX.utils.json_to_sheet(data);

		// Thêm worksheet vào workbook
		XLSX.utils.book_append_sheet(wb, ws, "Order Data");

		// Hiển thị hộp thoại lưu file để người dùng chọn nơi lưu và đặt tên cho file
		var filename = prompt("Enter file name:", "Order.xlsx");
		if (filename != null) {
			// Xuất file với tên và định dạng được người dùng chọn
			XLSX.writeFile(wb, filename, { bookType: 'xlsx', type: 'binary' });
		}
		}
	</script>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
<h1>List Order</h1>
<table>
    <thead>
        <tr><th colspan='5'></th><th>
            <form style="all:unset;" method="GET">
    <label for="filter_date">Filter by date:</label>
    <select name="filter_date" id="filter_date" onchange="changeUrl(this)">
        <option value="">All dates</option>
        <?php
                        include_once('connection.php');
                        // Lấy danh sách các ngày tháng có trong cơ sở dữ liệu
                        $date_sql = "SELECT DISTINCT order_date FROM invoice ORDER BY order_date DESC";
                        $date_result = $conn->query($date_sql);
                        while ($date_row = $date_result->fetch_assoc()) {
                            $selected = isset($_GET['filter_date']) && $_GET['filter_date'] == $date_row['order_date'] ? 'selected' : '';
                            echo "<option value='{$date_row['order_date']}' {$selected}>{$date_row['order_date']}</option>";
                        }
                        ?>
                    </select>
</form></th></tr>

        <tr>
            <th>Order ID #</th>
            <th>Order date</th>
            <th>Delivery Date</th>
            <th>Total</th>
            <th>Customer name</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody id="order-table-body">
        <?php
        // Kết nối đến cơ sở dữ liệu
        include_once("connection.php");
        // Truy vấn danh sách đơn hàng
        
        // Xử lý lấy giá trị date từ dropdown menu
        $filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

        // Thêm điều kiện WHERE vào câu truy vấn nếu có ngày được chọn
        $where = $filter_date ? " WHERE order_date = '{$filter_date}'" : '';

        // Câu truy vấn cơ sở dữ liệu
        $sql = "SELECT * FROM invoice {$where} ORDER BY id DESC LIMIT 10";

        // Thực hiện truy vấn
        $result = $conn->query($sql);

        // Hiển thị danh sách đơn hàng
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Lấy thông tin người dùng
                $user_id = $row["user_id"];
                $user_sql = "SELECT * FROM User WHERE id = $user_id";
                $user_result = $conn->query($user_sql);
                $user_row = $user_result->fetch_assoc();

                // Lấy danh sách sản phẩm trong đơn hàng
                $invoice_id = $row["id"];
                $product_sql = "SELECT * FROM InvoiceDetail WHERE invoice_id = $invoice_id";
                $product_result = $conn->query($product_sql);

                // Tính tổng số sản phẩm trong đơn hàng
                $total_quantity = 0;
                while ($product_row = $product_result->fetch_assoc()) {
                    $total_quantity += $product_row["quantity"];
                }
        ?>
                <tr>
                    <td><a href="?mpage=view_invoice&&id=<?php echo $row["id"]; ?>">#<?php echo $row["invoice_number"]; ?></a></td>
                    <td><?php echo $row["order_date"]; ?></td>
                    <td><?php echo $row["delivery_date"]; ?></td>
                    <td><?php echo $row["total"]; ?></td>
                    <td><?php echo $user_row["username"]; ?></td>
                    <td>
                        <form method="POST" action="">
                            <select name="status" onchange="this.form.submit()">
                            <option value="Not Confirmed" <?php if($row["status"]=="Not Confirm"){echo "selected";} ?>>Not confirmed</option>
                                <option value="confirmed" <?php if($row["status"]=="confirmed"){echo "selected";} ?>>Confirmed</option>
                                <option value="shipping" <?php if($row["status"]=="shipping"){echo "selected";} ?>>Shipping</option>
                                <option value="delivered" <?php if($row["status"]=="delivered"){echo "selected";} ?>>Delivered</option>
                                <option value="canceled" <?php if($row["status"]=="canceled"){echo "selected";} ?>>Canceled</option>
                            </select>
                            <input type="hidden" name="order_id" value="<?php echo $row["id"]; ?>">
                        </form>
                    </td>
                </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='7'>No orders yet</td></tr>";
        }
        if(isset($_POST['status']) && isset($_POST['order_id'])) {
            $new_status = $_POST['status'];
            $order_id = $_POST['order_id'];
            // Thực hiện cập nhật trạng thái mới trong cơ sở dữ liệu
            $update_sql = "UPDATE Invoice SET status = '$new_status' WHERE id = $order_id";
            if($conn->query($update_sql)) {
                // Nếu cập nhật thành công, chuyển hướng trở lại trang danh sách order
                echo '<script>swal.fire("Success", "Order status updated successfully!", "success").then(function() {window.location.href="?mpage=manageOrder";});</script>';
            } else {
                echo '<script>';
                echo 'swal.fire("Oops...", "There was an error processing your request. Please try again later.", "error");                ';
                echo '</script>';
                
            }
        }
        ?>
    </tbody>
</table>
<script>
function changeUrl(selectBox) {
  var value = selectBox.value;
  var url = '?page=manage&&mpage=manageOrder&&filter_date=' + encodeURIComponent(value);
  window.location.href = url;
}
</script>
<script>
// Load the next set of rows when the user scrolls to the bottom of the page
window.addEventListener('scroll', function() {
  // Check if the user has scrolled to the bottom of the page
  if (window.innerHeight + window.pageYOffset >= document.body.offsetHeight) {
    // Make an AJAX request to load the next set of rows
    const xhr = new XMLHttpRequest();
    const urlParams = new URLSearchParams(window.location.search);
    const filterDate = urlParams.get('filter_date');
    const currentPage = document.querySelectorAll('#order-table-body tr').length;
    xhr.open('GET', `load_more_orders.php?filter_date=${filterDate}&page=${currentPage}`);

    // Update the table with the new rows
    xhr.onload = function() {
      const newRows = xhr.responseText;

      if (newRows) {
        const orderTableBody = document.getElementById('order-table-body');
        orderTableBody.innerHTML += newRows;
      }
    };

    xhr.send();
  }
});
</script>