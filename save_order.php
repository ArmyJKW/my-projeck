<?php
session_start();
require_once("condb.php"); // เรียกใช้การเชื่อมต่อฐานข้อมูลของคุณ

// เช็คว่าล็อกอินหรือยัง (ถ้าต้องเป็นสมาชิกก่อนซื้อ)
if (!isset($_SESSION['m_id'])) {
    // ถ้ายังไม่ล็อกอิน ให้ไปหน้า login (สมมติว่าใช้ m_id จากตาราง tbl_member)
    // หรือถ้าไม่บังคับสมาชิก ก็อาจจะ hardcode เป็น 0 หรือ Guest
    echo "<script>alert('กรุณาล็อกอินก่อนทำรายการ'); window.location='login.php';</script>"; 
    exit;
}

// เตรียมข้อมูล
$m_id = $_SESSION['m_id']; 
$order_date = date("Y-m-d H:i:s");
$order_status = 1; // 1=รอชำระเงิน
$total_price = 0;

// คำนวณราคารวมก่อน (Loop รอบแรก)
foreach ($_SESSION['cart'] as $p_id => $qty) {
    $stmt = $conn->prepare("SELECT p_price FROM tbl_product WHERE p_id = ?");
    $stmt->execute([$p_id]);
    $row = $stmt->fetch();
    $total_price += $row['p_price'] * $qty;
}

// 1. บันทึกหัวบิล (tbl_order)
$sql_head = "INSERT INTO tbl_order (m_id, order_status, total_price, order_date) VALUES (?, ?, ?, ?)";
$stmt_head = $conn->prepare($sql_head);
$stmt_head->execute([$m_id, $order_status, $total_price, $order_date]);

// หา order_id ล่าสุดที่เพิ่งเพิ่ม
$order_id = $conn->lastInsertId();

// 2. บันทึกรายละเอียด (tbl_order_detail) (Loop รอบสอง)
foreach ($_SESSION['cart'] as $p_id => $qty) {
    $stmt = $conn->prepare("SELECT p_name, p_price FROM tbl_product WHERE p_id = ?");
    $stmt->execute([$p_id]);
    $row = $stmt->fetch();

    $p_name = $row['p_name'];
    $p_price = $row['p_price'];
    $total = $p_price * $qty;

    $sql_detail = "INSERT INTO tbl_order_detail (order_id, p_id, p_name, p_price, p_qty, total) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_detail = $conn->prepare($sql_detail);
    $stmt_detail->execute([$order_id, $p_id, $p_name, $p_price, $qty, $total]);
}

// 3. ล้างตะกร้า
unset($_SESSION['cart']);

echo "<script>alert('บันทึกการสั่งซื้อเรียบร้อย! เลขที่ใบสั่งซื้อ $order_id'); window.location='index.php';</script>";
?>