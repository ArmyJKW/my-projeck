<?php 
//คิวรี่ข้อมูลมาแสดงในตาราง
    include '../condb.php';
    $stmtType = $conn->prepare("
    SELECT * FROM tbl_type 
    ORDER BY type_id  ASC #เรียงลำดับข้อมูลจากน้อยไปมาก
    ");
      $stmtType->execute();
      $resultType = $stmtType->fetchAll();                                         
?>
  <table id="example1" class="table table-striped dataTable">
    <thead>
      <tr role="row" class="info">
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 1%;">No.</th>
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 50%;">ประเภทสินค้า</th>
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 3%;">-</th>  
        <th  tabindex="0" rowspan="1" colspan="1" style="width: 3%;">-</th> 
      </tr>
    </thead>
    <tbody>
       <?php $runNumber = 1; foreach ($resultType as $row_type) { ?>  
      <tr>
        <td  align="center">
         <?php echo $runNumber++; ?>
        </td>
         <td>
         <?php echo $row_type['type_name']; ?>
        </td>
        <td><a href="type.php?act=edit&type_id=<?php echo $row_type['type_id']; ?>"><i class="fas fa-edit" style="color: #2E86C1;"></i></a></td>
        <td><a href=""  onclick="confirmDelete(event, '<?php echo $row_type['type_id']; ?>')"><i class="fas fa-trash" style="color: #D35400;"></i></a></td>   
         <?php } ?>  
      </tr>
    </tbody>
  </table>
   <script>
       function confirmDelete(event, type_id) {
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
                   window.location.href = "type_del.php?type_id=" + encodeURIComponent(type_id)
              }
          });
      }

    </script>
