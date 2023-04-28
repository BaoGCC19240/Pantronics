<?php
include_once('connection.php');

// Get the filter date and current page number from the query string
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * 10; // Load 10 rows at a time

// Build the SQL query to fetch the next set of rows
$where = $filter_date ? "WHERE order_date = '{$filter_date}'" : '';
$sql = "SELECT * FROM invoice {$where} ORDER BY id DESC LIMIT 10 OFFSET {$offset}";

// Execute the query and build the HTML for the new rows
$result = $conn->query($sql);
$html = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_id = $row["user_id"];
        $user_sql = "SELECT * FROM User WHERE id = $user_id";
        $user_result = $conn->query($user_sql);
        $user_row = $user_result->fetch_assoc();

        $invoice_id = $row["id"];
        $product_sql = "SELECT * FROM InvoiceDetail WHERE invoice_id = $invoice_id";
        $product_result = $conn->query($product_sql);

        $total_quantity = 0;
        while ($product_row = $product_result->fetch_assoc()) {
            $total_quantity += $product_row["quantity"];
        }

        $html .= "<tr>";
        $html .= "<td><a href='?mpage=view_invoice&&id={$row['id']}'>#{$row['invoice_number']}</a></td>";
        $html .= "<td>{$row['order_date']}</td>";
        $html .= "<td>{$row['delivery_date']}</td>";
        $html .= "<td>{$row['total']}</td>";
        $html .= "<td>{$user_row['username']}</td>";
        $html .= "<td>
                    <form method='POST' action=''>
                        <select name='status' onchange='this.form.submit()'>
                            <option value='Not Confirmed' " . ($row["status"]=="Not Confirm" ? "selected" : "") . ">Not confirmed</option>
                            <option value='confirmed' " . ($row["status"]=="confirmed" ? "selected" : "") . ">Confirmed</option>
                            <option value='shipping' " . ($row["status"]=="shipping" ? "selected" : "") . ">Shipping</option>
                            <option value='delivered' " . ($row["status"]=="delivered" ? "selected" : "") . ">Delivered</option>
                            <option value='canceled' " . ($row["status"]=="canceled" ? "selected" : "") . ">Canceled</option>
                        </select>
                        <input type='hidden' name='order_id' value='{$row['id']}'>
                    </form>
                </td>";
        $html .= "</tr>";
    }
}

// Send back the HTML for the new rows
echo $html;
