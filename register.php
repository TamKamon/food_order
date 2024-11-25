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

    // ตรวจสอบการส่งข้อมูลจากฟอร์ม
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $_POST['username'];
        $pass = password_hash($_POST['password'], PASSWORD_BCRYPT); // เข้ารหัสรหัสผ่าน
        $name = $_POST['name'];
        $gender = $_POST['gender'];

        // เตรียมคำสั่ง SQL
        $stmt = $pdo->prepare("INSERT INTO users (username, password, name, gender) VALUES (:username, :password, :name, :gender)");

        // ผูกค่าตัวแปร
        $stmt->bindParam(':username', $user);
        $stmt->bindParam(':password', $pass);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':gender', $gender);

        // ดำเนินการเพิ่มข้อมูล
        if ($stmt->execute()) {
            echo "สมัครสมาชิกสำเร็จ!";
        } else {
            echo "เกิดข้อผิดพลาดในการสมัครสมาชิก!";
        }
    }
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาด: " . $e->getMessage());
}
?>
