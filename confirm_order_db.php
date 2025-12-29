<?php
session_start();
include 'condb.php';

// เรียกใช้ SweetAlert
echo '
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';

if (isset($_POST['m_id'])) {
    
    // ใช้ Transaction เพื่อความปลอดภัยของข้อมูล (ถ้าบันทึกไม่ครบ ให้ยกเลิกทั้งหมด)
    try {
        $conn->beginTransaction();

        // 1. รับค่าจากฟอร์ม
        $m_id = $_POST['m_id'];
        $total_price = $_POST['total_price'];
        $order_status = 1; // 1 = รอชำระเงิน
        $order_date = date('Y-m-d H:i:s');
        
        // (อาจจะเก็บชื่อที่อยู่ผู้รับลงใน tbl_order ด้วยก็ได้ ถ้าตารางมีฟิลด์รองรับ)
        
        // 2. บันทึกลง tbl_order
        $sql_order = "INSERT INTO tbl_order (m_id, order_status, order_date, total_price) 
                      VALUES (:m_id, :order_status, :order_date, :total_price)";
        $stmt = $conn->prepare($sql_order);
        $stmt->bindParam(':m_id', $m_id, PDO::PARAM_INT);
        $stmt->bindParam(':order_status', $order_status, PDO::PARAM_INT);
        $stmt->bindParam(':order_date', $order_date, PDO::PARAM_STR);
        $stmt->bindParam(':total_price', $total_price, PDO::PARAM_STR);
        $stmt->execute();

        // ดึง order_id ล่าสุดที่เพิ่ง insert เข้าไป
        $order_id = $conn->lastInsertId();

        // 3. วนลูปบันทึกรายละเอียดสินค้า (tbl_order_detail) และตัดสต็อก
        foreach ($_SESSION['cart'] as $p_id => $qty) {
            
            // ดึงราคาสินค้าปัจจุบัน (เพื่อความชัวร์)
            $stmt_prd = $conn->prepare("SELECT p_price, p_qty FROM tbl_product WHERE p_id = :p_id");
            $stmt_prd->bindParam(':p_id', $p_id, PDO::PARAM_INT);
            $stmt_prd->execute();
            $row_prd = $stmt_prd->fetch(PDO::FETCH_ASSOC);
            
            $total = $row_prd['p_price'] * $qty;

            // บันทึกลง tbl_order_detail
            $sql_detail = "INSERT INTO tbl_order_detail (order_id, p_id, p_qty, total) 
                           VALUES (:order_id, :p_id, :p_qty, :total)";
            $stmt_detail = $conn->prepare($sql_detail);
            $stmt_detail->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $stmt_detail->bindParam(':p_id', $p_id, PDO::PARAM_INT);
            $stmt_detail->bindParam(':p_qty', $qty, PDO::PARAM_INT);
            $stmt_detail->bindParam(':total', $total, PDO::PARAM_STR);
            $stmt_detail->execute();

            // ตัดสต็อกสินค้า (Optional: ถ้าต้องการตัดทันที)
            $sql_update_stock = "UPDATE tbl_product SET p_qty = p_qty - :qty WHERE p_id = :p_id";
            $stmt_stock = $conn->prepare($sql_update_stock);
            $stmt_stock->bindParam(':qty', $qty, PDO::PARAM_INT);
            $stmt_stock->bindParam(':p_id', $p_id, PDO::PARAM_INT);
            $stmt_stock->execute();
        }

        // ยืนยันการทำงานทั้งหมด
        $conn->commit();

        // 4. ล้างตะกร้าสินค้า
        unset($_SESSION['cart']);

        // แจ้งเตือนสำเร็จ
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "สั่งซื้อสำเร็จ!",
                  text: "กรุณาชำระเงินในขั้นตอนถัดไป",
                  type: "success",
                  timer: 2000,
                  showConfirmButton: false
              }, function() {
                  // ส่งไปหน้าประวัติการสั่งซื้อ หรือ หน้าชำระเงิน
                  window.location = "my_order.php"; 
              });
            }, 1000);
        </script>';

    } catch (Exception $e) {
        // ถ้ามี error ให้ยกเลิกการทำงานทั้งหมด
        $conn->rollBack();
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "เกิดข้อผิดพลาด",
                  text: "ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่",
                  type: "error"
              }, function() {
                  window.location = "cart.php";
              });
            }, 1000);
        </script>';
    }

} else {
    Header("Location: index.php");
}
?>