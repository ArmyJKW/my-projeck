<?php
session_start();
if (isset($_POST['p_id'])) {
    include '../condb.php';

    // เรียกใช้ SweetAlert
    echo '
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';

    $p_id = $_POST['p_id'];
    $amount = $_POST['amount'];
    $status = $_POST['status']; // add หรือ del

    // 1. ดึงจำนวนเดิมมาก่อน เพื่อป้องกันสต็อกติดลบ
    $stmt = $conn->prepare("SELECT p_qty FROM tbl_product WHERE p_id = :p_id");
    $stmt->bindParam(':p_id', $p_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $old_qty = $row['p_qty'];

    // 2. คำนวณสต็อกใหม่
    if ($status == 'add') {
        // ถ้าเป็นการเพิ่ม
        $sql = "UPDATE tbl_product SET p_qty = p_qty + :amount WHERE p_id = :p_id";
    } elseif ($status == 'del') {
        // ถ้าเป็นการลด ต้องเช็คก่อนว่าพอให้ลดไหม
        if ($old_qty < $amount) {
            echo '<script>
                 setTimeout(function() {
                  swal({
                      title: "เกิดข้อผิดพลาด",
                      text: "จำนวนสินค้าไม่พอให้ตัดสต็อก!",
                      type: "warning"
                  }, function() {
                      window.location = "stock_product.php"; 
                  });
                }, 100);
            </script>';
            exit; // จบการทำงานทันที
        }
        $sql = "UPDATE tbl_product SET p_qty = p_qty - :amount WHERE p_id = :p_id";
    }

    // 3. บันทึกลงฐานข้อมูล
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
    $stmt->bindParam(':p_id', $p_id, PDO::PARAM_INT);
    $result = $stmt->execute();

    if ($result) {
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "อัปเดตสต็อกสำเร็จ",
                  type: "success",
                  timer: 1000,
                  showConfirmButton: false
              }, function() {
                  window.location = "stock_product.php"; 
              });
            }, 1000);
        </script>';
    } else {
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "เกิดข้อผิดพลาด",
                  type: "error"
              }, function() {
                  window.location = "stock_product.php";
              });
            }, 1000);
        </script>';
    }
    $conn = null;
}
?>