<?php
session_start();
include 'condb.php';

// 1. ส่วนจัดการตะกร้าสินค้า (Logic)
$act = isset($_GET['act']) ? $_GET['act'] : 'view';
$p_id = isset($_GET['p_id']) ? $_GET['p_id'] : '';

// กรณีเพิ่มสินค้าลงตะกร้า (Add to Cart)
if ($act == 'add' && !empty($p_id)) {
    if (isset($_SESSION['cart'][$p_id])) {
        $_SESSION['cart'][$p_id]++;
    } else {
        $_SESSION['cart'][$p_id] = 1;
    }
}

// กรณีลบสินค้า (Remove)
if ($act == 'remove' && !empty($p_id)) {
    unset($_SESSION['cart'][$p_id]);
}

// กรณีอัปเดตจำนวนสินค้า (Update)
if ($act == 'update') {
    $amount_array = $_POST['amount'];
    foreach ($amount_array as $p_id => $amount) {
        $_SESSION['cart'][$p_id] = $amount;
    }
}

// กรณีเคลียร์ตะกร้า (Cancel)
if ($act == 'cancel') {
    unset($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sarabun:300,400,500,700&display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #fcfcfc;
        }
        .cart-table {
            width: 100%;
            border-collapse: separate; 
            border-spacing: 0 15px; /* เว้นระยะห่างระหว่างแถว */
        }
        .cart-table thead th {
            border: none;
            color: #555;
            font-weight: 600;
        }
        .cart-table tbody tr {
            background-color: #fff;
            /* box-shadow: 0 2px 5px rgba(0,0,0,0.02); */
        }
        .cart-table td {
            vertical-align: middle;
            border-top: none;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px;
        }
        
        /* ปุ่มลบสีแดง */
        .btn-delete {
            background-color: #ef4747;
            color: white;
            border-radius: 20px;
            padding: 5px 20px;
            border: none;
            font-size: 0.9rem;
            transition: 0.2s;
        }
        .btn-delete:hover {
            background-color: #d63030;
            color: white;
        }

        /* ปุ่มคำนวณราคาสีเหลือง */
        .btn-calc {
            background-color: #fbc02d;
            color: #333;
            border-radius: 5px;
            padding: 10px 30px;
            border: none;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn-calc:hover {
            background-color: #f9a825;
            color: #333;
        }

        /* ชุดปุ่มเพิ่มลดจำนวน */
        .qty-box {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qty-btn {
            border: 1px solid #e0e0e0;
            background: #fff;
            color: #ef4747;
            width: 30px;
            height: 35px;
            cursor: pointer;
            border-radius: 4px;
        }
        .qty-btn:hover {
            background: #f9f9f9;
        }
        .qty-input {
            width: 50px;
            height: 35px;
            text-align: center;
            border: 1px solid #e0e0e0;
            margin: 0 5px;
            border-radius: 4px;
        }

        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  <?php include 'header.php'; ?> 

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container">
          <br>
      </div>
    </div>

    <div class="content">
      <div class="container">
        
        <form id="frmcart" name="frmcart" method="post" action="?act=update">
          <table class="table cart-table">
            <thead>
              <tr>
                <th width="15%">สินค้า</th>
                <th width="30%">ชื่อสินค้า</th>
                <th width="15%" class="text-right">ราคา</th>
                <th width="15%" class="text-center">จำนวน</th>
                <th width="15%" class="text-right">รวม</th>
                <th width="10%" class="text-center">ลบ</th>
              </tr>
            </thead>
            <tbody>
            <?php
            $total = 0;
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $p_id => $qty) {
                    // ดึงข้อมูลสินค้าจาก DB
                    $stmt = $conn->prepare("SELECT * FROM tbl_product WHERE p_id = :p_id");
                    $stmt->bindParam(':p_id', $p_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    // คำนวณราคารวมต่อชิ้น
                    $sum = $row['p_price'] * $qty;
                    $total += $sum;
                    
                    // Path รูปภาพ (เช็คโฟลเดอร์ให้ตรงกับที่มีจริง)
                    $img_src = "assets/img/product/" . $row['p_img'];
            ?>
              <tr>
                <td>
                    <img src="<?php echo $img_src; ?>" width="80px" style="border-radius: 5px;">
                </td>
                <td>
                    <h5 style="font-size: 1rem; color: #555; margin: 0;"><?php echo $row['p_name']; ?></h5>
                </td>
                <td align="right" style="color: #777;"><?php echo number_format($row['p_price'], 0); ?></td>
                <td align="center">
                  <div class="qty-box">
                      <button type="button" class="qty-btn" onclick="decrementValue('qty_<?php echo $p_id; ?>')">-</button>
                      <input type="number" name="amount[<?php echo $p_id; ?>]" id="qty_<?php echo $p_id; ?>" value="<?php echo $qty; ?>" class="qty-input" min="1">
                      <button type="button" class="qty-btn" onclick="incrementValue('qty_<?php echo $p_id; ?>')">+</button>
                  </div>
                </td>
                <td align="right" style="color: #555;"><?php echo number_format($sum, 0); ?></td>
                <td align="center">
                  <a href="cart.php?p_id=<?php echo $p_id; ?>&act=remove" class="btn-delete" onclick="return confirm('ยืนยันการลบ?');">
                    ลบ
                  </a>
                </td>
              </tr>
            <?php } // ปิด foreach ?>
              
              <tr class="total-row">
                  <td colspan="4" align="right" style="padding-right: 50px; color: #777;">ราคารวมทั้งหมด</td>
                  <td align="right" style="font-size: 1.2rem; color: #333;"><?php echo number_format($total, 2); ?></td>
                  <td style="color: #999;">บาท</td>
              </tr>

            <?php } else { // กรณีไม่มีสินค้า ?>
              <tr>
                  <td colspan="6" align="center">
                      <br><br>
                      <h4 class="text-danger">ไม่มีรายการสั่งซื้อ</h4>
                      <br>
                      <a href="index.php" class="btn btn-secondary">เลือกซื้อสินค้า</a>
                      <br><br>
                  </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>

          <?php if (!empty($_SESSION['cart'])) { ?>
          <div class="row mt-4 mb-5">
              <div class="col-12 text-right">
                  <button type="submit" class="btn-calc">คำนวณราคา</button>
                  
                  <a href="confirm_order.php" class="btn btn-success ml-2" style="padding: 10px 30px; border-radius: 5px; font-weight: bold;">
                      สั่งซื้อสินค้า
                  </a>
              </div>
          </div>
          <?php } ?>

        </form>

      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
</div>

<script>
    function incrementValue(id) {
        var value = parseInt(document.getElementById(id).value, 10);
        value = isNaN(value) ? 0 : value;
        value++;
        document.getElementById(id).value = value;
    }

    function decrementValue(id) {
        var value = parseInt(document.getElementById(id).value, 10);
        value = isNaN(value) ? 0 : value;
        if(value > 1){ // ห้ามต่ำกว่า 1
            value--;
            document.getElementById(id).value = value;
        }
    }
</script>

</body>
</html>