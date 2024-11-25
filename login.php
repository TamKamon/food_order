<?php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'my_database';
$username = 'root';
$password = '';

try {
    // สร้างการเชื่อมต่อกับฐานข้อมูล
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์ม
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $_POST['username'];
        $pass = $_POST['password'];

        // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $user);
        $stmt->execute();

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        // ตรวจสอบข้อมูลผู้ใช้
        if ($userData && password_verify($pass, $userData['password'])) {
            echo "เข้าสู่ระบบสำเร็จ! ยินดีต้อนรับ " . htmlspecialchars($userData['name']) . "!";
            // สามารถเปลี่ยนเป็นการ redirect ไปหน้าอื่น เช่น dashboard.php
        } else {
            echo "ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง!";
        }
    }
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาด: " . $e->getMessage());
}
?>
