<!DOCTYPE html>
<html>
<head>
	<title>Add new category</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
	<h1>Add new category</h1>
	<?php
	include_once('connection.php');

	// Kiểm tra xem người dùng đã submit form chưa
	if (isset($_POST['add'])) {
		// Lấy dữ liệu từ form và kiểm tra tính hợp lệ
		$name = mysqli_real_escape_string($conn, $_POST["name"]);
		$description = mysqli_real_escape_string($conn, $_POST["description"]);
		if (empty($name) || empty($description)) {
			echo '<script>swal("Error", "Please enter all required fields", "error");</script>';

		} else {
			// Sử dụng Prepared Statements để thêm mới danh mục sản phẩm
			$stmt = $conn->prepare("INSERT INTO ProductCategory (name, description) VALUES (?, ?)");
			$stmt->bind_param("ss", $name, $description);
			if ($stmt->execute()) {
				echo '<script>
        Swal.fire({
            icon: "success",
            title: "Product category added successfully",
            showConfirmButton: false,
            timer: 2000
        }).then(function () {
            window.location.href = "?page=manage&&mpage=manageCategory";
        });
    </script>';
			} else {
				echo "Error: " . $stmt->error;
			}
			$stmt->close();
		}
	}

	// Đóng kết nối
	$conn->close();
	?>
	<form method="POST" action="">
		<label for="name">Category name:</label>
		<input type="text" name="name" required>
		<br>
		<label for="description">Description:</label>
		<textarea name="description" required></textarea>
		<br>
		<input type="submit" name="add" value="Add Category">
	</form>
</body>
</html>
