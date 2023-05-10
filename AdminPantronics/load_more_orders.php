<?php
// Kết nối đến cơ sở dữ liệu
include_once("connection.php");

// Xử lý lấy giá trị date từ query string
if (isset($_GET['filter_date']) && $_GET['filter_date'] !== '') {
    $filter_date = $_GET['filter_date'];
  } else {
    $filter_date = NULL;
  }
// Xử lý lấy giá trị số trang hiện tại
$countPage = isset($_GET['countPage']) ? intval($_GET['countPage']) : 0;
$start = $countPage + 10;

// Thêm điều kiện WHERE vào câu truy vấn nếu có ngày được chọn
$where = $filter_date!==null ? " WHERE order_date = '{$filter_date}'" : '';

// Câu truy vấn cơ sở dữ liệu
$sql = "SELECT * FROM invoice {$where} ORDER BY id DESC LIMIT 10 OFFSET {$start}";

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
