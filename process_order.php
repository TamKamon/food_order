<?php
// Modify process_order.php
session_start();
require 'admin/connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if table information exists in session
    if (!isset($_SESSION['current_table'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ไม่พบข้อมูลโต๊ะ กรุณาสแกน QR Code ใหม่'
        ]);
        exit;
    }

    $item_id = $_POST['itemId'];
    $quantity = $_POST['quantity'];
    $special_instructions = $_POST['specialInstructions'];
    $table_id = $_SESSION['current_table']['table_id'];
    
    // สร้าง order_id
    $order_id = 'ORD' . time() . rand(100, 999);
    
    try {
        // เริ่ม transaction
        $conn->begin_transaction();

        // ดึงข้อมูลราคาอาหาร
        $stmt = $conn->prepare("SELECT price FROM menu_items WHERE item_id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        
        if (!$item) {
            throw new Exception("ไม่พบรายการอาหาร");
        }

        $total_amount = $item['price'] * $quantity;
        
        // บันทึกข้อมูลการสั่งอาหาร
        $stmt = $conn->prepare("INSERT INTO orders (order_id, table_id, total_price, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("sid", $order_id, $table_id, $total_amount);
        $stmt->execute();

        // บันทึกรายการอาหารที่สั่ง
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, unit_price, special_instructions) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siids", $order_id, $item_id, $quantity, $item['price'], $special_instructions);
        $stmt->execute();

        // commit transaction
        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'บันทึกการสั่งอาหารเรียบร้อยแล้ว',
            'order_id' => $order_id,
            'table_number' => $_SESSION['current_table']['table_number']
        ]);

    } catch (Exception $e) {
        // rollback เมื่อเกิดข้อผิดพลาด
        $conn->rollback();
        
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>