<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Change Password</title>
	<link rel="stylesheet" href="style.css">
</head>
<style>
    .container {
	margin: 50px auto;
	padding: 20px;
	max-width: 480px;
	background-color: #f8f8f8;
	border: 1px solid #ccc;
	border-radius: 15px;
}

h2 {
	margin-top: 0;
}

form {
	display: flex;
	flex-direction: column;
}

label {
	margin-top: 10px;
}

input[type="password"] {
	padding: 10px;
	border-radius: 5px;
	border: 1px solid #ccc;
	margin-top: 5px;
}

button[type="submit"] {
    display:block;
	background-color: #00a4e0ba;
	color: white;
	padding: 10px;
	border: none;
	border-radius: 5px;
	margin: 1rem auto 0;
	cursor: pointer;
    width:170px;
    text-align:center;
}

button[type="submit"]:hover {
	background-color: #00a4e0ba;
}
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.js"></script>
<body>
	<div class="container">
		<h2>Change Password</h2>
		<form method="POST">
			<label for="current-password">Current Password</label>
			<input type="password" id="current-password" name="current-password" placeholder="Enter current password" required>

			<label for="password">New Password</label>
			<input type="password" id="password" name="password" oninput="checkLenght(); checkPass();" placeholder="Enter new password" required>

			<label for="confirm-password">Confirm Password</label>
			<input type="password" id="re_password" name="re_password" oninput="checkPass();" placeholder="Confirm new password" required>

			<button type="submit" name="save-btn">Save Changes</button>
		</form>
	</div>
    <?php
    include_once('connection.php');
    
    if(isset($_POST['save-btn'])){
        $us=$_SESSION['us_id'];
        $curPass = $_POST['current-password'];
        $newPass = $_POST['password'];
        $confirmPass = $_POST['re_password'];
        
        // Check if current password is correct
        $sq="SELECT password FROM user WHERE id=?";
        $stmt = mysqli_prepare($conn, $sq);
        mysqli_stmt_bind_param($stmt, "s", $us);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
        $encrypted_password = ($row != null) ? $row['password'] : '';
        
        if (password_verify($curPass, $encrypted_password)) {
            // Current password is correct
            
            // Check if new password and confirm password match
            if($newPass == $confirmPass){
                $newPass = password_hash($newPass, PASSWORD_DEFAULT);
                $sq = "UPDATE user SET password=? WHERE id=?";
                $stmt = mysqli_prepare($conn, $sq);
                mysqli_stmt_bind_param($stmt, "ss", $newPass, $us);
                mysqli_stmt_execute($stmt);
                
                echo '<script>
        Swal.fire({
            icon: "success",
            title: "Password changed successfully.",
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location = "index.php";
        });
    </script>';
            } else {
                // New password and confirm password do not match
                echo '<script>Swal.fire("Error", "New password and confirm password do not match.", "error");</script>';
            }
        } else {
            // Current password is incorrect
            echo '<script>Swal.fire("Error", "Current password is incorrect. Please try again.", "error");</script>';
        }
    }
?>
</body>

</html>
<script src="script.js"></script>