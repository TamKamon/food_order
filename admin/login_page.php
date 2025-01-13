<?php
// เปิดการแสดงข้อผิดพลาด
ini_set('display_errors', 1);
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
require 'connect.php';

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์ม
// รับข้อมูลจากฟอร์ม
$username = $_POST['username'];
$password = $_POST['password']; // รหัสผ่านที่ผู้ใช้กรอก

// ใช้ MD5 แฮชรหัสผ่าน
$hashed_password = md5($password);

// ค้นหาผู้ใช้จากฐานข้อมูล
$sql = "SELECT id, username, password_hash FROM login WHERE username = '$username'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    
    // ตรวจสอบรหัสผ่าน
    if ($hashed_password === $user['password_hash']) {
        // ถ้ารหัสผ่านถูกต้อง
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // อัปเดตเวลาล็อกอินล่าสุด
        $update_sql = "UPDATE login SET last_login = NOW() WHERE id = " . $user['id'];
        mysqli_query($conn, $update_sql);

        echo "<script type='text/javascript'>
        alert('คุณกำลังถูกเปลี่ยนเส้นทางไปยังแดชบอร์ด');
        window.location.href = 'index.php';
      </script>";
        exit;
    } else {
        echo "<script type='text/javascript'>
        alert('คุณใส่รหัสไม่ถูกต้องกรุณาใส่รหัสให้ถูกต้อง');
        window.location.href = 'login.php';
      </script>";
    }
} else {
    $error = "ไม่พบชื่อผู้ใช้ในระบบ!";
}



// ปิดการเชื่อมต่อฐานข้อมูล
mysqli_close($conn);
?>