<?php
require 'connect.php';

// ตรวจสอบว่ามีการส่งค่า id ของรายการอาหาร
if (isset($_GET['id'])) {
    $menu_id = intval($_GET['id']);

    // ดึงข้อมูลเมนูจากฐานข้อมูล
    $sql = "SELECT * FROM menu_items WHERE item_id = $menu_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $menu_item = $result->fetch_assoc();
    } else {
        echo "<script>alert('ไม่พบรายการอาหาร'); window.location.href = 'add_menu.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ไม่มีข้อมูลรายการที่เลือก'); window.location.href = 'add_menu.php';</script>";
    exit;
}

// จัดการการอัปเดตข้อมูลเมนู
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $calories = $_POST['calories'] ?? NULL;
    $spicy_level = $_POST['spicy_level'];
    $preparation_time = $_POST['preparation_time'] ?? NULL;
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    // อัปโหลดรูปภาพใหม่หากมีการเลือก
    $image_path = $menu_item['image'];
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $update_sql = "UPDATE menu_items SET 
                    menu_name = ?, category = ?, description = ?, price = ?, calories = ?, 
                    spicy_level = ?, preparation_time = ?, is_available = ?, image = ? 
                    WHERE id = ?";

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssdisisii", $name, $category, $description, $price, $calories,
                       $spicy_level, $preparation_time, $is_available, $image_path, $menu_id);

    if ($stmt->execute()) {
        echo "<script>alert('อัปเดตรายการเรียบร้อยแล้ว'); window.location.href = 'add_menu.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตรายการ');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขรายการอาหาร</title>
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-4">
        <h1>แก้ไขรายการอาหาร</h1>
        <form action="edit_menu.php?id=<?= $menu_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-floating mb-3 mb-md-0">
                        <input class="form-control" id="inputName" name="name" type="text" value="<?= htmlspecialchars($menu_item['name']); ?>" required />
                        <label for="inputName">ชื่อเมนู</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select class="form-select" id="inputCategory" name="category" required>
                            <option value="">เลือกหมวดหมู่</option>
                            <option value="อาหารแกง" <?= $menu_item['category'] == 'อาหารแกง' ? 'selected' : ''; ?>>อาหารแกง</option>
                            <option value="อาหารผัด" <?= $menu_item['category'] == 'อาหารผัด' ? 'selected' : ''; ?>>อาหารผัด</option>
                            <option value="อาหารทอด" <?= $menu_item['category'] == 'อาหารทอด' ? 'selected' : ''; ?>>อาหารทอด</option>
                            <option value="เครื่องดื่ม" <?= $menu_item['category'] == 'เครื่องดื่ม' ? 'selected' : ''; ?>>เครื่องดื่ม</option>
                        </select>
                        <label for="inputCategory">หมวดหมู่</label>
                    </div>
                </div>
            </div>

            <div class="form-floating mb-3">
                <textarea class="form-control" id="inputDescription" name="description" style="height: 100px"><?= htmlspecialchars($menu_item['description']); ?></textarea>
                <label for="inputDescription">คำอธิบายเมนู</label>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input class="form-control" id="inputPrice" name="price" type="number" step="0.01" value="<?= $menu_item['price']; ?>" required />
                        <label for="inputPrice">ราคา (บาท)</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input class="form-control" id="inputCalories" name="calories" type="number" value="<?= $menu_item['calories']; ?>" />
                        <label for="inputCalories">แคลอรี่</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="inputSpicyLevel" name="spicy_level">
                            <option value="ไม่เผ็ด" <?= $menu_item['spicy_level'] == 'ไม่เผ็ด' ? 'selected' : ''; ?>>ไม่เผ็ด</option>
                            <option value="เผ็ดน้อย" <?= $menu_item['spicy_level'] == 'เผ็ดน้อย' ? 'selected' : ''; ?>>เผ็ดน้อย</option>
                            <option value="เผ็ดปานกลาง" <?= $menu_item['spicy_level'] == 'เผ็ดปานกลาง' ? 'selected' : ''; ?>>เผ็ดปานกลาง</option>
                            <option value="เผ็ดมาก" <?= $menu_item['spicy_level'] == 'เผ็ดมาก' ? 'selected' : ''; ?>>เผ็ดมาก</option>
                        </select>
                        <label for="inputSpicyLevel">ระดับความเผ็ด</label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input class="form-control" id="inputPrepTime" name="preparation_time" type="number" value="<?= $menu_item['preparation_time']; ?>" />
                        <label for="inputPrepTime">เวลาเตรียม (นาที)</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <div class="form-check form-switch mt-3 pt-2">
                            <input class="form-check-input" type="checkbox" id="inputAvailable" name="is_available" <?= $menu_item['is_available'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="inputAvailable">พร้อมให้บริการ</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="inputImage" class="form-label">รูปภาพเมนู</label>
                <input class="form-control" type="file" id="inputImage" name="image" accept="image/*">
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-block">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
</body>
</html>
