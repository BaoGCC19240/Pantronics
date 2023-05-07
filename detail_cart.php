<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.js"></script>
 <div class="container">
  <h1>Detail Cart</h1>
  <table>
    <thead>
      <tr>
        <th>Product Image</th>
        <th>Product Name</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody id="cart-table-body">
      <?php
      $total = 0;
      $fee = 0;
      if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-checkout'])) {
        $_SESSION['cart'] = json_decode($_POST['cart'], true);
      }
      // Xử lý giá trị của biến cart ở đây
      if (!empty($_SESSION['cart'])) {

        foreach ($_SESSION['cart'] as $item) {
          $total += $item['price'] * $item['quantity'];
          ?>
          <tr>
            <td><img src="<?php echo $item['image'] ?>" alt="<?php echo $item['name'] ?>"></td>
            <td>
              <?php echo $item['name'] ?>
            </td>
            <td>
              <?php echo $item['quantity'] ?>
            </td>
            <td>$
              <?php echo $item['price'] ?>
            </td>
            <td>$
              <?php echo $item['price'] * $item['quantity'] ?>
            </td>
          </tr>
        </tbody>
        <?php
        }
        $fee = ($total / 160 > 3) ? $total / 160 : 3;
        ?>
      <?php
      } else {
        ?>
      <td colspan="5" style="text-align:center">No products have been added to the cart.</td>
      <?php
      }
      ?>
    <tfoot>
      <tr>
        <td colspan="4" style="text-align:right">Provisional:</td>
        <td>
          <?php echo $total ?>$
        </td>
      </tr>
      <tr>
        <td colspan="4" style="text-align:right">Transport fee:</td>
        <td>
          <?php 
          $fee =number_format($fee, 2, '.', '');
          echo $fee; ?>$
        </td>
      </tr>
      <tr>
        <td colspan="4" style="text-align:right">Total:</td>
        <td>
          <?php echo $total + $fee ?>$
        </td>
      </tr>
    </tfoot>
    <!-- add more products as needed -->

  </table>
  <form style="all:unset;" method='POST'>
    <div class="pay_Method">
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
    <button type="submit" class="checkout-btn" name="checkout-btn" id="checkout-btn">Checkout</button>
  </form>
</div>

<?php
// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (isset($_POST["checkout-btn"])) {
  if (!isset($_SESSION["us_id"])) {
    // Chuyển hướng đến trang đăng nhập hoặc đăng ký
    echo '<meta http-equiv="refresh" content="0;url=?page=login"/>';
  } else {
    // Kết nối đến cơ sở dữ liệu
    include_once('connection.php');
    if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
      echo "<script>swal.fire('Empty Cart', 'Your cart is currently empty', 'warning').then(() => {window.location.href = 'index.php'});</script>";
      exit();
  }
    //kiểm tra số lượng
    foreach ($_SESSION['cart'] as $product) {
      $product_id = $product['id'];
      $quantity = $product['quantity'];
      $price = $product['price'];

      // Lấy số lượng sản phẩm hiện có trong kho
      $get_product_quantity_query = "SELECT quantity FROM Product WHERE id = ?";
      $stmt = mysqli_prepare($conn, $get_product_quantity_query);
      mysqli_stmt_bind_param($stmt, "i", $product_id);
      mysqli_stmt_execute($stmt);
      $product_quantity = mysqli_stmt_get_result($stmt)->fetch_assoc()['quantity'];

      if ($quantity > $product_quantity) {
        // Số lượng sản phẩm trong giỏ hàng vượt quá số lượng sản phẩm hiện có trong kho
        // In thông báo lỗi và dừng đơn hàng
        echo "<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Sorry, there are only $product_quantity items in stock.',
    });
