<?php
// เชื่อมต่อฐานข้อมูล
require 'connect.php';
// Include phpqrcode library
require 'phpqrcode/qrlib.php';
$message = '';

// แก้ไขในส่วน if ($_SERVER["REQUEST_METHOD"] == "POST") { ... }
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table_number = $_POST['table_number'];
    $status = $_POST['status'];

    // ตรวจสอบว่าหมายเลขโต๊ะซ้ำหรือไม่
    $check_sql = "SELECT * FROM tables WHERE table_number = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $table_number);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $message = '<div style="color: red;">หมายเลขโต๊ะนี้มีอยู่ในระบบแล้ว</div>';
    } else {
        // Create directory for QR codes if it doesn't exist
        $qr_directory = 'qrcodes/';
        if (!file_exists($qr_directory)) {
            mkdir($qr_directory, 0777, true);
        }

        // Insert into database first to get table_id
        $sql = "INSERT INTO tables (table_number, status) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $table_number, $status);
        
        if ($stmt->execute()) {
            $table_id = $conn->insert_id; // Get the newly inserted table ID
            
            // Generate unique filename for QR code
            $qr_filename = 'table_' . $table_number . '_' . time() . '.png';
            $qrCodePath = $qr_directory . $qr_filename;

            // สร้าง URL สำหรับ QR Code
            $tableData = [
                'table_number' => $table_number,
                'id' => $table_id
            ];
            // สร้าง URL สำหรับ QR Code
            $qrData = "http://100.124.72.13/food_order/food_order.php?table_number=".$table_number."&id=".$table_id;
            
            // Generate QR Code
            QRcode::png($qrData, $qrCodePath, QR_ECLEVEL_L, 10);

            // Update table record with QR code path
            $update_sql = "UPDATE tables SET qr_code = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $qrCodePath, $table_id);
            $update_stmt->execute();
            $update_stmt->close();

            $message = '<div style="color: green;">เพิ่มโต๊ะใหม่และสร้าง QR Code เรียบร้อยแล้ว</div>';
        } else {
            $message = '<div style="color: red;">เกิดข้อผิดพลาด: ' . $conn->error . '</div>';
        }
        $stmt->close();
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เพิ่มโต๊ะใหม่</title>
    <style>
        .container {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="number"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            margin: 15px 0;
            padding: 10px;
            border-radius: 4px;
        }

        .back-link {
            margin-top: 20px;
            display: block;
        }

        .qr-preview {
            margin-top: 20px;
            text-align: center;
        }

        .qr-preview img {
            max-width: 200px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>เพิ่มโต๊ะใหม่</h2>

        <?php echo $message; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="table_number">หมายเลขโต๊ะ:</label>
                <input type="number" id="table_number" name="table_number" required min="1">
            </div>

            <div class="form-group">
                <label for="status">สถานะ:</label>
                <select id="status" name="status" required>
                    <option value="available">ว่าง</option>
                    <option value="occupied">มีผู้ใช้</option>
                    <option value="reserved">จอง</option>
                </select>
            </div>

            <button type="submit">เพิ่มโต๊ะ</button>
        </form>

        <?php
        // Display QR code if it was just generated
        // Display QR code if it was just generated
if (isset($qrCodePath) && file_exists($qrCodePath)) {
    echo '<div class="qr-preview">
            <h3>QR Code ที่สร้างขึ้น:</h3>
            <img src="' . $qrCodePath . '" alt="Table QR Code">
          </div>';
}
  
        ?>
        <!-- เพิ่มในส่วนที่ต้องการแสดงข้อมูลโต๊ะ -->
                    <?php if (isset($_SESSION['current_table'])): ?>
                    <div class="alert alert-info">
                        <h4>โต๊ะหมายเลข: <?php echo htmlspecialchars($_SESSION['current_table']['table_number']); ?></h4>
                    </div>
                    <?php endif; ?>

        <a href="add_table.php" class="create-button">กลับไปหน้ารายการโต๊ะ</a>
    </div>

    <script>
        document.querySelector('form').onsubmit = function(e) {
            const tableNumber = document.getElementById('table_number').value;
            if (tableNumber <= 0) {
                alert('กรุณาระบุหมายเลขโต๊ะที่มากกว่า 0');
                e.preventDefault();
                return false;
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>