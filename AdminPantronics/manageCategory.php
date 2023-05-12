<!DOCTYPE html>
<html>
<head>
	<title>Manage Product Category</title>
</head>
<body>
<div class="export"><button onclick="exportToExcel()"><a style="all: unset;" href="exportCategory.php">Export</a></button></div>
	<script>
		function exportToExcel() {
		// Tạo đối tượng Workbook mới
		var wb = XLSX.utils.book_new();

		// Tạo một đối tượng Worksheet mới
		var ws = XLSX.utils.json_to_sheet(data);

		// Thêm worksheet vào workbook
		XLSX.utils.book_append_sheet(wb, ws, "Category Data");

		// Hiển thị hộp thoại lưu file để người dùng chọn nơi lưu và đặt tên cho file
		var filename = prompt("Enter file name:", "category.xlsx");
		if (filename != null) {
			// Xuất file với tên và định dạng được người dùng chọn
			XLSX.writeFile(wb, filename, { bookType: 'xlsx', type: 'binary' });
		}
		}
	</script>
	<h1>Category</h1>
    <a href="?mpage=addCategory">Add</a>
	<table>
		<tr>
			<th>ID</th>
			<th>Category Name</th>
			<th>Description</th>
			<th>Action</th>

		</tr>
		<?php
			// Thực hiện kết nối tới cơ sở dữ liệu
			include_once('connection.php');

			// Lấy danh sách các sản phẩm từ cơ sở dữ liệu
			$sql = 'SELECT * FROM ProductCategory';
			$result = mysqli_query($conn, $sql);

			if (mysqli_num_rows($result) > 0) {
			    // Hiển thị dữ liệu lấy được từ cơ sở dữ liệu
			    while($row = mysqli_fetch_assoc($result)) {
			        ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo stripslashes($row['name']); ?></td>
            <td><?php echo stripslashes($row['description']); ?></td>
            <td><a href='?mpage=updateCategory&&id=<?php echo $row['id']; ?>'>Update</a></td>
            <td><a href="?mpage=manageCategory&amp;function=del&amp;id=<?php echo $row['id']; ?>" onclick="event.preventDefault(); deleteConfirm(this)">Delete</a>

		</td>

        </tr>
        <?php
			    }
			} else {
			    echo "<tr><td colspan='5'>Not added</td></tr>";
			}
		?>
	</table>
	<br>
	<?php
    include_once("connection.php");
    if(isset($_GET["function"])=="del"){
        if(isset($_GET['id'])){
            $id=$_GET["id"];

            // Kiểm tra xem category này có sản phẩm nào không
            $sql_check_product = "SELECT * FROM Product WHERE category_id = '$id'";
            $result_check_product = mysqli_query($conn, $sql_check_product);

            if (mysqli_num_rows($result_check_product) > 0) {
                // Nếu có sản phẩm liên kết thì không cho phép xóa
                echo "<script>
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'This category cannot be deleted because there are already associated products!'
				}).then(() => {
					window.location.href = '?mpage=manageCategory';
				});
			</script>";
            } else {
                // Nếu không có sản phẩm liên kết thì tiến hành xóa category
                mysqli_query($conn,"DELETE FROM ProductCategory WHERE id='$id'");
                echo "<script>
				Swal.fire({
					title: 'Category deleted!',
					text: 'The category has been deleted successfully.',
					icon: 'success',
					confirmButtonText: 'OK'
				}).then(function() {
					window.location.href = '?mpage=manageCategory';
				});
			</script>";
            }
        }
    } 
?>
</body>
</html>
