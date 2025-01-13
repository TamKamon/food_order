<?php
// เชื่อมต่อฐานข้อมูล
require 'connect.php';
$message = '';

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
        // สร้าง QR Code (ตัวอย่างใช้เลขโต๊ะเป็น QR)
        $qr_code = "TABLE_" . $table_number;
        
        $sql = "INSERT INTO tables (table_number, status, qr_code) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $table_number, $status, $qr_code);
        
        if ($stmt->execute()) {
            $message = '<div style="color: green;">เพิ่มโต๊ะใหม่เรียบร้อยแล้ว</div>';
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
        input[type="number"], select {
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
        <html lang="th">
                            <head>
                                <meta charset="UTF-8">
                                <title>รายการโต๊ะ</title>
                                <style>
                                    .create-button {
                                        background-color: #4CAF50;
                                        color: white;
                                        padding: 10px 20px;
                                        text-decoration: none;
                                        display: inline-block;
                                        border-radius: 5px;
                                        margin: 20px;
                                        font-size: 16px;
                                    }
                                    .create-button:hover {
                                        background-color: #45a049;
                                    }
                                </style>
                            </head>
                            <body>
                                <!-- เพิ่มปุ่มด้านบนของตาราง -->
                                <a href="add_table.php" class="create-button">กลับไปหน้ารายการโต๊ะ</a>

                                <!-- ส่วนที่เหลือของโค้ดตารางที่มีอยู่เดิม -->
                            </body>
                            </html>

        
    </div>
    <!DOCTYPE html>
                            

    <script>
        // เพิ่ม JavaScript สำหรับการตรวจสอบข้อมูล
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