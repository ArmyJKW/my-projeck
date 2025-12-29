<?php 
session_start();
include("../condb.php"); 

// 1. ดึงข้อมูลมาแสดงใน Dashboard

// 1.1 นับจำนวนสินค้าทั้งหมด
$stmtPrd = $conn->prepare("SELECT COUNT(*) as total_prd FROM tbl_product");
$stmtPrd->execute();
$rowPrd = $stmtPrd->fetch(PDO::FETCH_ASSOC);
$total_prd = $rowPrd['total_prd'];

// 1.2 นับจำนวนออเดอร์ที่รอชำระ (status=1)
$stmtOrder = $conn->prepare("SELECT COUNT(*) as total_order FROM tbl_order WHERE order_status=1");
$stmtOrder->execute();
$rowOrder = $stmtOrder->fetch(PDO::FETCH_ASSOC);
$total_order_wait = $rowOrder['total_order'];

// 1.3 นับจำนวนสมาชิก
$stmtMem = $conn->prepare("SELECT COUNT(*) as total_mem FROM tbl_member");
$stmtMem->execute();
$rowMem = $stmtMem->fetch(PDO::FETCH_ASSOC);
$total_mem = $rowMem['total_mem'];

// 1.4 รวมยอดขายรายเดือน (เฉพาะสถานะชำระแล้ว = 2)
$month = date('m');
$year = date('Y');
$stmtSale = $conn->prepare("SELECT SUM(total_price) as total_sale FROM tbl_order WHERE order_status=2 AND MONTH(order_date)=:m AND YEAR(order_date)=:y");
$stmtSale->bindParam(':m', $month, PDO::PARAM_INT);
$stmtSale->bindParam(':y', $year, PDO::PARAM_INT);
$stmtSale->execute();
$rowSale = $stmtSale->fetch(PDO::FETCH_ASSOC);
$total_sale_month = ($rowSale['total_sale']) ? $rowSale['total_sale'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<?php $menu = "index";?>
<?php include'head.php'; ?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <?php include'nav.php'; ?>
  <?php include'menu.php'; ?>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
          </div>
        </div>
      </div></div>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo $total_order_wait; ?></h3>
                <p>ออเดอร์ใหม่ (รอชำระ)</p>
              </div>
              <div class="icon">
                <i class="fas fa-shopping-cart"></i>
              </div>
              <a href="order.php?act=wait" class="small-box-footer">จัดการออเดอร์ <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php echo $total_prd; ?></h3>
                <p>รายการสินค้าทั้งหมด</p>
              </div>
              <div class="icon">
                <i class="fas fa-box-open"></i>
              </div>
              <a href="product.php" class="small-box-footer">ดูสินค้า <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?php echo $total_mem; ?></h3>
                <p>สมาชิกทั้งหมด</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
              <a href="member.php" class="small-box-footer">ดูสมาชิก <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>฿<?php echo number_format($total_sale_month, 0); ?></h3>
                <p>ยอดขายเดือนนี้</p>
              </div>
              <div class="icon">
                <i class="fas fa-chart-pie"></i>
              </div>
              <a href="report.php" class="small-box-footer">ดูรายงาน <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header border-transparent">
                        <h3 class="card-title">สินค้าที่มีสต็อกน้อย (ต้องรีบเติม)</h3>
                        <div class="card-tools">
                          <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                          </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>สินค้า</th>
                                    <th>คงเหลือ</th>
                                    <th>สถานะ</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                // ดึงสินค้าที่เหลือน้อยกว่า 10 ชิ้น
                                $stmtLow = $conn->prepare("SELECT * FROM tbl_product WHERE p_qty < 10 ORDER BY p_qty ASC LIMIT 5");
                                $stmtLow->execute();
                                while($rowLow = $stmtLow->fetch(PDO::FETCH_ASSOC)){
                                ?>
                                <tr>
                                    <td><a href="#"><?php echo $rowLow['p_id']; ?></a></td>
                                    <td><?php echo $rowLow['p_name']; ?></td>
                                    <td><span class="badge badge-danger"><?php echo $rowLow['p_qty']; ?></span></td>
                                    <td>
                                        <a href="stock_product.php" class="btn btn-sm btn-warning">เติมของ</a>
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

      </div></section>
    </div>
  <?php include'footer.php'; ?>
  
  <aside class="control-sidebar control-sidebar-dark">
    </aside>
  </div>
<?php include'script.php'; ?>
</body>
</html>