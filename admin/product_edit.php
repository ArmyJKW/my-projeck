<?php
    if(isset($_GET['p_id'])){
      include '../condb.php';
      
      // ดึงข้อมูลประเภทสินค้า
      $stmtType = $conn->prepare("SELECT * FROM tbl_type ORDER BY type_id ASC");
      $stmtType->execute();
      $resultType = $stmtType->fetchAll();

      // ดึงข้อมูลสินค้าที่จะแก้ไข
      $stmtPrd = $conn->prepare("SELECT * FROM tbl_product WHERE p_id=?");
      $stmtPrd->execute([$_GET['p_id']]);
      $row_prd = $stmtPrd->fetch(PDO::FETCH_ASSOC);

      if($stmtPrd->rowCount() < 1){
          header('Location: product.php');
          exit();
      }
    }
?>
<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title">แก้ไขข้อมูลสินค้า</h3>
    </div>
    <form action="product_edit_db.php" method="post" enctype="multipart/form-data">
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-6">
                    <label>ชื่อสินค้า</label>
                    <input type="text" name="p_name" value="<?= $row_prd['p_name'];?>" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label>ประเภทสินค้า</label>
                    <select name="type_id" class="form-control" required>
                        <?php foreach($resultType as $row_type){ ?>
                        <option value="<?php echo $row_type['type_id'];?>" <?php if($row_prd['type_id']==$row_type['type_id']){ echo "selected";} ?>>
                            <?php echo $row_type['type_name'];?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                 <div class="form-group col-md-6">
                    <label>ราคา (บาท)</label>
                    <input type="number" name="p_price" value="<?= $row_prd['p_price'];?>" class="form-control" required>
                </div>
                 <div class="form-group col-md-6">
                    <label>รูปภาพสินค้า</label>
                    <input type="file" name="p_img" class="form-control" accept="image/*">
                    <br>
                    <img src="../assets/img/product/<?= $row_prd['p_img'];?>" width="100px">
                </div>
            </div>
            <div class="row">
                 <div class="form-group col-md-12">
                    <label>รายละเอียดสินค้า</label>
                    <textarea name="p_detail" class="form-control" rows="3"><?= $row_prd['p_detail'];?></textarea>
                </div>
            </div>
           
            <div class="form-group">
                <input type="hidden" name="p_id" value="<?= $row_prd['p_id'];?>">
                <input type="hidden" name="p_img2" value="<?= $row_prd['p_img'];?>">
                <button type="submit" class="btn btn-warning">บันทึกการแก้ไข</button>
                <a href="product.php" class="btn btn-dark">กลับ</a>
            </div>
        </div>
    </form>
</div>