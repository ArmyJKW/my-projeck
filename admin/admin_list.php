<?php 
//คิวรี่ข้อมูลมาแสดงในตาราง
    include '../condb.php';
    $stmtMem = $conn->prepare("
    SELECT * FROM tbl_member 
    WHERE m_level = 'admin'
    ORDER BY m_id ASC #เรียงลำดับข้อมูลจากน้อยไปมาก
    ");
      $stmtMem->execute();
      $resultMem = $stmtMem->fetchAll();                                         
?>
  <table id="example1" class="table table-striped dataTable">
    <thead>
      <tr role="row" class="info">
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 1%;">No.</th>
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 5%;">รูป</th>
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 10%;">username</th>
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 10%;">password</th>
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ชื่อ</th>
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 20%;">อีเมล์</th>
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 5%;">เปิด</th>
        <th  style="width: 10%; text-align: center;">สถานะ</th>
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 3%;">-</th>  
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 3%;">-</th> 
      </tr>
    </thead>
    <tbody>
       <?php $runNumber = 1; foreach ($resultMem as $row_member) { ?>  
      <tr>
        <td  align="center">
         <?php echo $runNumber++; ?>
        </td>
        <td  align="center">
        <img src="m_img/<?php echo $row_member['m_img']; ?>" id="uploaded_image" class="img-responsive img-circle " width="40">
        </td>
         <td>
         <?php echo $row_member['m_username']; ?>
        </td>
         <td>
         <?php echo $row_member['m_password']; ?>
        </td>
         <td>
         <?php echo $row_member['m_name']; ?>
        </td>
         <td>
         <?php echo $row_member['m_email']; ?>
        </td>
        <td>
        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
            <input 
                type="checkbox"
                class="custom-control-input"
                id="customSwitch<?= $row_member['m_id']?>"
                onchange="toggle_check(<?= $row_member['m_id']?>)"
                <?php echo($row_member['m_status'] == 1)?'checked':''; ?> 
            >
            <label class="custom-control-label" for="customSwitch<?= $row_member['m_id']?>"></label>
        </div>
        </td>
        <td align="center">
           <small class="badge badge-warning"><i class="far fa-check-circle"></i> ผู้ดูแลระบบ</small>
        </td>
        <td><a href="admin.php?act=edit&m_id=<?php echo $row_member['m_id']; ?>"><i class="fas fa-edit" style="color: #2E86C1;"></i></a></td>
        <td><a href="#" onclick="confirmDelete(event, '<?php echo $row_member['m_id']; ?>')"><i class="fas fa-trash" style="color: #D35400;"></i></a></td>
         <?php } ?>  
      </tr>
    </tbody>
  </table>
    <script>
       function toggle_check(m_id) {
      // alert(m_id) //เช็คค่า user_id
        $.ajax({
        method: 'POST',
        url: 'admin_update_status.php',
        data: {
        m_id: m_id
        },
        });
      }

      function confirmDelete(event, m_id) {
          event.preventDefault(); // หยุดการเปลี่ยนเส้นทางไปยังหน้าลบก่อน
          Swal.fire({
              text: "คุณแน่ใจที่จะลบข้อมูลหรือไม่?",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "ตกลง",
              cancelButtonText: "ยกเลิก"
          }).then((result) => {
              if (result.isConfirmed) {
                  // Redirect to logout.php if user confirms
                   window.location.href = "admin_del.php?m_id=" + encodeURIComponent(m_id)
              }
          });
      }
    </script>
