<?php 
session_start();
include 'condb.php';
include 'head.php'; 

// เช็คการล็อกอิน
if(!isset($_SESSION['m_id'])){
    header("Location: login.php");
    exit();
}

$m_id = $_SESSION['m_id'];
$stmt = $conn->prepare("SELECT * FROM tbl_member WHERE m_id = ?");
$stmt->execute([$m_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<body>
    <?php include 'header.php'; ?>
    <?php include 'banner.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-user-edit"></i> แก้ไขข้อมูลส่วนตัว</h4>
                    </div>
                    <form action="edit_profile_db.php" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="m_img/<?php echo $row['m_img'];?>" class="rounded-circle" width="120" height="120" style="object-fit: cover; border: 3px solid #ddd;">
                                <br>
                                <span class="badge badge-info mt-2">ระดับสมาชิก: <?php echo $row['m_level'];?></span>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Username (เปลี่ยนไม่ได้)</label>
                                    <input type="text" class="form-control" value="<?php echo $row['m_username'];?>" disabled>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>เปลี่ยนรหัสผ่าน (ถ้าไม่เปลี่ยนให้เว้นว่าง)</label>
                                    <input type="password" name="m_password" class="form-control" placeholder="กรอกรหัสผ่านใหม่">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>ชื่อ-นามสกุล</label>
                                <input type="text" name="m_name" class="form-control" value="<?php echo $row['m_name'];?>" required>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>เบอร์โทรศัพท์</label>
                                    <input type="text" name="m_tel" class="form-control" value="<?php echo $row['m_tel'];?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>อีเมล</label>
                                    <input type="email" name="m_email" class="form-control" value="<?php echo $row['m_email'];?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>ที่อยู่จัดส่ง</label>
                                <textarea name="m_address" class="form-control" rows="3"><?php echo $row['m_address'];?></textarea>
                            </div>

                            <div class="form-group">
                                <label>เปลี่ยนรูปโปรไฟล์</label>
                                <input type="file" name="m_img" class="form-control-file" accept="image/*">
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <input type="hidden" name="m_id" value="<?php echo $m_id;?>">
                            <input type="hidden" name="m_img2" value="<?php echo $row['m_img'];?>">
                            <a href="index.php" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>