<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">เพิ่มข้อมูลสมาชิก</h3>
    </div>
    <form action="member_add_db.php" method="post" enctype="multipart/form-data">
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Username (สำหรับเข้าสู่ระบบ)</label>
                    <input type="text" name="m_username" class="form-control" placeholder="ตั้งชื่อผู้ใช้งาน" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Password</label>
                    <input type="password" name="m_password" class="form-control" placeholder="ตั้งรหัสผ่าน" required>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>ชื่อ-นามสกุล</label>
                    <input type="text" name="m_name" class="form-control" placeholder="ชื่อจริง นามสกุลจริง" required>
                </div>
                <div class="form-group col-md-6">
                    <label>เบอร์โทรศัพท์</label>
                    <input type="text" name="m_tel" class="form-control" placeholder="เบอร์โทรศัพท์" required>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>อีเมล</label>
                    <input type="email" name="m_email" class="form-control" placeholder="example@email.com">
                </div>
                <div class="form-group col-md-6">
                    <label>รูปโปรไฟล์</label>
                    <input type="file" name="m_img" class="form-control" accept="image/*" required>
                </div>
            </div>
             <div class="form-group">
                <label>ที่อยู่</label>
                <textarea name="m_address" class="form-control" rows="3" placeholder="ที่อยู่ปัจจุบัน"></textarea>
            </div>
            
            <div class="form-group">
                <input type="hidden" name="m_level" value="member">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึกข้อมูล</button>
                <a href="member.php" class="btn btn-danger">ยกเลิก</a>
            </div>
        </div>
    </form>
</div>