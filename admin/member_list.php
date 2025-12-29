<?php 
//คิวรี่ข้อมูลสมาชิก (ตัด admin ออก ถ้าต้องการดูเฉพาะลูกค้า)
    include '../condb.php';
    $stmtMem = $conn->prepare("
    SELECT * FROM tbl_member 
    WHERE m_level != 'admin' 
    ORDER BY m_id DESC
    ");
    $stmtMem->execute();
    $resultMem = $stmtMem->fetchAll();                                         
?>
  
  <div class="card">
      <div class="card-header bg-navy">
        <h3 class="card-title"><i class="fas fa-users"></i> รายการสมาชิก</h3>
        <div class="card-tools">
          <a href="member.php?act=add" class="btn btn-primary btn-sm"><i class="fas fa-user-plus"></i> เพิ่มสมาชิก</a>
        </div>
      </div>
      <div class="card-body">
        <table id="example1" class="table table-bordered table-striped dataTable">
          <thead>
            <tr role="row" class="info">
              <th width="5%" class="text-center">No.</th>
              <th width="15%" class="text-center">รหัสสมาชิก</th> <th width="10%" class="text-center">รูปภาพ</th>
              <th width="15%">Username</th>
              <th width="20%">ชื่อ-นามสกุล</th>
              <th width="15%">เบอร์โทร</th>
              <th width="10%" class="text-center">จัดการ</th>
            </tr>
          </thead>
          <tbody>
             <?php $i = 1; foreach ($resultMem as $row) { ?>  
            <tr>
              <td align="center"><?php echo $i++; ?></td>
              <td align="center">
                <span class="badge badge-info" style="font-size: 14px;">
                    MEM-<?php echo str_pad($row['m_id'], 4, '0', STR_PAD_LEFT); ?>
                </span>
              </td>
              <td align="center">
                <img src="../m_img/<?php echo $row['m_img']; ?>" width="50px" style="border-radius: 50%; object-fit: cover; height: 50px;">
              </td>
              <td><?php echo $row['m_username']; ?></td>
              <td><?php echo $row['m_name']; ?></td>
              <td><?php echo $row['m_tel']; ?></td>
              <td align="center">
                <a href="member.php?act=edit&m_id=<?php echo $row['m_id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                <a href="#" onclick="confirmDelete(event, '<?php echo $row['m_id']; ?>')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
              </td>   
            </tr>
            <?php } ?>  
          </tbody>
        </table>
      </div>
  </div>

   <script>
       function confirmDelete(event, m_id) {
          event.preventDefault(); 
          swal({
              title: "คุณแน่ใจหรือไม่?",
              text: "คุณต้องการลบข้อมูลสมาชิกนี้ใช่หรือไม่!",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "ตกลง, ลบเลย!",
              cancelButtonText: "ยกเลิก",
              closeOnConfirm: false
          }, function(){
              window.location.href = "member_del.php?m_id=" + m_id;
          });
      }
   </script>