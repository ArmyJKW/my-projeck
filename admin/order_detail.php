<?php 
    include '../condb.php';
    $order_id = $_GET['order_id'];

    // 1. ดึงหัวบิล
    $stmtHead = $conn->prepare("
        SELECT o.*, m.m_name, m.m_tel, m.m_address 
        FROM tbl_order as o 
        INNER JOIN tbl_member as m ON o.m_id = m.m_id 
        WHERE o.order_id = ?");
    $stmtHead->execute([$order_id]);
    $head = $stmtHead->fetch();

    // 2. ดึงรายการสินค้าในบิล
    $stmtDetail = $conn->prepare("
        SELECT d.*, p.p_img 
        FROM tbl_order_detail as d 
        INNER JOIN tbl_product as p ON d.p_id = p.p_id 
        WHERE d.order_id = ?");
    $stmtDetail->execute([$order_id]);
    $details = $stmtDetail->fetchAll();
?>

<div class="card">
    <div class="card-header bg-secondary">
      <h3 class="card-title">รายละเอียดใบสั่งซื้อเลขที่: Ref-<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT);?></h3>
      <div class="card-tools">
          <a href="order.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left"></i> ย้อนกลับ</a>
      </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5><i class="fas fa-user"></i> ข้อมูลลูกค้า</h5>
                <p>
                    <b>ชื่อ:</b> <?php echo $head['m_name']; ?><br>
                    <b>เบอร์โทร:</b> <?php echo $head['m_tel']; ?><br>
                    <b>ที่อยู่จัดส่ง:</b> <?php echo $head['m_address']; ?>
                </p>
            </div>
            <div class="col-md-6 text-right">
                <h5><i class="fas fa-money-bill"></i> ข้อมูลการชำระเงิน</h5>
                <p>
                    <b>วันที่สั่งซื้อ:</b> <?php echo date('d/m/Y H:i', strtotime($head['order_date'])); ?><br>
                    <b>สถานะปัจจุบัน:</b> 
                    <?php 
                        if($head['order_status']==1) echo '<span class="badge badge-warning">รอชำระเงิน</span>';
                        elseif($head['order_status']==2) echo '<span class="badge badge-success">ชำระเงินแล้ว</span>';
                        else echo '<span class="badge badge-danger">ยกเลิก</span>';
                    ?>
                </p>
                <?php if($head['pay_slip'] != '') { ?>
                    <a href="../assets/img/slip/<?php echo $head['pay_slip'];?>" target="_blank" class="btn btn-info btn-sm">
                        <i class="fas fa-file-image"></i> ดูหลักฐานการโอน
                    </a>
                <?php } ?>
            </div>
        </div>
        <hr>

        <h5>รายการสินค้า</h5>
        <table class="table table-bordered table-hover">
            <thead class="bg-light">
                <tr>
                    <th width="5%">#</th>
                    <th width="10%">รูปภาพ</th>
                    <th>สินค้า</th>
                    <th width="10%" class="text-right">ราคา</th>
                    <th width="10%" class="text-center">จำนวน</th>
                    <th width="15%" class="text-right">รวม (บาท)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i=1; 
                $total=0;
                foreach($details as $row){ 
                    $total += $row['total'];
                ?>
                <tr>
                    <td><?php echo $i++;?></td>
                    <td><img src="../assets/img/product/<?php echo $row['p_img'];?>" width="50"></td>
                    <td><?php echo $row['p_name'];?></td>
                    <td align="right"><?php echo number_format($row['p_price'],2);?></td>
                    <td align="center"><?php echo $row['p_qty'];?></td>
                    <td align="right"><?php echo number_format($row['total'],2);?></td>
                </tr>
                <?php } ?>
                <tr class="bg-light">
                    <td colspan="5" align="right"><b>ยอดรวมสุทธิ</b></td>
                    <td align="right"><b><?php echo number_format($total,2);?></b></td>
                </tr>
            </tbody>
        </table>

        <div class="mt-4">
            <form action="order_status_db.php" method="post">
                <input type="hidden" name="order_id" value="<?php echo $order_id;?>">
                <label>ปรับสถานะออเดอร์:</label>
                <div class="input-group col-md-4 pl-0">
                    <select name="order_status" class="form-control">
                        <option value="1" <?php if($head['order_status']==1) echo 'selected';?>>รอชำระเงิน</option>
                        <option value="2" <?php if($head['order_status']==2) echo 'selected';?>>ชำระเงินแล้ว (อนุมัติ)</option>
                        <option value="3" <?php if($head['order_status']==3) echo 'selected';?>>ยกเลิก</option>
                    </select>
                    <span class="input-group-append">
                        <button type="submit" class="btn btn-primary">บันทึกสถานะ</button>
                    </span>
                </div>
            </form>
        </div>

    </div>
</div>