</script>";

        exit;
      }
      $total += $fee;
      $user_id = $_SESSION["us_id"];
      $invoice_number = 'INV-' . time(); // Số hóa đơn được tạo từ timestamp
      $order_date = date('Y-m-d'); // Ngày đặt hàng
      $deliver = date('Y-m-d', strtotime($order_date . ' + 5 days'));

      $query_user = "SELECT address, phone FROM user WHERE id = ?";
      $stmt = mysqli_prepare($conn, $query_user);
      mysqli_stmt_bind_param($stmt, "i", $user_id);
      mysqli_stmt_execute($stmt);
      $result_user = mysqli_stmt_get_result($stmt);
      $row_user = mysqli_fetch_assoc($result_user);
      $address = stripslashes($row_user['address']);
      $phone = stripslashes($row_user['phone']);
      $payment_method = $_POST['payment_method'];
    }
    if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'cash_on_delivery') {
      // Tạo đơn hàng mới
      $status='Not Confirm';
      $query = "INSERT INTO Invoice (invoice_number, order_date, delivery_date, total, status, user_id, address, phone, transaction) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, "sssssssss", $invoice_number, $order_date, $deliver, $total, $status, $user_id, $address, $phone, $payment_method);
    
      if (mysqli_stmt_execute($stmt)) {
        $invoice_id = mysqli_insert_id($conn);
    
      } else {
        die('Lỗi: ' . mysqli_error($conn));
      }
      // Duyệt qua mảng cart và tạo chi tiết đơn hàng
      foreach ($_SESSION['cart'] as $product) {
        $product_id = $product['id'];
        $quantity = $product['quantity'];
        $price = $product['price'];
        $insert_invoice_detail_query = "INSERT INTO InvoiceDetail (quantity, price, product_id, invoice_id) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_invoice_detail_query);
        mysqli_stmt_bind_param($stmt, "ssss", $quantity, $price, $product_id, $invoice_id);
        mysqli_stmt_execute($stmt);
    
        // Trừ số lượng sản phẩm trong bảng Product
        $update_product_quantity_query = "UPDATE Product SET quantity = quantity - ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_product_quantity_query);
        mysqli_stmt_bind_param($stmt, "ss", $quantity, $product_id);
        mysqli_stmt_execute($stmt);
      }
      unset($_SESSION['cart']);
      ?>
      <script>
        var cart = [];
        localStorage.setItem('cart', JSON.stringify(cart));
        swal.fire({
          title: "Success!",
          text: "Your order has been placed successfully!",
          type: "success",
          confirmButtonText: "OK"
      }).then(function() {
          window.location = "index.php";
      });
      </script>
      <?php
    }
    if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'vnpay') {
      date_default_timezone_set('Asia/Ho_Chi_Minh');
      /*
       * To change this license header, choose License Headers in Project Properties.
       * To change this template file, choose Tools | Templates
       * and open the template in the editor.
       */

      $vnp_TmnCode = "9PC9G6E6"; //Mã định danh merchant kết nối (Terminal Id)
      $vnp_HashSecret = "PEYOLBSXCEZMAAJZKFNWQVQQMCJJNWKZ"; //Secret key
      $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
      $vnp_Returnurl = "https://pantronics.herokuapp.com/index.php?page=checkout_details";
      $vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
      $apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
      //Config input format
      //Expire
      $startTime = date("YmdHis");
      $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
      $vnp_TxnRef = 'INV-' . time(); //Mã giao dịch thanh toán tham chiếu của merchant
      $vnp_Amount = intval($total) * 100 * 23000; // Số tiền thanh toán
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
        "vnp_ExpireDate" => $expire
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
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
      }
      echo "<meta http-equiv=\"refresh\" content=\"0;URL=$vnp_Url\"/>";
    }
    if(isset($_GET['vnp_ResponseCode']) && $_GET['vnp_ResponseCode'] == 00) {
  $query = "INSERT INTO Invoice (invoice_number, order_date, delivery_date, total, status, user_id,address,phone,transaction) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = mysqli_prepare($conn, $query);

  mysqli_stmt_bind_param($stmt, "sssssssss", $invoice_number, $order_date, $deliver, $total, $status, $user_id, $address, $phone, $payment_method);

  if(mysqli_stmt_execute($stmt)) {
    $invoice_id = mysqli_insert_id($conn);
  } else {
    die('Error: ' . mysqli_error($conn));
  }

  // Duyệt qua mảng cart và tạo chi tiết đơn hàng
  foreach ($_SESSION['cart'] as $product) {
    $product_id = $product['id'];
    $quantity = $product['quantity'];
    $price = $product['price'];
    $insert_invoice_detail_query = "INSERT INTO InvoiceDetail (quantity, price, product_id, invoice_id) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_invoice_detail_query);
    mysqli_stmt_bind_param($stmt, "ssss", $quantity, $price, $product_id, $invoice_id);
    mysqli_stmt_execute($stmt);
    // Trừ số lượng sản phẩm trong bảng Product
    $update_product_quantity_query = "UPDATE Product SET quantity = quantity - ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_product_quantity_query);
    mysqli_stmt_bind_param($stmt, "ss", $quantity, $product_id);
    mysqli_stmt_execute($stmt);
  }

  unset($_SESSION['cart']);
  ?>
  <script>
    var cart = [];
    localStorage.setItem('cart', JSON.stringify(cart));
  </script>
  <?php
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
      echo '<script>
swal.fire({
  title: "Payment failed",
  text: "Please try again later",
  icon: "error",
  button: "OK",
}).then(function() {
  window.location = "index.php";
});
</script>';
    }
    
  }

}

?>

<script src="script.js"></script>