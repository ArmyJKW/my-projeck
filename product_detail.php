<?php
session_start();
include 'condb.php';

// 1. รับค่า p_id และตรวจสอบว่ามีค่าส่งมาหรือไม่
$p_id = isset($_GET['p_id']) ? $_GET['p_id'] : '';

if($p_id == ''){
    // ถ้าไม่มี ID ส่งมา ให้กลับหน้าแรกทันที
    header("Location: index.php");
    exit();
}

// 2. ดึงข้อมูลสินค้า
$sql = "SELECT p.*, t.type_name 
        FROM tbl_product as p 
        LEFT JOIN tbl_type as t ON p.type_id = t.type_id
        WHERE p.p_id = :p_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':p_id', $p_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// *** จุดสำคัญ: ถ้าหาไม่เจอ ($row เป็น false) ให้หยุดทำงานและเด้งออก ***
if($row === false){
    echo '<script>alert("ไม่พบสินค้า หรือสินค้านี้ถูกลบไปแล้ว"); window.location="index.php";</script>';
    exit();
}

// 3. อัปเดตยอดวิว (เฉพาะตอนที่เจอสินค้าแล้วเท่านั้น)
$sql_update_view = "UPDATE tbl_product SET p_view = p_view + 1 WHERE p_id = :p_id";
$stmt_view = $conn->prepare($sql_update_view);
$stmt_view->bindParam(':p_id', $p_id, PDO::PARAM_INT);
$stmt_view->execute();

// Path รูปภาพ
$img_src = "admin/m_img/" . $row['p_img']; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['p_name']; ?> | รายละเอียดสินค้า</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sarabun:300,400,500,700&display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .custom-card { background: #fff; border-radius: 10px; box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); padding: 20px; margin-bottom: 20px; border: none; }
        .section-title { font-size: 1.1rem; font-weight: bold; color: #333; margin-bottom: 15px; display: flex; align-items: center; }
        .section-title i { color: #c90d3d; margin-right: 10px; font-size: 1.2rem; }
        .divider { border-bottom: 1px solid #eee; margin-bottom: 20px; }
        .btn-buy-lg { background-color: #c90d3d; color: white; border-radius: 30px; padding: 10px 40px; font-size: 1rem; font-weight: bold; border: none; transition: 0.3s; }
        .btn-buy-lg:hover { background-color: #a00b30; color: white; }
        .product-thumb-top { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; border: 1px solid #ddd; }
        .info-list { list-style: none; padding: 0; margin: 0; font-size: 0.95rem; color: #555; }
        .info-list li { margin-bottom: 15px; }
        .info-label { color: #888; margin-right: 5px; }
    </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  <?php include 'header.php'; ?> 

  <div class="content-wrapper">
    <div class="content-header"><br></div>

    <div class="content">
      <div class="container">
        
        <div class="custom-card">
            <div class="row align-items-center">
                <div class="col-auto">
                    <img src="<?php echo $img_src; ?>" class="product-thumb-top" onerror="this.src='assets/no-image.png'">
                </div>
                <div class="col-md-7">
                    <h3 style="font-weight: 700; margin-bottom: 5px; color: #1f2d3d;">
                        <?php echo $row['p_name']; ?>
                    </h3>
                    <div class="text-muted" style="font-size: 0.9rem;">
                        <i class="fas fa-user-circle mr-1"></i> แอดมิน &nbsp;|&nbsp;
                        <i class="fas fa-map-marker-alt mr-1"></i> กรุงเทพมหานคร
                    </div>
                </div>
            </div>
            
            <hr class="mt-3 mb-3">

            <div class="row align-items-center">
                <div class="col-md-6 text-muted">
                    <i class="far fa-flag mr-1 text-danger"></i> 
                    ลงวันที่ <?php echo date('d/m/Y', strtotime($row['date_save'])); ?>
                </div>
                <div class="col-md-6 text-right">
                    <?php if($row['p_qty'] > 0){ ?>
                        <a href="cart.php?p_id=<?php echo $row['p_id']; ?>&act=add" class="btn-buy-lg">
                            <i class="fas fa-shopping-cart mr-1"></i> สั่งซื้อสินค้า
                        </a>
                    <?php } else { ?>
                        <button class="btn btn-secondary btn-lg rounded-pill" disabled>สินค้าหมด</button>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-md-12">
                <div class="custom-card">
                    <div class="section-title"><i class="fas fa-align-left"></i> รายละเอียด</div>
                    <div class="divider"></div>
                    <div class="p-2" style="color: #444; min-height: 100px;">
                        <?php echo nl2br($row['p_detail']); ?>
                    </div>
                </div>
                
                <div class="custom-card">
                    <div class="section-title"><i class="far fa-images"></i> รูปที่เกี่ยวข้อง</div>
                    <div class="divider"></div>
                    <div class="text-center text-muted p-3">- ไม่มีรูปภาพเพิ่มเติม -</div>
                </div>

                <div class="custom-card">
                    <div class="section-title"><i class="far fa-comment-dots"></i> รีวิวสินค้า</div>
                    <div class="divider"></div>
                    <div class="text-center text-muted p-3">- ยังไม่มีรีวิว -</div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="custom-card">
                    <div class="section-title"><i class="fas fa-sliders-h"></i> รายละเอียดเพิ่มเติม</div>
                    <div class="divider"></div>
                    <ul class="info-list">
                        <li><span class="info-label">ID:</span> <?php echo $row['p_id']; ?></li>
                        <li><span class="info-label">ประเภท:</span> <?php echo $row['type_name']; ?></li>
                        <li><span class="info-label">ราคา:</span> <span class="text-dark font-weight-bold"><?php echo number_format($row['p_price'], 0); ?> บาท</span></li>
                        <li><span class="info-label">จำนวน:</span> <?php echo $row['p_qty']; ?> unit</li>
                        <li><span class="info-label">ยอดเข้าชม:</span> <?php echo $row['p_view']; ?> ครั้ง</li>
                    </ul>
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