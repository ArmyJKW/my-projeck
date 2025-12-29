<?php
    if(isset($_GET['m_id'])){
      include '../condb.php';
      $stmt = $conn->prepare("SELECT * FROM tbl_member WHERE m_id=?");
      $stmt->execute([$_GET['m_id']]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>
<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title">แก้ไขข้อมูลสมาชิก</h3>
    </div>
    <form action="member_edit_db.php" method="post" enctype="multipart/form-data">
        <div class="card-body">
             <div class="row">
                <div class="form-group col-md-6">
                    <label>Username</label>
                    <input type="text" name="m_username" class="form-control" value="<?php echo $row['m_username'];?>" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label>Password (ใส่เฉพาะเมื่อต้องการเปลี่ยน)</label>
                    <input type="password" name="m_password" class="form-control" placeholder="เว้นว่างไว้ถ้าไม่เปลี่ยน">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>ชื่อ-นามสกุล</label>
                    <input type="text" name="m_name" class="form-control" value="<?php echo $row['m_name'];?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label>เบอร์โทรศัพท์</label>
                    <input type="text" name="m_tel" class="form-control" value="<?php echo $row['m_tel'];?>" required>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>อีเมล</label>
                    <input type="email" name="m_email" class="form-control" value="<?php echo $row['m_email'];?>">
                </div>
                <div class="form-group col-md-6">
                    <label>รูปโปรไฟล์ (อัปโหลดใหม่เพื่อเปลี่ยน)</label>
                    <input type="file" name="m_img" class="form-control" accept="image/*">
                    <img src="../m_img/<?php echo $row['m_img'];?>" width="100px" class="mt-2">
                </div>
            </div>
             <div class="form-group">
                <label>ที่อยู่</label>
                <textarea name="m_address" class="form-control" rows="3"><?php echo $row['m_address'];?></textarea>
            </div>
            
            <div class="form-group">
                <input type="hidden" name="m_id" value="<?php echo $row['m_id'];?>">
                <input type="hidden" name="m_img2" value="<?php echo $row['m_img'];?>">
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> บันทึกการแก้ไข</button>
                <a href="member.php" class="btn btn-danger">ยกเลิก</a>
            </div>
        </div>
    </form>
</div>