<?php 
    include '../condb.php';
    // ดึงข้อมูลออเดอร์ เชื่อมกับตารางสมาชิกเพื่อเอาชื่อคนสั่ง
    $stmt = $conn->prepare("
        SELECT o.*, m.m_name 
        FROM tbl_order as o 
        INNER JOIN tbl_member as m ON o.m_id = m.m_id 
        ORDER BY o.order_id DESC
    ");
    $stmt->execute();
    $result = $stmt->fetchAll();                                         
?>
  
<div class="card">
    <div class="card-header bg-navy">
      <h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> รายการสั่งซื้อสินค้า</h3>
    </div>
    
    <div class="card-body">
      <table id="example1" class="table table-bordered table-striped dataTable">
        <thead>
          <tr class="info">
            <th width="5%" class="text-center">#</th>
            <th width="15%" class="text-center">เลขที่ใบสั่งซื้อ</th>
            <th width="20%">ลูกค้า</th>
            <th width="15%" class="text-center">วันที่สั่งซื้อ</th>
            <th width="15%" class="text-right">ยอดรวม</th>
            <th width="15%" class="text-center">สถานะ</th>
            <th width="10%" class="text-center">จัดการ</th>
          </tr>
        </thead>
        <tbody>
           <?php 
           $i = 1;
           foreach ($result as $row) { 
               // เช็คสถานะเพื่อเปลี่ยนสี Badge
               $st = $row['order_status'];
               if($st == 1){
                   $status_show = '<span class="badge badge-warning">รอชำระเงิน</span>';
               }elseif($st == 2){
                   $status_show = '<span class="badge badge-success">ชำระเงินแล้ว</span>';
               }else{
                   $status_show = '<span class="badge badge-danger">ยกเลิก</span>';
               }
           ?>  
          <tr>
            <td align="center"><?php echo $i++; ?></td>
            <td align="center">
                Ref-<?php echo str_pad($row['order_id'], 6, '0', STR_PAD_LEFT);?>
            </td>
            <td><?php echo $row['m_name']; ?></td>
            <td align="center"><?php echo date('d/m/Y H:i', strtotime($row['order_date'])); ?></td>
            <td align="right"><?php echo number_format($row['total_price'], 2); ?></td>
            <td align="center"><?php echo $status_show; ?></td>
            <td align="center">
              <a href="order.php?act=detail&order_id=<?php echo $row['order_id']; ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-search"></i> เปิดดู
              </a>
            </td>   
          </tr>
          <?php } ?>  
        </tbody>
      </table>
    </div>
</div>