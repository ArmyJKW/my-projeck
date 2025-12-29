<?php
session_start();
include 'condb.php';
$order_id = $_GET['order_id'];

// ดึงข้อมูลออเดอร์มาแสดงยอดเงินที่ต้องจ่าย
$stmt = $conn->prepare("SELECT * FROM tbl_order WHERE order_id = :order_id");
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row['order_status'] != 1){
    // ถ้าสถานะไม่ใช่รอชำระเงิน ให้เด้งกลับ
    echo "<script>window.location='my_order.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งชำระเงิน</title>
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
  <?php include 'header.php'; ?>
  
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">แจ้งชำระเงิน <small>Order ID: <?php echo $order_id; ?></small></h1>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">กรอกรายละเอียดการโอนเงิน</h3>
              </div>
              
              <form action="payment_db.php" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="alert alert-info">
                     ยอดที่ต้องชำระ: <b>฿<?php echo number_format($row['total_price'], 2); ?></b>
                  </div>
                  
                  <div class="form-group">
                    <label>วันที่โอนเงิน</label>
                    <input type="datetime-local" name="pay_date" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label>จำนวนเงินที่โอน</label>
                    <input type="number" step="0.01" name="pay_amount" class="form-control" value="<?php echo $row['total_price']; ?>" required>
                  </div>

                  <div class="form-group">
                    <label>หลักฐานการโอนเงิน (สลิป)</label>
                    <div class="custom-file">
                      <input type="file" name="pay_slip" class="custom-file-input" id="customFile" accept="image/*" required>
                      <label class="custom-file-label" for="customFile">เลือกไฟล์รูปภาพ</label>
                    </div>
                  </div>
                  
                  <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                </div>

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary btn-block">ยืนยันการแจ้งชำระเงิน</button>
                  <a href="my_order.php" class="btn btn-default btn-block">ยกเลิก</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'footer.php'; ?>
</div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script>
// โค้ดสำหรับแสดงชื่อไฟล์เมื่อเลือกรูป
$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
</script>
</body>
</html>