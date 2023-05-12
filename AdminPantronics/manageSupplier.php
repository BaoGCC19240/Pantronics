<!DOCTYPE html>
<html>
<head>
	<title>Manage Supplier</title>
</head>
<body>
<div class="export"><button onclick="exportToExcel()"><a style="all: unset;" href="exportSupplier.php">Export</a></button></div>
	<script>
		function exportToExcel() {
		// Tạo đối tượng Workbook mới
		var wb = XLSX.utils.book_new();

		// Tạo một đối tượng Worksheet mới
		var ws = XLSX.utils.json_to_sheet(data);

		// Thêm worksheet vào workbook
		XLSX.utils.book_append_sheet(wb, ws, "Supplier Data");

		// Hiển thị hộp thoại lưu file để người dùng chọn nơi lưu và đặt tên cho file
		var filename = prompt("Enter file name:", "Supplier.xlsx");
		if (filename != null) {
			// Xuất file với tên và định dạng được người dùng chọn
			XLSX.writeFile(wb, filename, { bookType: 'xlsx', type: 'binary' });
		}
		}
	</script>
	<h1>Supplier</h1>
    <a href="?mpage=addSupplier">Add</a>
	<table>
		<tr>
			<th>ID</th>
			<th>Supplier Name</th>
			<th>Address</th>
            <th>Phone</th>
			<th colspan="2">Action</th>

		</tr>
		<?php
// Thực hiện kết nối tới cơ sở dữ liệu
include_once('connection.php');

// Lấy danh sách các sản phẩm từ cơ sở dữ liệu
$sql = 'SELECT * FROM Supplier';
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Hiển thị dữ liệu lấy được từ cơ sở dữ liệu
    while($row = mysqli_fetch_assoc($result)) {
?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo stripslashes($row['name']); ?></td>
            <td><?php echo stripslashes($row['address']); ?></td>
            <td><?php echo stripslashes($row['phone']); ?></td>
            <td><a href="?mpage=updateSupplier&amp;id=<?php echo $row['id']; ?>">Update</a></td>
            <td><a href="?mpage=manageSupplier&amp;function=del&amp;id=<?php echo $row['id']; ?>" onclick="event.preventDefault(); deleteConfirm(this)">Delete</a></td>
        </tr>
<?php
    }
} else {
?>
    <tr><td colspan="5">Not added</td></tr>
<?php
}
?>
	</table>
	<br>
	<?php
    include_once("connection.php");
    if(isset($_GET["function"])=="del"){
        if(isset($_GET['id'])){
            $id=$_GET["id"];

            $sql_check_product = "SELECT * FROM Product WHERE supplier_id = '$id'";
            $result_check_product = mysqli_query($conn, $sql_check_product);

            if (mysqli_num_rows($result_check_product) > 0) {
				echo '<script>
						swal({
							title: "Error",
							text: "This supplier cannot be deleted because there are already associated products!",
							type: "error"
						}).then(function() {
							window.location.href = "?mpage=manageSupplier";
						});
					  </script>';
			} else {
                // Nếu không có sản phẩm liên kết thì tiến hành xóa category
				mysqli_query($conn,"DELETE FROM Supplier WHERE id='$id'");
				echo "<script>
				Swal.fire({
					title: 'Supplier deleted!',
					text: 'The supplier has been deleted successfully.',
					icon: 'success',
					confirmButtonText: 'OK'
				}).then(function() {
					window.location.href = '?mpage=manageSupplier';
				});
			</script>";
				
            }
        }
    } 
?>
</body>
</html>
