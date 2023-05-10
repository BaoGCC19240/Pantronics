<?php
session_start();
if (!isset($_SESSION["us_admin"])) {
    // Nếu người dùng chưa đăng nhập, chuyển hướng đến trang đăng nhập
    header('Location: loginAdmin.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Admin Panel</title>
	<link rel="stylesheet" type="text/css" href="styleManage.css">
	<script src="script.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
	<div class="mn-wrapper">
		<nav>
			<ul>
				<li><a href="?page=manage&&mpage=rev_statistics">Order statistics</a></li>
				<li><a href="?page=manage&&mpage=supplierReport">Supplier Report</a></li>
				<li><a href="?page=manage&&mpage=manageUser">User</a></li>
				<li><a href="?page=manage&&mpage=manageSupplier">Supplier</a></li>
				<li><a href="?page=manage&&mpage=manageCategory">Category</a></li>
				<li><a href="?page=manage&&mpage=manageProduct">Product</a></li>
				<li><a href="?page=manage&&mpage=manageOrder">Order</a></li>
			</ul>
		</nav>
		<div class="mn-content">
			<?php
			if(isset($_GET['mpage'])){
				$mpage=$_GET['mpage'];
				if($mpage=='manageCategory'){
					include_once('manageCategory.php');
				}
				if($mpage=='addCategory'){
					include_once('addCategory.php');
				}
				if($mpage=='updateCategory'){
					include_once('updateCategory.php');
				}
				if($mpage=='manageProduct'){
					include_once('manageProduct.php');
				}
				if($mpage=='addProduct'){
					include_once('addProduct.php');
				}
				if($mpage=='updateProduct'){
					include_once('updateProduct.php');
				}
				if($mpage=='manageOrder'){
					include_once('manageOrder.php');
				}if($mpage=='view_invoice'){
					include_once('view_invoice.php');
				}
				if($mpage=='manageUser'){
					include_once('manageUser.php');
				}
				if($mpage=='top_10product'){
					include_once('top_10product.php');
				}
				if($mpage=='manageSupplier'){
					include_once('manageSupplier.php');
				}
				if($mpage=='addSupplier'){
					include_once('addSupplier.php');
				}
				if($mpage=='updateSupplier'){
					include_once('updateSupplier.php');
				}
				if($mpage=='top_5category'){
					include_once('top_5category.php');
				}
				if($mpage=='supplierReport'){
					include_once('supplierReport.php');
				}
				if($mpage=='rev_statistics'){
					include_once('rev_statistics.php');
				}
			}else{
				include_once('rev_statistics.php');
			}
			?>
		</div>
	</div>
	<script src="script.js"></script>
</body>
</html>
