<script src="script.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.js"></script>
<div class="banner">
            <img src="./Images/banner1.png" alt="banner 1">
            <img src="./Images/banner3.png" alt="banner 2">
            <img src="./Images/banner2.png" alt="banner 3">
    </div>
  
    <div class="filter">
    <p>Sort <i class="fa fa-filter" aria-hidden="true"></i></p>
    <button class="filter-btn" data-sort="best-seller">Best-selling product</button>
    <button class="filter-btn" data-sort="newest">Newest product</button>
    <button class="filter-btn" data-sort="price-descending">Price descending</button>
    <button class="filter-btn" data-sort="price-ascending">Price ascending</button>
  </div>
  <h2 style="text-align:center">Featured Products</h2>
  <script>
    // Lắng nghe sự kiện click của các nút Sort
document.querySelectorAll('.filter-btn').forEach(button => {
  button.addEventListener('click', () => {
    // Lấy giá trị sort từ thuộc tính data-sort của nút
    const sort = button.getAttribute('data-sort');

    // Gửi yêu cầu AJAX để tải lại danh sách sản phẩm
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `sort_products.php?sort=${sort}`);
    xhr.onload = () => {
      if (xhr.status === 200) {
        // Cập nhật danh sách sản phẩm mới
        const productsList = document.querySelector('.products ul');
        productsList.innerHTML = '';
        const products = JSON.parse(xhr.responseText);
        products.forEach(product => {
            productsList.innerHTML += `
  <li>
    <img src="${product.image}" alt="Product has no image">
    <h3>${product.name}</h3>
    <p>${product.price.toLocaleString('en-US', { style: 'currency', currency: '$' })}</p>
    ${product.qty > 0 ? `
      <a href="#" class="btn btn-secondary" onclick="addToCart(event, '${product.id}')">Add to Cart</a>
      <a href="?page=product_detail&&id=${product.id}" class="btn btn-primary btn-details">View Details</a>
    ` : `
      <a href="#" class="btn btn-secondary out-stock" onclick="" style="pointer-events:none;">Out of Stock</a>
    `}
  </li>
`;
        });
      }
    };
    xhr.send();
  });
});

    </script>
    <div class="products">
        <ul>
            <?php
            include_once('connection.php');

            if(isset($_GET['catSelect'])){
                $id =$_GET['catSelect'];
                $sq="select * from product where category_id = '$id'";
                
            }else{
                $sq ="select * from product";
            }
            $result =mysqli_query($conn,$sq);
            
            while($row = mysqli_fetch_assoc($result)) {
                $sql = "SELECT * FROM ProductImage WHERE product_id = " . $row['id'] . " ORDER BY id LIMIT 1";
                $res = mysqli_query($conn,$sql);
                $fect =mysqli_fetch_assoc($res);
                $imageUrl =$fect['image_url'];
            ?>
            <li><img src="<?php echo $imageUrl; ?>" alt="Product has no image">
                <h3><?php echo $row['name'] ;?></h3>
                <p><?php echo intval($row['price']); ?> $</p>
                <?php
                if($row['quantity']>0){
                    ?>
                <a href="#" class="btn btn-secondary" onclick="addToCart(event, '<?php echo $row['id']; ?>')">Add to Cart</a>
                <a href="?page=product_detail&&id=<?php echo $row['id']; ?>" class="btn btn-primary btn-details">View Details</a></li>
                <?php
                }
                else{
                    ?>
                <a href="#" class="btn btn-secondary out-stock" onclick="" style="pointer-events:none;">Out of Stock</a>    
                
                
            <?php }}?>
        </ul>
        <script>
           function addToCart(event, id) {
            event.preventDefault();
            let product = {
                name: event.target.parentNode.querySelector('h3').textContent,
                price: event.target.parentNode.querySelector('p').textContent,
                image: event.target.parentNode.querySelector('img').getAttribute('src'),
                quantity:1,
                id: id
            }
            let cartIndex = cart.findIndex(item => item.id == id);
            if (cartIndex === -1) {
                cart.push(product);
            } else {
                cart[cartIndex].quantity++;
            }
          Swal.fire({
          icon: "success",
          title: "Product added to cart",
          showConfirmButton: false,
          timer: 2000})
            localStorage.setItem('cart', JSON.stringify(cart));
            totalCart();
            renderCart();
            }
        </script>
    </div>
