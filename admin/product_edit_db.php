<?php
if (isset($_POST['p_name'])) {
     include '../condb.php';
     
      echo '
      <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
      
    // รับค่าจากฟอร์ม
    $p_name = $_POST['p_name'];
    $type_id = $_POST['type_id'];
    $p_price = $_POST['p_price'];
    $p_detail = $_POST['p_detail'];
    
    // อัปโหลดรูปภาพ
    $date1 = date("Ymd_His");
    $numrand = (mt_rand());
    $p_img = (isset($_POST['p_img']) ? $_POST['p_img'] : '');
    $upload = $_FILES['p_img']['name'];

    if($upload !='') { 
        // โฟลเดอร์เก็บรูป ต้องสร้าง folder "assets/img/product/" หรือ "admin/p_img/" ตามโครงสร้างคุณ
        // จากโค้ด list ใช้ ../assets/img/product/
        $path="../assets/img/product/"; 
        $type = strrchr($_FILES['p_img']['name'],".");
        $newname = $numrand.$date1.$type;
        $path_copy = $path.$newname;
        move_uploaded_file($_FILES['p_img']['tmp_name'],$path_copy);  
    } else {
        $newname = '';
    }
  
    // SQL Insert
    $stmt = $conn->prepare("INSERT INTO tbl_product (p_name, type_id, p_price, p_detail, p_img)
    VALUES (:p_name, :type_id, :p_price, :p_detail, '$newname')");
    
    $stmt->bindParam(':p_name', $p_name, PDO::PARAM_STR);
    $stmt->bindParam(':type_id', $type_id, PDO::PARAM_INT);
    $stmt->bindParam(':p_price', $p_price, PDO::PARAM_STR); // ใช้ STR หรือ INT ก็ได้สำหรับ float
    $stmt->bindParam(':p_detail', $p_detail, PDO::PARAM_STR);

    $result = $stmt->execute();

    // ตรวจสอบผลลัพธ์
    if($result){
        echo '<script>
                setTimeout(function() {
                swal({
                    title: "บันทึกข้อมูลสำเร็จ",
                    text: "Redirecting in 1 seconds.",
                    type: "success",
                    timer: 1000,
                    showConfirmButton: false
                }, function() {
                    window.location = "product.php"; 
                });
            }, 1000);
        </script>';
    }else{
        echo '<script>
                setTimeout(function() {
                swal({
                    title: "เกิดข้อผิดพลาด",
                    type: "error"
                }, function() {
                    window.location = "product.php";
                });
            }, 1000);
        </script>';
    }      
}
$conn = null; 
?>