<?php
// เชื่อมต่อฐานข้อมูล
require 'connect.php';
// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $calories = $_POST['calories'] ?? NULL;
    $spicy_level = $_POST['spicy_level'];
    $preparation_time = $_POST['preparation_time'] ?? NULL;
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // จัดการอัปโหลดรูปภาพ
    $image_url = NULL;
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        
        // ย้ายไฟล์ไปยังโฟลเดอร์ uploads
        if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    // เตรียมคำสั่ง SQL
    $sql = "INSERT INTO menu_items 
            (name, description, price, category, image_url, calories, spicy_level, preparation_time, is_available) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // เตรียม statement
    $stmt = $conn->prepare($sql);
    
    // ผูกพารามิเตอร์
    $stmt->bind_param("ssdssissi", 
        $name, $description, $price, $category, $image_url, 
        $calories, $spicy_level, $preparation_time, $is_available
    );

    // ประมวลผลและตรวจสอบ
    if ($stmt->execute()) {
        echo "<script type='text/javascript'>
        alert('บันทึกข้อมูลสำเร็จ');
        window.location.href = 'add_menu.php';
      </script>";
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    // ปิด statement
    $stmt->close();
}

// ปิดการเชื่อมต่อ
$conn->close();
?>