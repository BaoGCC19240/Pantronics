<?php
include_once('connection.php');

$sort = $_GET['sort'];

if ($sort === 'best-seller') {
  $sql = "SELECT p.*, SUM(id.quantity) as total_sales
  FROM product p
  JOIN InvoiceDetail id ON p.id = id.product_id
  GROUP BY p.id
  ORDER BY total_sales DESC";
} elseif ($sort === 'newest') {
  $sql = "SELECT * FROM product ORDER BY id DESC";
} elseif ($sort === 'price-descending') {
  $sql = "SELECT * FROM product ORDER BY price DESC";
} elseif ($sort === 'price-ascending') {
  $sql = "SELECT * FROM product ORDER BY price ASC";
}

$result = mysqli_query($conn, $sql);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
  $sql = "SELECT * FROM ProductImage WHERE product_id = " . $row['id'] . " ORDER BY id LIMIT 1";
  $res = mysqli_query($conn, $sql);
  $fect =mysqli_fetch_assoc($res);
  $imageUrl =$fect['image_url'];

  $product = [
    'id' => $row['id'],
    'name' => $row['name'],
    'price' => $row['price'],
    'image' => $imageUrl,
    'qty'=>$row['quantity']
  ];
  $products[] = $product;
}

header('Content-Type: application/json');
echo json_encode($products);
?>
