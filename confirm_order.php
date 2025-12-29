<?php
session_start();
include 'condb.php';

// 1. เช็คว่าล็อกอินหรือยัง
if (!isset($_SESSION['m_id'])) {
    Header("Location: login.php"); // ถ้ายังไม่ล็อกอิน ไล่ไปหน้า login
    exit();
}

// 2. เช็คว่ามีสินค้าในตะกร้าไหม
if (empty($_SESSION['cart'])) {
    Header("Location: index.php"); // ถ้าตะกร้าว่าง ไล่กลับหน้าแรก
    exit();
}

// 3. ดึงข้อมูลสมาชิกมาแสดงในฟอร์ม
$m_id = $_SESSION['m_id'];
$stmt = $conn->prepare("SELECT * FROM tbl_member WHERE m_id = :m_id");
$stmt->bindParam(':m_id', $m_id, PDO::PARAM_INT);
$stmt->execute();
$row_member = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันการสั่งซื้อ</title>
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
            <h1 class="m-0"> ยืนยันการสั่งซื้อ <small>Checkout</small></h1>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container">
        <div class="row">
          
          <div class="col-md-8">
            <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title">รายการสินค้าในตะกร้า</h3>
              </div>
              <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="50%">สินค้า</th>
                            <th width="15%" class="text-center">ราคา</th>
                            <th width="10%" class="text-center">จำนวน</th>
                            <th width="20%" class="text-right">รวม</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total = 0;
                    $i = 1;
                    foreach ($_SESSION['cart'] as $p_id => $qty) {
                        $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE p_id = :p_id");
                        $stmt->bindParam(':p_id', $p_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $sum = $row['p_price'] * $qty;
                        $total += $sum;
                    ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $row['p_name']; ?></td>
                            <td align="center"><?php echo number_format($row['p_price'], 2); ?></td>
                            <td align="center"><?php echo $qty; ?></td>
                            <td align="right"><?php echo number_format($sum, 2); ?></td>
                        </tr>
                    <?php } ?>
                        <tr>
                            <td colspan="4" align="right"><b>ยอดรวมสุทธิ</b></td>
                            <td align="right" class="bg-gray"><b><?php echo number_format($total, 2); ?></b></td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>
            <a href="cart.php" class="btn btn-secondary"> <i class="fas fa-arrow-left"></i> กลับไปแก้ไขตะกร้า</a>
          </div>

          <div class="col-md-4">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">ที่อยู่ในการจัดส่ง</h3>
              </div>
              <form action="confirm_order_db.php" method="POST">
                  <div class="card-body">
                    <div class="form-group">
                        <label>ชื่อ-นามสกุล ผู้รับ</label>
                        <input type="text" name="name" class="form-control" value="<?php echo $row_member['m_name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>ที่อยู่จัดส่ง</label>
                        <textarea name="address" class="form-control" rows="3" required><?php echo $row_member['m_address']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>เบอร์โทรศัพท์</label>
                        <input type="text" name="tel" class="form-control" value="<?php echo $row_member['m_tel']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>อีเมล</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $row_member['m_email']; ?>" required>
                    </div>
                    
                    <input type="hidden" name="total_price" value="<?php echo $total; ?>">
                    <input type="hidden" name="m_id" value="<?php echo $m_id; ?>">

                    <button type="submit" class="btn btn-success btn-block btn-lg">
                        ยืนยันการสั่งซื้อ <i class="fas fa-check-circle"></i>
                    </button>
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
</body>
</html>