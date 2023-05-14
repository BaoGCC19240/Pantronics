<script src="script.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.js"></script>
<div class="searchBar">
    <div class="search-container">
        <input type="text" placeholder="Search for . . ." name="search" onkeyup="searchProducts(this.value)">
        <button type="submit" name="search-btn"><i class="fa fa-search"></i></button>
    </div>
</div>

<style>
    /* Style for checkbox */
    input[type=checkbox] {
        margin-right: 5px;
    }

    /* Style for label */
    label {
        display: inline-flex;
        align-items: center;
        margin-left: 10px;
    }
</style>
<script>
    function searchProducts(keyword) {
        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let products = JSON.parse(this.responseText);
                let productList = document.querySelector('.products ul');
                let notFound = document.querySelector('.not-found');
                productList.innerHTML = '';
                notFound.style.display = 'none';
                if (products.length === 0) {
                    notFound.style.display = 'block';
                } else {
                    products.forEach(function (product) {
                        var price =  parseInt(product.price);
                        let li = document.createElement('li');
                        li.innerHTML = `
                        <img src="${product.image}" alt="Product has no image">
                        <h3>${product.name}</h3>
                        <p>${price}<span> USD</span></p>
                        ${product.quantity > 0 ?
                            `<a href="#" class="btn btn-secondary" onclick="addToCart(event, '${product.id}')">Add to Cart</a>
                            <a href="?page=product_detail&&id=${product.id}" class="btn btn-primary btn-details">View Details</a>` :
                            `<a href="#" class="btn btn-secondary out-stock" onclick="" style="pointer-events:none;">Out of Stock</a>`}
                        `;
                        productList.appendChild(li);
                    });
                }
            }
        };
        xhr.open("GET", "searchProduct.php?keyword=" + keyword, true);
        xhr.send();
    }



</script>
<h2 style="text-align:center">Search products</h2>
<div class="search-results">
    <ul></ul>
    <h3 class="not-found">No products found !!!!</h3>
</div>
<div class="products">
    <ul>
        <?php
        include_once('connection.php');

        if (isset($_GET['catSelect'])) {
            $id = $_GET['catSelect'];
            $sq = "select * from product where category_id = ?";
            $stmt = mysqli_prepare($conn, $sq);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        
        } else {
            $sq = "select * from product";
            $result = mysqli_query($conn, $sq);
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $sql = "SELECT * FROM ProductImage WHERE product_id = " . $row['id'] . " ORDER BY id LIMIT 1";
            $res = mysqli_query($conn, $sql);
            $fect = mysqli_fetch_assoc($res);
            $imageUrl = $fect['image_url'];
            ?>
            <li><img src="<?php echo $imageUrl; ?>" alt="Product has no image">
                <h3>
                    <?php echo $row['name']; ?>
                </h3>
                <p>
                    <?php echo intval($row['price']); ?> <span> USD</span>
                </p>
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
                </li>
                
            <?php }}?>
    </ul>
    <script>


        function addToCart(event, id) {
            event.preventDefault();
            let product = {
                name: event.target.parentNode.querySelector('h3').textContent,
                price: event.target.parentNode.querySelector('p').textContent,
                image: event.target.parentNode.querySelector('img').getAttribute('src'),
                quantity: 1,
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
