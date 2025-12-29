<?php
session_start();
include 'condb.php';

// SweetAlert
echo '<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';

if(isset($_POST['order_id'])){
    $order_id = $_POST['order_id'];
    $pay_date = $_POST['pay_date'];
    $pay_amount = $_POST['pay_amount'];
    
    // อัปโหลดรูปภาพ
    $date1 = date("Ymd_His");
    $numrand = (mt_rand());
    $upload = $_FILES['pay_slip']['name'];

    if($upload !='') {
        // ตัดนามสกุลไฟล์
        $typefile = strrchr($_FILES['pay_slip']['name'],"."); 
        
        // สร้างโฟลเดอร์ slip ถ้ายังไม่มี (คุณต้องไปสร้างโฟลเดอร์ชื่อ 'slip' ในโปรเจกต์ หรือจะใช้ p_img ก็ได้)
        // แนะนำให้สร้างโฟลเดอร์ img/slip/ หรือ admin/slip/
        $path = "admin/slip/"; // ** อย่าลืมสร้างโฟลเดอร์ slip ใน admin นะครับ **
        
        // ตั้งชื่อไฟล์ใหม่
        $newname = 'slip_'.$numrand.$date1.$typefile;
        $path_copy = $path.$newname;

        // คัดลอกไฟล์
        move_uploaded_file($_FILES['pay_slip']['tmp_name'], $path_copy);
    } else {
        $newname = '';
    }

    // อัปเดตข้อมูลลง tbl_order
    // เปลี่ยนสถานะเป็น 2 (รอตรวจสอบ/ชำระแล้ว)
    $sql = "UPDATE tbl_order SET 
            order_status = 2,
            pay_date = :pay_date,
            pay_amount = :pay_amount,
            pay_slip = :pay_slip
            WHERE order_id = :order_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pay_date', $pay_date, PDO::PARAM_STR);
    $stmt->bindParam(':pay_amount', $pay_amount, PDO::PARAM_STR);
    $stmt->bindParam(':pay_slip', $newname, PDO::PARAM_STR);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $result = $stmt->execute();

    if($result){
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "แจ้งชำระเงินสำเร็จ",
                  text: "กรุณารอเจ้าหน้าที่ตรวจสอบ",
                  type: "success"
              }, function() {
                  window.location = "my_order.php";
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
                  window.location = "my_order.php";
              });
            }, 1000);
        </script>';
    }
}
?>