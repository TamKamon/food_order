<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_order";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character encoding
mysqli_set_charset($conn, "utf8");

// แก้ไข Query โดยใช้ LEFT JOIN แทน JOIN ปกติ และปรับการเชื่อมโยง order_id
$sql = "SELECT 
    o.order_id,
    o.table_id,
    o.total_price,
    o.status,
    o.created_at as order_time,
    t.table_number,
    GROUP_CONCAT(CONCAT(m.name, ' x', oi.quantity) SEPARATOR ', ') as order_details,
    GROUP_CONCAT(oi.special_instructions SEPARATOR ', ') as instructions
FROM orders o
LEFT JOIN tables t ON o.table_id = t.id
LEFT JOIN order_items oi ON CONCAT('ORD', o.order_id) = oi.order_id
LEFT JOIN menu_items m ON oi.item_id = m.item_id
GROUP BY o.order_id
ORDER BY o.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการอาหารที่สั่ง</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">รายการอาหารที่สั่ง</h2>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>เลขที่โต๊ะ</th>
                            <th>รายการอาหาร</th>
                            <th>ราคารวม</th>
                            <th>หมายเหตุ</th>
                            <th>เวลาที่สั่ง</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['table_number']; ?></td>
                                <td><?php echo $row['order_details'] ?: 'ไม่มีรายการ'; ?></td>
                                <td><?php echo number_format($row['total_price'], 2); ?> บาท</td>
                                <td><?php echo $row['instructions'] ?: '-'; ?></td>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($row['order_time'])); ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch($row['status']) {
                                        case 'pending':
                                            $statusClass = 'badge bg-warning';
                                            $statusText = 'รอดำเนินการ';
                                            break;
                                        case 'completed':
                                            $statusClass = 'badge bg-success';
                                            $statusText = 'เสร็จสิ้น';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'badge bg-danger';
                                            $statusText = 'ยกเลิก';
                                            break;
                                    }
                                    ?>
                                    <span class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">ไม่พบรายการอาหารที่สั่ง</div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>