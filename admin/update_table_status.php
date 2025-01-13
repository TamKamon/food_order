<?php
header('Content-Type: application/json');

// เชื่อมต่อฐานข้อมูล
require 'connect.php';
// Prepare SQL query to select all tables

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit;
}

// รับค่าจากฟอร์ม
$tableId = $_POST['tableId'];
$status = $_POST['status'];

// เตรียมคำสั่ง SQL
$sql = "UPDATE tables SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $status, $tableId);

// ประมวลผลและส่งกลับ
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>