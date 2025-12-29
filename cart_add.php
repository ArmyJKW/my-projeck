<?php
session_start();
// รับค่า id สินค้าที่ส่งมา
$p_id = $_GET['p_id'];

// ถ้ายังไม่มีตะกร้า ให้สร้างใหม่
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}

// ถ้ามีสินค้านี้อยู่แล้ว ให้บวกจำนวนเพิ่ม
if(in_array($p_id, array_keys($_SESSION['cart']))){
    $_SESSION['cart'][$p_id] += 1;
} else {
    // ถ้ายังไม่มี ให้เพิ่มเข้าไปเริ่มที่ 1 ชิ้น
    $_SESSION['cart'][$p_id] = 1;
}

// เด้งกลับไปหน้าเดิม หรือหน้าตะกร้า
echo "<script>alert('เพิ่มสินค้าลงตะกร้าแล้ว'); window.location='index.php';</script>";
?>