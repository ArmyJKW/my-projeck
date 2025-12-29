<?php 
// เชื่อมต่อฐานข้อมูลและดึงข้อมูลสินค้าพร้อมชื่อประเภท
    include '../condb.php';
    $stmtPrd = $conn->prepare("
        SELECT p.*, t.type_name 
        FROM tbl_product as p 
        INNER JOIN tbl_type as t ON p.type_id = t.type_id
        ORDER BY p.p_id ASC
    ");
    $stmtPrd->execute();
    $resultPrd = $stmtPrd->fetchAll();                                         
?>

<div class="card-body">
    <table id="example1" class="table table-striped dataTable">
    <thead>
        <tr role="row" class="info">
            <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">No.</th>
            <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">รูปภาพ</th>
            <th tabindex="0" rowspan="1" colspan="1" style="width: 30%;">ชื่อสินค้า</th>
            <th tabindex="0" rowspan="1" colspan="1" style="width: 15%;">ประเภท</th>
            <th tabindex="0" rowspan="1" colspan="1" style="width: 10%;">ราคา</th>
            <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">แก้ไข</th>
            <th tabindex="0" rowspan="1" colspan="1" style="width: 5%;">ลบ</th>
        </tr>
    </thead>
    <tbody>
        <?php $runNumber = 1; foreach ($resultPrd as $row_prd) { ?>  
        <tr>
            <td align="center">
                <?php echo $runNumber++; ?>
            </td>
            <td align="center">
                <img src="../assets/img/product/<?php echo $row_prd['p_img']; ?>" width="50px">
            </td>
            <td>
                <?php echo $row_prd['p_name']; ?>
            </td>
            <td>
                <?php echo $row_prd['type_name']; ?>
            </td>
            <td>
                <?php echo number_format($row_prd['p_price'], 2); ?>
            </td>
            <td>
                <a href="product.php?act=edit&p_id=<?php echo $row_prd['p_id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
            </td>
            <td>
                <a href="#" onclick="confirmDelete(event, '<?php echo $row_prd['p_id']; ?>')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
            </td>   
        </tr>
        <?php } ?>  
    </tbody>
    </table>
</div>

<script>
    function confirmDelete(event, p_id) {
        event.preventDefault();
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
                window.location.href = "product_del.php?p_id=" + p_id;
            }
        });
    }
</script>