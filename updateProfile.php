<style>
    .container {
  margin: 20px auto;
  max-width: 500px;
  background-color: #d7f6ff;
  border-radius:20px;
}

form label {
  display: block;
  margin-bottom: 5px;
}

form input[type="text"],
form input[type="password"],
form input[type="email"],
form input[type="tel"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  margin-bottom: 20px;
}

form button[type="submit"] {
    display:block;
  background-color: #007bff;
  color: #fff;
  margin:0 auto 0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  text-align:center;
}

form button[type="submit"]:hover {
  background-color: #0069d9;

}
    </style>
<?php
include_once('connection.php');
if(isset($_SESSION["us_id"]) && $_SESSION["us_id"] != null) {
    $id = $_SESSION["us_id"];
    $sql = "SELECT * FROM user WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
}
?>

<div class="container">
    <h2>Update Profile</h2>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" class="form-control" name="username" placeholder="Enter username" value="<?php echo stripslashes($row['username']);?>" disabled>
        <label for="email">Email:</label>
        <input type="email" class="form-control" name="email" placeholder="Enter email" value="<?php echo stripslashes($row['email']);?>" required>
        <label for="address">Address:</label>
        <input type="text" class="form-control" name="address" placeholder="Enter address" value="<?php echo stripslashes($row['address']);?>" required>
        <label for="phone">Phone:</label>
        <input type="tel" class="form-control" name="phone" placeholder="Enter phone" value="<?php echo stripslashes($row['phone']);?>" required>
        <button type="submit" name='update'>Update</button>
    </form>
</div>

<?php
if(isset($_POST['update'])) {
    $email = $_POST["email"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];

    // update user information in database
    $sql = "UPDATE user SET email=?, address=?, phone=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $email, $address, $phone, $id);
    if(mysqli_stmt_execute($stmt)) {
    // update successful, show success message using SweetAlert and redirect to user profile page
    echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
    echo '<script>
        swal({
            title: "Update successful!",
            text: "Your information has been updated successfully.",
            icon: "success",
            button: "OK",
        }).then(() => {
            window.location.href = "index.php";
        });
    </script>';
    exit();
} else {
        // update failed, display error message
        echo "Error updating user information: " . mysqli_error($conn);
    }
}
?>

	<script src="script.js"></script>