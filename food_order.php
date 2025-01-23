<?php
// food_order.php
session_start();
if (isset($_GET['table_number']) && isset($_GET['id'])) {
    $_SESSION['current_table'] = [
        'table_number' => $_GET['table_number'],
        'table_id' => $_GET['id']
    ];
} else {
    // Redirect if no table information
    header('Location: error.php?message=No table information provided');
    exit;
}

require 'admin/connect.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลรายการอาหารที่มีอยู่ (is_available = 1)
$sql = "SELECT * FROM menu_items WHERE is_available = 1 ORDER BY category, name";
$result = $conn->query($sql);

// สร้างตารางสำหรับเก็บข้อมูลการสั่งอาหาร
$create_orders_table = "CREATE TABLE IF NOT EXISTS orders (
    order_id VARCHAR(20) PRIMARY KEY,
    table_id INT NOT NULL,
    customer_name VARCHAR(100),
    total_amount DECIMAL(10,2),
    order_status ENUM('pending', 'preparing', 'ready', 'served', 'cancelled') DEFAULT 'pending',
    order_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$create_order_items_table = "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(20),
    item_id INT,
    quantity INT,
    unit_price DECIMAL(10,2),
    special_instructions TEXT
)";

$conn->query($create_orders_table);
$conn->query($create_order_items_table);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สั่งอาหาร - ร้านคนหัวครัว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .menu-item-card {
            transition: transform 0.2s;
        }
        .menu-item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .spicy-level {
            font-size: 0.8rem;
            padding: 0.2rem 0.5rem;
            border-radius: 0.25rem;
            margin-left: 0.5rem;
        }
        .preparation-time {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255,255,255,0.9);
            padding: 0.3rem 0.6rem;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container py-4">
    <div class="alert alert-info mb-4">
        <h4 class="mb-0">โต๊ะหมายเลข: <?php echo htmlspecialchars($_SESSION['current_table']['table_number']); ?></h4>
    </div>
    <h1 class="text-center mb-4">เมนูอาหาร</h1>
        
        <!-- ฟอร์มค้นหา -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <input type="text" id="searchMenu" class="form-control" placeholder="ค้นหาเมนูอาหาร...">
            </div>
        </div>

        <!-- แสดงรายการอาหาร -->
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while($item = $result->fetch_assoc()) {
            ?>
                <div class="col-md-4 col-sm-6 mb-4 menu-item">
                    <div class="card menu-item-card h-100">
                        <?php if ($item['image_url']): ?>
                            <img src="admin/<?php echo htmlspecialchars($item['image_url']); ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        
                        <div class="preparation-time">
                            <i class="fas fa-clock"></i> <?php echo $item['preparation_time']; ?> นาที
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($item['name']); ?>
                                <?php if ($item['spicy_level'] != 'ไม่เผ็ด'): ?>
                                    <span class="badge bg-danger spicy-level">
                                        <?php echo htmlspecialchars($item['spicy_level']); ?>
                                    </span>
                                <?php endif; ?>
                            </h5>
                            <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                            <p class="card-text">
                                <strong>ราคา: <?php echo number_format($item['price'], 2); ?> บาท</strong>
                            </p>
                            <button type="button" class="btn btn-primary w-100"
                                    onclick="openOrderModal(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                สั่งอาหาร
                            </button>
                        </div>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<div class='col-12'><p class='text-center'>ไม่พบรายการอาหาร</p></div>";
            }
            ?>
        </div>
    </div>

    <!-- Modal สำหรับสั่งอาหาร -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">สั่งอาหาร</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm">
                        <input type="hidden" id="itemId" name="itemId">
                        <div class="mb-3">
                            <label class="form-label">ชื่อรายการ: <span id="modalItemName"></span></label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ราคา: <span id="modalItemPrice"></span> บาท</label>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">จำนวน:</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   value="1" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="specialInstructions" class="form-label">คำสั่งพิเศษ:</label>
                            <textarea class="form-control" id="specialInstructions" name="specialInstructions"
                                      rows="2" placeholder="เช่น ไม่ใส่ผัก, เผ็ดน้อย"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="submitOrder()">ยืนยันการสั่ง</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentItem = null;
        const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));

        function openOrderModal(item) {
            currentItem = item;
            document.getElementById('itemId').value = item.item_id;
            document.getElementById('modalItemName').textContent = item.name;
            document.getElementById('modalItemPrice').textContent = item.price;
            orderModal.show();
        }

        function submitOrder() {
            const formData = new FormData(document.getElementById('orderForm'));
            
            fetch('process_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('สั่งอาหารเรียบร้อยแล้ว!');
                    orderModal.hide();
                } else {
                    alert('เกิดข้อผิดพลาด: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาดในการสั่งอาหาร');
            });
        }

        // ฟังก์ชันค้นหาเมนู
        document.getElementById('searchMenu').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            document.querySelectorAll('.menu-item').forEach(item => {
                const itemName = item.querySelector('.card-title').textContent.toLowerCase();
                const itemDesc = item.querySelector('.card-text').textContent.toLowerCase();
                if (itemName.includes(searchText) || itemDesc.includes(searchText)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>