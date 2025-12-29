<?php
// เริ่ม Session (ใส่ @ เพื่อปิดแจ้งเตือน Notice หากมีการ start ไปแล้ว)
@session_start(); 

// 1. เชื่อมต่อฐานข้อมูล (จำเป็นต้องใช้เพื่อเช็คสิทธิ์ล่าสุดจาก DB)
require_once '../condb.php'; 

// 2. ถ้าไม่มี Session m_id เลย (ยังไม่ล็อกอิน) -> ดีดไปหน้า Login
if(!isset($_SESSION['m_id'])){
    header("Location: ../login.php");
    exit();
}

// 3. ดึงข้อมูลล่าสุดจากฐานข้อมูล (Check Real-time)
// วิธีนี้ชัวร์กว่าเช็คจาก Session เพราะถ้าแก้ใน DB ปุ๊บ สิทธิ์จะเปลี่ยนทันที
$checkId = $_SESSION['m_id'];
$stmtCheck = $conn->prepare("SELECT m_level, m_status FROM tbl_member WHERE m_id = :id");
$stmtCheck->bindParam(':id', $checkId, PDO::PARAM_INT);
$stmtCheck->execute();
$rowCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC);

// 4. ตรวจสอบเงื่อนไข: ต้องเป็น admin และ status ต้องเป็น 1 (Active)
// ถ้าหาไม่เจอ ($rowCheck เป็น false) หรือไม่ใช่ admin หรือ status ไม่ใช่ 1 -> ดีดออก
if(!$rowCheck || $rowCheck['m_level'] != 'admin' || $rowCheck['m_status'] != 1) {
    echo '
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
    <script>
        setTimeout(function() {
            swal({
                title: "คุณไม่มีสิทธิ์ใช้งานหน้านี้",
                text: "สถานะของคุณอาจถูกระงับ หรือไม่ใช่ผู้ดูแลระบบ",
                type: "error"
            }, function() {
                window.location = "../logout.php"; // ส่งไปหน้า Logout เพื่อล้าง Session
            });
        }, 100);
    </script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ShoppingCart | Admin</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
</head>