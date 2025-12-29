<?php
    include '../condb.php';
    // ดึงข้อมูลประเภทสินค้ามาแสดงใน Select Option
    $stmtType = $conn->prepare("SELECT * FROM tbl_type ORDER BY type_id ASC");
    $stmtType->execute();
    $resultType = $stmtType->fetchAll();
?>
<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">เพิ่มข้อมูลสินค้า</h3>
    </div>
    <form action="product_add_db.php" method="post" enctype="multipart/form-data">
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-6">
                    <label>ชื่อสินค้า</label>
                    <input type="text" name="p_name" class="form-control" placeholder="ชื่อสินค้า" required>
                </div>
                <div class="form-group col-md-6">
                    <label>ประเภทสินค้า</label>
                    <select name="type_id" class="form-control" required>
                        <option value="">-- เลือกประเภท --</option>
                        <?php foreach($resultType as $row_type){ ?>
                        <option value="<?php echo $row_type['type_id'];?>"><?php echo $row_type['type_name'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>ราคา (บาท)</label>
                    <input type="number" name="p_price" class="form-control" placeholder="0.00" required>
                </div>
                <div class="form-group col-md-6">
                    <label>จำนวนสินค้า (Stock)</label>
                    <input type="number" name="p_qty" class="form-control" placeholder="ระบุจำนวน" required>
                </div>
                <div class="form-group col-md-6">
                    <label>รูปภาพสินค้า</label>
                    <input type="file" name="p_img" class="form-control" accept="image/*" required>
                </div>
            </div>
            <div class="row">
                 <div class="form-group col-md-12">
                    <label>รายละเอียดสินค้า</label>
                    <textarea name="p_detail" class="form-control" rows="3"></textarea>
                </div>
            </div>
           
            <div class="form-group">
                <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                <a href="product.php" class="btn btn-dark">กลับ</a>
            </div>
        </div>
    </form>
</div>