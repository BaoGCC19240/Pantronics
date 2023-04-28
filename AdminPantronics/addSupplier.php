<!DOCTYPE html>
<html>
<head>
	<title>Add new supplier</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
	<h1>Add new supplier</h1>
	<?php
	include_once('connection.php');

	// Kiểm tra xem người dùng đã submit form chưa
	if (isset($_POST['btn-addSupp'])) {
		// Lấy dữ liệu từ form và kiểm tra tính hợp lệ
		$name = mysqli_real_escape_string($conn, $_POST["name"]);
		$address = mysqli_real_escape_string($conn, $_POST["address"]);
        $phone = mysqli_real_escape_string($conn, $_POST["phone"]);
		if (empty($name) || empty($address)||empty($phone)) {
			echo "Error: Please enter all required fields";
		} else {
			// Sử dụng Prepared Statements để thêm mới danh mục sản phẩm
			$stmt = $conn->prepare("INSERT INTO supplier (name, address, phone) VALUES (?, ?,?)");
			$stmt->bind_param("sss", $name, $address,$phone);
			if ($stmt->execute()) {
				echo '<script>';
echo 'Swal.fire({
          title: "Success!",
          text: "Supplier added successfully",
          icon: "success",
          timer: 2000,
          showConfirmButton: false
      }).then(function() {
          window.location.href = "?page=manage&&mpage=manageSupplier";
      });';
echo '</script>';

			} else {
				echo "Error: " . $stmt->error;
			}
			$stmt->close();
		}
	}

	// Đóng kết nối
	$conn->close();
	?>
	<form action="" method="POST">
		<label for="name">Supplier Name:</label><br>
		<input type="text" id="name" name="name" required><br>
		<label for="address">Address:</label><br>
		<textarea id="address" name="address" required></textarea><br>
        <label for="phone">Phone:</label><br>
		<input type="number" id="phone" name="phone" required><br>
		<input type="submit" value="Add Supplier" name="btn-addSupp">
	</form>
</body>
</html>
