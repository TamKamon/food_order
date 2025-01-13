<?php
// เริ่ม session
session_start();

// ลบข้อมูลใน session ทั้งหมด
session_unset();

// ทำลาย session
session_destroy();

// เปลี่ยนเส้นทางไปยังหน้า login หรือหน้าอื่นที่คุณต้องการ
echo "<script type='text/javascript'>
        alert('คุณต้องการออกจากระบบ');
        window.location.href = 'login.php';
      </script>";
exit;
?>
