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
            <form method="POST" action="" id="update-form-<?php echo $row["id"]; ?>">
                    <select name="status">
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

?>
<script>
    // Thêm sự kiện onchange cho các dropdown menu
    const selectList = document.querySelectorAll("select[name='status']");
    for (let i = 0; i < selectList.length; i++) {
        const select = selectList[i];
        select.addEventListener('change', function (event) {
            // Ngăn chặn mặc định của form (tải lại trang)
            event.preventDefault();

            // Lấy giá trị status mới
            const newStatus = event.target.value;

            // Lấy id của đơn hàng tương ứng
            const orderId = event.target.form.querySelector("input[name='order_id']").value;

            // Tạo đối tượng XMLHttpRequest để gửi yêu cầu đến server
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_order_status.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            // Xử lý phản hồi từ server sau khi gửi yêu cầu
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Cập nhật trạng thái đơn hàng trên giao diện
                    const statusColumn = event.target.closest('td');
                    const statusCell = statusColumn.querySelector('.status-cell');
                    statusCell.textContent = newStatus;
                } else {
                    // Hiển thị thông báo lỗi nếu có lỗi xảy ra
                    alert('There was an error processing your request. Please try again later.');
                }
            };

            // Gửi yêu cầu đến server với các thông tin cần thiết
            xhr.send(`status=${encodeURIComponent(newStatus)}&order_id=${encodeURIComponent(orderId)}`);
        });
    }
</script>