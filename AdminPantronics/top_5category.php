<?php
include_once('connection.php');

// Lấy top 10 sản phẩm bán chạy nhất trong tháng hiện tại
$current_month = date('Y-m');
$category_sql = "SELECT c.name, SUM(d.quantity) AS total_quantity
FROM Product p
INNER JOIN ProductCategory c ON p.category_id = c.id
INNER JOIN InvoiceDetail d ON p.id = d.product_id
INNER JOIN Invoice i ON d.invoice_id = i.id
WHERE MONTH(i.order_date) = MONTH(NOW()) AND YEAR(i.order_date) = YEAR(NOW())
GROUP BY c.id
ORDER BY total_quantity DESC
LIMIT 5;";
$category_result = $conn->query($category_sql);

// Hiển thị kết quả ra trang web
?>
<h1>Top 5 best selling product category of the month</h1>

<table>
    <thead>
        <tr>
            <th></th>
            <th><label for="month">Select a month and year:</label>
<select name="month" id="cat_Month" style="width:unset;">
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
            <th>Category name</th>
            <th>Sell ​​number</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($categoryt_row = $category_result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $categoryt_row['name']; ?></td>
                <td><?php echo $categoryt_row['total_quantity']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function() {
    $('#cat_Month').on('change', function() {
        var selectedMonth = $(this).val();
        $.ajax({
            url: 'get_best_selling_category.php',
            data: {cat_Month: selectedMonth},
            dataType: 'json',
            success: function(data) {
                var tableRows = '';
                $.each(data, function(index, category) {
                    tableRows += '<tr>';
                    tableRows += '<td>' + category.name + '</td>';
                    tableRows += '<td>' + category.total_quantity + '</td>';
                    tableRows += '</tr>';
                });
                $('table tbody').html(tableRows);
            }
        });
    });
});
</script>