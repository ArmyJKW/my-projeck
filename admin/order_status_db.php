<?php
session_start();
include '../condb.php';

if(isset($_POST['order_id']) && isset($_POST['order_status'])){
    
    // เรียกใช้ SweetAlert
    echo '
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';

    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    // อัปเดตสถานะ
    $sql = "UPDATE tbl_order SET order_status = :order_status WHERE order_id = :order_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':order_status', $order_status, PDO::PARAM_INT);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $result = $stmt->execute();

    if($result){
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "อัปเดตสถานะสำเร็จ",
                  type: "success",
                  timer: 1000,
                  showConfirmButton: false
              }, function() {
                  window.location = "order.php?act=detail&order_id='.$order_id.'"; 
              });
            }, 1000);
        </script>';
    }else{
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "เกิดข้อผิดพลาด",
                  type: "error"
              }, function() {
                  window.history.back();
              });
            }, 1000);
        </script>';
    }
}
?>