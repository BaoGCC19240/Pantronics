<?php
include_once('connection.php');

// Lấy top 10 sản phẩm bán chạy nhất trong tháng hiện tại
$current_month = date('Y-m');
$product_sql = "SELECT p.name, SUM(d.quantity) AS total_quantity, SUM(d.quantity * p.price) AS total_revenue
                FROM Product p 
                JOIN InvoiceDetail d ON p.id = d.product_id 
                JOIN Invoice i ON d.invoice_id = i.id 
                WHERE i.order_date LIKE '$current_month%' 
                GROUP BY p.id 
                ORDER BY total_quantity DESC 
                LIMIT 10";
$product_result = $conn->query($product_sql);

// Hiển thị kết quả ra trang web
?>
<h1>Top 10 best selling products of the month</h1>

<table>
    <thead>
        <tr>
            <th></th>
            <th></th>
            <th><label for="month">Select a month and year:</label>
<select name="month" id="month" style="width:unset;">
    <?php
        // Lặp qua danh sách các năm từ 2000 đến năm hiện tại
        for ($year = 2020; $year <= date("Y"); $year++) {
            // Lặp qua danh sách các tháng từ 1 đến 12
            for ($month = 1; $month <= 12; $month++) {
                // Tạo giá trị cho thẻ option
                $value = date("Y-m", strtotime($year . '-' . $month . '-01'));
                // Tạo nội dung cho thẻ option
                $text = date("F Y", strtotime($year . '-' . $month . '-01'));
                // Kiểm tra nếu là tháng năm hiện tại thì thêm thuộc tính selected vào thẻ option
                if ($value == date('Y-m')) {
                    echo "<option value='{$value}' selected>{$text}</option>";
                } else {
                    echo "<option value='{$value}'>{$text}</option>";
                }
            }
        }
    ?>
</select></th>
        </tr>
        <tr>
            <th>Product name</th>
            <th>Sell ​​number</th>
            <th>Total revenue</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($product_row = $product_result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $product_row['name']; ?></td>
                <td><?php echo $product_row['total_quantity']; ?></td>
                <td><?php echo $product_row['total_revenue']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function() {
        $('#month').on('change', function() {
            var selectedMonth = $(this).val();
            $.ajax({
                url: 'get_best_selling_products.php',
                data: {month: selectedMonth},
                dataType: 'json',
                success: function(data) {
                    var tableRows = '';
                    $.each(data, function(index, product) {
                        tableRows += '<tr>';
                        tableRows += '<td>' + product.name + '</td>';
                        tableRows += '<td>' + product.total_quantity + '</td>';
                        tableRows += '<td>' + product.total_revenue + '</td>';
                        tableRows += '</tr>';
                    });
                    $('table tbody').html(tableRows);
                }
            });
        });
    });
</script>