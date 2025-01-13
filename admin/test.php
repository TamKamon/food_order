<?php
// เชื่อมต่อฐานข้อมูล
require 'connect.php';
// Prepare SQL query to select all tables
$sql = "SELECT * FROM tables";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สถานะโต๊ะ</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <style>
        .available { color: green; }
        .occupied { color: red; }
        .reserved { color: orange; }
    </style>
</head>
<body>
    <table id="datatablesSimple">
        <thead>
            <tr>
                <th>รหัสโต๊ะ</th>
                <th>หมายเลขโต๊ะ</th>
                <th>สถานะ</th>
                <th>รหัส QR</th>
                <th>วันที่สร้าง</th>
                <th>วันที่อัปเดต</th>
                <th>การดำเนินการ</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result->num_rows > 0) {
                // Output data of each row
                while($table = $result->fetch_assoc()) {
            ?>
            <tr>
                <td><?php echo htmlspecialchars($table['id']); ?></td>
                <td><?php echo htmlspecialchars($table['table_number']); ?></td>
                <td class="<?php echo htmlspecialchars($table['status']); ?>">
                    <?php 
                    switch($table['status']) {
                        case 'available':
                            echo 'ว่าง';
                            break;
                        case 'occupied':
                            echo 'มีผู้ใช้';
                            break;
                        case 'reserved':
                            echo 'จอง';
                            break;
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($table['qr_code'] ?: 'ยังไม่มี QR Code'); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($table['created_at'])); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($table['updated_at'])); ?></td>
                <td>
                    <button onclick="updateTableStatus(<?php echo $table['id']; ?>)">
                        อัปเดตสถานะ
                    </button>
                </td>
            </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='7'>ไม่พบข้อมูลโต๊ะ</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Modal สำหรับอัปเดตสถานะโต๊ะ -->
    <div id="updateStatusModal" style="display:none;">
        <h2>อัปเดตสถานะโต๊ะ</h2>
        <form id="updateStatusForm">
            <input type="hidden" id="tableId" name="tableId">
            <select name="status">
                <option value="available">ว่าง</option>
                <option value="occupied">มีผู้ใช้</option>
                <option value="reserved">จอง</option>
            </select>
            <button type="submit">บันทึก</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new simpleDatatables.DataTable("#datatablesSimple");
        });

        function updateTableStatus(tableId) {
            // เปิด Modal และตั้งค่า tableId
            document.getElementById('tableId').value = tableId;
            document.getElementById('updateStatusModal').style.display = 'block';
        }

        // จัดการฟอร์มอัปเดตสถานะ
        document.getElementById('updateStatusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // ส่งข้อมูลด้วย AJAX ไปยังสคริปต์ PHP สำหรับอัปเดตสถานะ
            fetch('update_table_status.php', {
                method: 'POST',
                body: new FormData(e.target)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('อัปเดตสถานะสำเร็จ');
                    location.reload(); // โหลดหน้าใหม่
                } else {
                    alert('เกิดข้อผิดพลาด: ' + data.message);
                }
            });
        });
    </script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>