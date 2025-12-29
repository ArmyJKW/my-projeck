<?php 
if(isset($_GET['p_id'])){
    include '../condb.php';
    $p_id = $_GET['p_id'];
    
    // (Optional) ลบไฟล์รูปภาพออกจากโฟลเดอร์ด้วยก็ได้ ถ้าต้องการ
    
    $stmt = $conn->prepare('DELETE FROM tbl_product WHERE p_id=:p_id');
    $stmt->bindParam(':p_id', $p_id , PDO::PARAM_INT);
    $stmt->execute();

    if($stmt->rowCount() > 0){
        echo '<script>       
              window.location = "product.php"; 
              </script>';
    }else{
       echo '<script>         
              window.location = "product.php"; 
             </script>';
    }
    $conn = null;
} 
?>