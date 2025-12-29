<?php
session_start();
include 'condb.php';

// 1. เช็คว่าล็อกอินหรือยัง
if (!isset($_SESSION['m_id'])) {
    Header("Location: login.php");
    exit();
}

$m_id = $_SESSION['m_id'];

// 2. ดึงข้อมูลออเดอร์ของสมาชิกคนนี้ (เรียงจากล่าสุดไปเก่าสุด)
$stmt = $conn->prepare("SELECT * FROM tbl_order WHERE m_id = :m_id ORDER BY order_id DESC");
$stmt->bindParam(':m_id', $m_id, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการสั่งซื้อ</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  <?php include 'header.php'; ?> 

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"> ประวัติการสั่งซื้อ <small>My Orders</small></h1>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="card card-primary card-outline">
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr class="bg-light">
                      <th width="15%">รหัสใบสั่งซื้อ</th>
                      <th width="20%">วันที่สั่งซื้อ</th>
                      <th width="15%">ยอดสุทธิ</th>
                      <th width="15%">สถานะ</th>
                      <th width="35%">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    if(count($orders) > 0){
                        foreach ($orders as $row) { 
                            $status = $row['order_status'];
                            // กำหนดสีและข้อความสถานะ
                            if ($status == 1) {
                                $status_show = '<span class="badge badge-warning">รอชำระเงิน</span>';
                                // ปุ่มแจ้งชำระเงิน (แสดงเฉพาะตอนสถานะรอชำระ)
                                $btn_pay = '<a href="payment.php?order_id='.$row['order_id'].'" class="btn btn-info btn-sm"><i class="fas fa-file-invoice-dollar"></i> แจ้งชำระเงิน</a>';
                            } elseif ($status == 2) {
                                $status_show = '<span class="badge badge-success">ชำระเงินแล้ว</span>';
                                $btn_pay = ''; // จ่ายแล้วไม่ต้องโชว์ปุ่มจ่าย
                            } elseif ($status == 3) {
                                $status_show = '<span class="badge badge-danger">ยกเลิก</span>';
                                $btn_pay = '';
                            } else {
                                $status_show = '<span class="badge badge-secondary">อื่นๆ</span>';
                                $btn_pay = '';
                            }
                    ?>
                    <tr>
                      <td>Ref-<?php echo str_pad($row['order_id'], 6, '0', STR_PAD_LEFT); ?></td>
                      <td><?php echo date('d/m/Y H:i', strtotime($row['order_date'])); ?></td>
                      <td><b><?php echo number_format($row['total_price'], 2); ?></b> บาท</td>
                      <td><?php echo $status_show; ?></td>
                      <td>
                        <a href="my_order_detail.php?order_id=<?php echo $row['order_id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i> ดูรายการ
                        </a>
                        <?php echo $btn_pay; ?>
                      </td>
                    </tr>
                    <?php } 
                    } else { ?>
                        <tr>
                            <td colspan="5" align="center" class="text-muted p-4">
                                ยังไม่มีรายการสั่งซื้อ <br>
                                <a href="index.php" class="btn btn-primary mt-2">ไปเลือกซื้อสินค้า</a>
                            </td>
                        </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
</div>
</body>
</html>