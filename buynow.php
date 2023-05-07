<head>
  <link rel="stylesheet" type="text/css" href="buynow.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.js"></script>
</head>
<?php
include_once('connection.php');

// Get the product ID from the URL parameter
$id = $_GET['id'];
$quantity = $_GET['quantity'];

// Query the database to get the product details
$stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Use prepared statements to query for the product image
$stmt = $conn->prepare("SELECT * FROM productimage WHERE product_id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$ima = $stmt->get_result()->fetch_assoc();

$product_price = $row['price'];
$fee = ($quantity * $row['price'] / 160 > 3) ? $quantity * $row['price'] / 160 : 3;
$fee = number_format($fee, 2, '.', '');
$quantity_in_stock = $row['quantity'];
?>
<div id="info">
  <ul>
    <li>
      <img src="<?php echo stripslashes($ima['image_url']); ?>" alt="Product Image">
      <!-- Display the product details on the Buy Now page -->
      <h2>
        <?php echo stripslashes($row['name']); ?>
      </h2>
      <p class="qty">
        <?php echo stripslashes($quantity); ?>
      </p>
      <p id="price">
        <?php echo stripslashes($row['price']); ?>
      </p>
    </li>
    <li>
      <hr class="hr">
    </li>
    <li>
      <p style="font-weight:400; ">Provisional: </p>
      <p style="font-weight:600; ">
        <?php echo $quantity * $row['price']; ?>$
      </p>
    </li>
    <li>
      <p style="font-weight:400; ">Transport fee: </p>
      <p style="font-weight:600; ">
        <?php echo $fee; ?>$
      </p>
    </li>
    <li>
      <hr class="hr">
    </li>
    <li>
      <p style="font-weight:400; ">Total: </p>
      <p style="font-weight:600; ">
        <?php echo $quantity * $row['price'] + $fee; ?>$
      </p>
    </li>
    <ul>
</div>


<!-- Add a form for the user to enter their shipping information and payment details -->
<div class="user-info">
  <form action="" method="post">
    <h1 style="margin-bottom:0;">PanTronics-<span>Shipment Details</span></h1>
    <p style="margin-left:5%;">Do you already have an account?<a href="?page=login"
        style="text-decoration: none;">Login</a></p>

<?php
// Check if the session variables exist
if (isset($_SESSION['name']) && isset($_SESSION['email']) && isset($_SESSION['address']) && isset($_SESSION['phone'])) {
    // Pre-populate the form fields with the session variables
    echo '<label for="name">Name:</label>
          <input type="text" id="name" name="name" value="'.stripslashes($_SESSION['name']).'" required>

          <label for="email">Email:</label>
          <input type="email" id="email" name="email" value="'.stripslashes($_SESSION['email']).'" required>

          <label for="address">Address:</label>
          <textarea id="address" name="address" required>'.stripslashes($_SESSION['address']).'</textarea>

          <label for="card-number">Phone Number:</label>
          <input type="text" id="phone-number" name="phone-number" value="'.stripslashes($_SESSION['phone']).'" required>';

}else{?>


    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="address">Address:</label>
    <textarea id="address" name="address" required></textarea>

    <label for="card-number">Phone Number:</label>
    <input type="number" id="phone-number" name="phone-number" required>
    <?php }?>

    <input type="hidden" name="product-id" value="<?php echo $row['id']; ?>">
    <div class="pay_Method">
      <div>
        <h3>Payment methods</h3>
        <label>
          <input type="radio" checked name="payment_method" value="cash_on_delivery">
          Cash on delivery
        </label>
        <label>
          <input type="radio" name="payment_method" value="vnpay">
          Pay with VNPAY
        </label>
      </div>

    </div>
    <button type="submit" name="placeOrder">Continue</button>
  </form>
</div>
<script>
  const phoneNumberInput = document.getElementById("phone-number");

// Regular expression to match a valid phone number
const phoneNumberRegex = /^\d{10}$/;

phoneNumberInput.addEventListener("input", () => {
  const phoneNumber = phoneNumberInput.value;
  if (phoneNumberRegex.test(phoneNumber)) {
    // Phone number is valid
    phoneNumberInput.setCustomValidity("");
  } else {
    // Phone number is invalid
    phoneNumberInput.setCustomValidity("Please enter a valid phone number");
  }
});
</script>
<?php
include_once('connection.php');

// Get the product ID from the URL parameter
$id = $_GET['id'];
$quantity = $_GET['quantity'];

// Query the database to get the product details
$stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$stmt = $conn->prepare("SELECT * FROM productimage WHERE product_id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$ima = $stmt->get_result()->fetch_assoc();

$product_price = $row['price'];
$fee = ($quantity * $row['price'] / 160 > 3) ? $quantity * $row['price'] / 160 : 3;
$fee = number_format($fee, 2, '.', '');
$quantity_in_stock = $row['quantity'];
$total = $quantity * $product_price + $fee;

// Check if the form has been submitted
if (isset($_POST['placeOrder'])) {
  // Get the user's details from the form
  $username = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone-number']);
  $payment_method = $_POST['payment_method'];

  $_SESSION['name'] = $_POST['name'];
  $_SESSION['email'] = $_POST['email'];
  $_SESSION['address'] = $_POST['address'];
  $_SESSION['phone'] = $_POST['phone-number'];
  $_SESSION['payment_method'] = $_POST['payment_method'];


  // Check the selected payment method
  if ($payment_method == 'cash_on_delivery') {
    // Insert the user's details into the database using prepared statements
    $stmt = mysqli_prepare($conn, "INSERT INTO User (username, email, address, phone) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $address, $phone);
    if (mysqli_stmt_execute($stmt)) {
      $user_id = mysqli_insert_id($conn);
      $invoice_number = 'INV-' . time();
      $order_date = date('Y-m-d');
      $deliver = date('Y-m-d', strtotime($order_date . ' + 5 days'));
      $query = "INSERT INTO Invoice (invoice_number, order_date,delivery_date, total, status, user_id, address, phone,transaction ) VALUES ('$invoice_number', '$order_date', '$deliver', '$total', 'Not Confirm', '$user_id','$address','$phone' ,'$payment_method')";
      if (mysqli_query($conn, $query)) {
        $invoice_id = mysqli_insert_id($conn);
        $query = "INSERT INTO invoicedetail (quantity, price, invoice_id, product_id) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iiii", $quantity, $product_price, $invoice_id, $id);
        if (mysqli_stmt_execute($stmt)) {
          // Update the product quantity in the database
          $quantity_in_stock -= $quantity;
          $stmt = mysqli_prepare($conn, "UPDATE product SET quantity = ? WHERE id = ?");
          mysqli_stmt_bind_param($stmt, "ii", $quantity_in_stock, $id);
          mysqli_stmt_execute($stmt);
          echo '<script>
    swal.fire({
        title: "Success!",
        text: "Your order has been placed successfully!",
        type: "success",
        confirmButtonText: "OK"
    }).then(function() {
        window.location = "index.php";
    });
</script>';
        } else {
          echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
      } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
      }
    } else {
      echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
  }
  if ($payment_method == 'vnpay') {
    


    date_default_timezone_set('Asia/Ho_Chi_Minh');
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    $vnp_TmnCode = "9PC9G6E6"; //Mã định danh merchant kết nối (Terminal Id)
    $vnp_HashSecret = "PEYOLBSXCEZMAAJZKFNWQVQQMCJJNWKZ"; //Secret key
    $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    $vnp_Returnurl = "https://pantronics.herokuapp.com/index.php?page=buynow&id=".$id."&quantity=" .$quantity;
    $vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
    $apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
    //Config input format
    //Expire
    $startTime = date("YmdHis");
    $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
    $vnp_TxnRef = 'INV-' . time(); //Mã giao dịch thanh toán tham chiếu của merchant
    $vnp_Amount = intval($total) * 100*23000; // Số tiền thanh toán
$vnp_Locale = 'en'; //Ngôn ngữ chuyển hướng thanh toán
$vnp_BankCode = 'NCB'; //Mã phương thức thanh toán
$vnp_IpAddr = $_SERVER['REMOTE_ADDR']; //IP Khách hàng thanh toán

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => "Thanh toan GD:" . $vnp_TxnRef,
    "vnp_OrderType" => "other",
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef,
    "vnp_ExpireDate"=>$expire
);

if (isset($vnp_BankCode) && $vnp_BankCode != "") {
    $inputData['vnp_BankCode'] = $vnp_BankCode;
}

ksort($inputData);
$query = "";
$i = 0;
$hashdata = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = $vnp_Url . "?" . $query;
if (isset($vnp_HashSecret)) {
    $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}
echo "<meta http-equiv=\"refresh\" content=\"0;URL=$vnp_Url\"/>";
  }
}
if(isset($_GET['vnp_ResponseCode'])&&$_GET['vnp_ResponseCode']==00){
  $username =  $_SESSION['name'];
  $email = $_SESSION['email'];
  $address = $_SESSION['address'];
  $phone = $_SESSION['phone'];
  $payment_method = $_SESSION['payment_method'];

  $conn->begin_transaction();
    $sql = "INSERT INTO User (username, email, address, phone) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $address, $phone);
    if ($stmt->execute()) {
      $user_id = mysqli_insert_id($conn);
      $invoice_number = 'INV-' . time();
      $order_date = date('Y-m-d');
      $deliver = date('Y-m-d', strtotime($order_date . ' + 5 days'));
      $status="Not Confirm";
      $query = "INSERT INTO Invoice (invoice_number, order_date,delivery_date, total, status, user_id, address, phone,transaction ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("sssssssss", $invoice_number, $order_date, $deliver, $total, $status, $user_id, $address, $phone, $payment_method);
      if ($stmt->execute()) {
        $invoice_id = mysqli_insert_id($conn);
        $query = "INSERT INTO invoicedetail (quantity, price, invoice_id, product_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssii", $quantity, $product_price, $invoice_id, $id);
        if ($stmt->execute()) {
          // Update the product quantity in the database
          $quantity_in_stock -= $quantity;
          $query = "UPDATE product SET quantity = ? WHERE id = ?";
          $stmt = $conn->prepare($query);
          $stmt->bind_param("ii", $quantity_in_stock, $id);
          $stmt->execute();
        } else {
          echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
      } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
      }
    } else {
      echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }

  $conn->commit();
  unset($_SESSION['name']);
unset($_SESSION['email']);
unset($_SESSION['address']);
unset($_SESSION['phone']);
echo '<script>
swal.fire({
    title: "Success!",
    text: "Your order has been placed successfully!",
    type: "success",
    confirmButtonText: "OK"
}).then(function() {
    window.location = "index.php";
});
</script>';
}

if(isset($_GET['vnp_ResponseCode'])&&$_GET['vnp_ResponseCode']==24){
  $conn->rollback();
  echo '<script>
swal.fire({
  title: "Payment failed",
  text: "Please try again later",
  icon: "error",
  button: "OK",
}).then(function() {
});
</script>';
}
?>
<script src="script.js"></script>