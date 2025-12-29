<?php 
    include '../condb.php';
    // ดึงข้อมูลสินค้าเรียงตามจำนวนน้อยไปมาก
    $stmtPrd = $conn->prepare("
        SELECT p.*, t.type_name 
        FROM tbl_product as p 
        INNER JOIN tbl_type as t ON p.type_id = t.type_id
        ORDER BY p.p_qty ASC
    ");
    $stmtPrd->execute();
    $resultPrd = $stmtPrd->fetchAll();                                         
?>
  
<div class="card">
    <div class="card-header bg-navy">
      <h3 class="card-title"><i class="fas fa-boxes"></i> รายการสต็อกสินค้า</h3>
    </div>
    
    <div class="card-body">
      <table id="example1" class="table table-bordered table-striped dataTable">
        <thead>
          <tr class="info">
            <th width="5%" class="text-center">No.</th>
            <th width="10%" class="text-center">รูปภาพ</th>
            <th width="35%">ชื่อสินค้า</th>
            <th width="15%" class="text-center">จำนวนคงเหลือ</th>
            <th width="15%" class="text-center">จัดการสต็อก</th>
          </tr>
        </thead>
        <tbody>
           <?php $runNumber = 1; foreach ($resultPrd as $row_prd) { 
               // เช็คจำนวนสินค้าเพื่อเปลี่ยนสี
               $qty = $row_prd['p_qty'];
               if($qty <= 5){
                   $qty_badge = '<span class="badge badge-danger" style="font-size:100%">'.$qty.' (ใกล้หมด)</span>';
               }elseif($qty <= 20){
                   $qty_badge = '<span class="badge badge-warning" style="font-size:100%">'.$qty.'</span>';
               }else{
                   $qty_badge = '<span class="badge badge-success" style="font-size:100%">'.$qty.'</span>';
               }
           ?>  
          <tr>
            <td align="center"><?php echo $runNumber++; ?></td>
            <td align="center">
              <img src="../assets/img/product/<?php echo $row_prd['p_img']; ?>" width="50px" style="border-radius:5px">
            </td>
             <td><?php echo $row_prd['p_name']; ?></td>
            <td align="center"><?php echo $qty_badge; ?></td>
            <td align="center">
              <button type="button" class="btn btn-primary btn-sm btn-flat" data-toggle="modal" data-target="#modal_stock<?php echo $row_prd['p_id']; ?>">
                <i class="fas fa-sync-alt"></i> ปรับสต็อก
              </button>


                <div class="modal fade" id="modal_stock<?php echo $row_prd['p_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-navy">
                        <h5 class="modal-title">จัดการสต็อก: <?php echo $row_prd['p_name']; ?></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <form action="stock_update_db.php" method="post">
                        <div class="modal-body">
                        <div class="form-group text-center">
                            <label>จำนวนคงเหลือปัจจุบัน</label>
                            <h1 class="text-success"><?php echo $row_prd['p_qty']; ?></h1>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>เลือกการทำรายการ</label>
                            <div class="row text-center">
                                <div class="col-6">
                                    <input type="radio" name="status" value="add" checked required> 
                                    <span class="text-success"><i class="fas fa-plus-circle"></i> เพิ่มสินค้า (รับเข้า)</span>
                                </div>
                                <div class="col-6">
                                    <input type="radio" name="status" value="del" required> 
                                    <span class="text-danger"><i class="fas fa-minus-circle"></i> ลดสินค้า (ขาย/เสีย)</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>จำนวนที่ต้องการปรับปรุง</label>
                            <input type="number" name="amount" class="form-control" min="1" required placeholder="ระบุจำนวน">
                        </div>
                        <input type="hidden" name="p_id" value="<?php echo $row_prd['p_id']; ?>">
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                        </form>
                    </div>
                    </div>
                </div>
            </td>   
          </tr>
        <?php } ?>  
        </tbody>
      </table>
    </div>
</div>