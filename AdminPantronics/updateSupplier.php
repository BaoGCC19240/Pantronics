<!DOCTYPE html>
<html>
<head>
  <title>Update supplier</title>
</head>
<?php
include_once('connection.php');
$id = $_GET['id'];
// Lấy thông tin của sản phẩm từ database
$stmt = mysqli_prepare($conn, "SELECT * FROM Supplier WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
?>
<body>
  <h1>Update Supplier</h1>
  <form method="POST" action="">
  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
  <label>Name:</label>
  <input type="text" name="name" value="<?php echo $row['name']; ?>"><br>
  <label>Address:</label>
  <input type="text" name="address" value="<?php echo $row['address']; ?>"><br>
  <label>Phone:</label>
  <input type="text" name="phone" value="<?php echo $row['phone']; ?>"><br>
  <input type="submit" name="update" value="Update">
</form>
<?php 
include_once('connection.php');
if(isset($_POST['update'])){
    $id = $_POST['id'];
  $name = $_POST['name'];
  $address = $_POST['address'];
  $phone = $_POST['phone'];

  // Cập nhật dữ liệu vào database
  $stmt = mysqli_prepare($conn, "UPDATE supplier SET name=?, address=?, phone=? WHERE id=?");
mysqli_stmt_bind_param($stmt, "sssi", $name, $address, $phone, $id);
if(mysqli_stmt_execute($stmt)){
  echo '<script>
  Swal.fire({
      title: "Success!",
      text: "The supplier has been updated.",
      icon: "success",
      showConfirmButton: false,
      timer: 2000
  }).then(function() {
      window.location.href = "?mpage=manageSupplier";
  });
</script>';
}else {
  echo "Error: " . mysqli_error($conn);
}

}
?>
  
</body>
</html>
