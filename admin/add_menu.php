<?php
// เชื่อมต่อฐานข้อมูล
require 'connect.php';

// ตรวจสอบการลบเมนู
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM menu_items WHERE item_id = $delete_id";
    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>alert('ลบรายการเรียบร้อยแล้ว');</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบรายการ');</script>";
    }
}

// Prepare SQL query to select all menu items
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Sidenav Light - SB Admin</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.html">Start Bootstrap</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="#!">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="index.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">Interface</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Layouts
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="layout-static.html">Static Navigation</a>
                                    <a class="nav-link" href="layout-sidenav-light.htmlฃ">Light Sidenav</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Pages
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                        Authentication
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="login.html">Login</a>
                                            <a class="nav-link" href="register.html">Register</a>
                                            <a class="nav-link" href="password.html">Forgot Password</a>
                                        </nav>
                                    </div>
                                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                        Error
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="401.html">401 Page</a>
                                            <a class="nav-link" href="404.html">404 Page</a>
                                            <a class="nav-link" href="500.html">500 Page</a>
                                        </nav>
                                    </div>
                                </nav>
                            </div>
                            <div class="sb-sidenav-menu-heading">Addons</div>
                            <a class="nav-link" href="charts.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Charts
                            </a>
                            <a class="nav-link" href="tables.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                Tables
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        Start Bootstrap
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">การจัดการรายการอาหาร</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                            <li class="breadcrumb-item active">Sidenav Light</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-body">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                            <div class="card-header"><h3 class="text-center font-weight-light my-4">เพิ่มข้อมูลรายการอาหาร</h3></div>
                                <div class="card-body">
                                    <form action="add_menu_item.php" method="POST" enctype="multipart/form-data">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputName" name="name" type="text" placeholder="ชื่อเมนู" required />
                                                    <label for="inputName">ชื่อเมนู</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select" id="inputCategory" name="category" required>
                                                        <option value="">เลือกหมวดหมู่</option>
                                                        <option value="อาหารแกง">อาหารแกง</option>
                                                        <option value="อาหารผัด">อาหารผัด</option>
                                                        <option value="อาหารทอด">อาหารทอด</option>
                                                        <option value="เครื่องดื่ม">เครื่องดื่ม</option>
                                                    </select>
                                                    <label for="inputCategory">หมวดหมู่</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <textarea class="form-control" id="inputDescription" name="description" placeholder="คำอธิบายเมนู" style="height: 100px"></textarea>
                                            <label for="inputDescription">คำอธิบายเมนู</label>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputPrice" name="price" type="number" step="0.01" placeholder="ราคา" required />
                                                    <label for="inputPrice">ราคา (บาท)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputCalories" name="calories" type="number" placeholder="แคลอรี่" />
                                                    <label for="inputCalories">แคลอรี่</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <select class="form-select" id="inputSpicyLevel" name="spicy_level">
                                                        <option value="ไม่เผ็ด">ไม่เผ็ด</option>
                                                        <option value="เผ็ดน้อย">เผ็ดน้อย</option>
                                                        <option value="เผ็ดปานกลาง">เผ็ดปานกลาง</option>
                                                        <option value="เผ็ดมาก">เผ็ดมาก</option>
                                                    </select>
                                                    <label for="inputSpicyLevel">ระดับความเผ็ด</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputPrepTime" name="preparation_time" type="number" placeholder="เวลาเตรียม" />
                                                    <label for="inputPrepTime">เวลาเตรียม (นาที)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <div class="form-check form-switch mt-3 pt-2">
                                                        <input class="form-check-input" type="checkbox" id="inputAvailable" name="is_available" checked>
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
                                            <button type="submit" class="btn btn-primary btn-block">เพิ่มรายการอาหาร</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable Example
                            </div>
                            <div class="card-body">
                            <table id="datatablesSimple">
                                
        <thead>
            <tr>
                <th>รหัสเมนู</th>
                <th>ชื่อเมนู</th>
                <th>คำอธิบาย</th>
                <th>ราคา</th>
                <th>ประเภท</th>
                <th>ระดับความเผ็ด</th>
                <th>เวลาเตรียม</th>
                <th>แคลอรี่</th>
                <th>สถานะ</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result->num_rows > 0) {
                // Output data of each row
                while($item = $result->fetch_assoc()) {
            ?>
            <tr>
                <td><?php echo htmlspecialchars($item['item_id']); ?></td>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo htmlspecialchars($item['description'] ?: 'ไม่มีคำอธิบาย'); ?></td>
                <td><?php echo number_format($item['price'], 2); ?> บาท</td>
                <td><?php echo htmlspecialchars($item['category']); ?></td>
                <td><?php echo htmlspecialchars($item['spicy_level']); ?></td>
                <td><?php echo htmlspecialchars($item['preparation_time']); ?> นาที</td>
                <td><?php echo htmlspecialchars($item['calories']); ?></td>
                <td><?php echo $item['is_available'] ? 'พร้อมจำหน่าย' : 'ไม่พร้อมจำหน่าย'; ?></td>
                <td>
                    <a href="edit_menu.php?id=<?=$item['item_id']; ?>" class="btn btn-warning">แก้ไข</a>
                    <a href="add_menu.php?delete_id=<?=$item['item_id']; ?>" class="btn btn-danger" onclick="return confirm('ยืนยันการลบรายการนี้หรือไม่?');">ลบ</a>
                </td>
            </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='9'>ไม่พบข้อมูลเมนู</td></tr>";
            }
            ?>
        </tbody>
    </table>
                            </div>
                        </div>
                        </div>
                        
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>

