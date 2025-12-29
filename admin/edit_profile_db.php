<?php
session_start();
if (isset($_POST['m_name'])) {
    include 'condb.php';
    
    // เรียกใช้ SweetAlert (ถ้ามีใน project)
    echo '
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';

    $m_id = $_POST['m_id'];
    $m_name = $_POST['m_name'];
    $m_tel = $_POST['m_tel'];
    $m_email = $_POST['m_email'];
    $m_address = $_POST['m_address'];
    $m_img2 = $_POST['m_img2'];
    
    // เช็คเปลี่ยนรหัสผ่าน
    $m_password = $_POST['m_password'];
    $pass_sql = "";
    if($m_password != ""){
        $pass_sql = ", m_password = '$m_password'";
    }

    // อัปโหลดรูปภาพ
    $date1 = date("Ymd_His");
    $numrand = (mt_rand());
    $upload = $_FILES['m_img']['name'];

    if($upload !='') { 
        $path="m_img/"; // โฟลเดอร์เก็บรูป (Front-end)
        $type = strrchr($_FILES['m_img']['name'],".");
        $newname = $numrand.$date1.$type;
        $path_copy = $path.$newname;
        move_uploaded_file($_FILES['m_img']['tmp_name'],$path_copy);  
    }else{
        $newname = $m_img2;
    }

    $sql = "UPDATE tbl_member SET 
            m_name = :m_name,
            m_tel = :m_tel,
            m_email = :m_email,
            m_address = :m_address,
            m_img = :m_img
            $pass_sql
            WHERE m_id = :m_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':m_name', $m_name, PDO::PARAM_STR);
    $stmt->bindParam(':m_tel', $m_tel, PDO::PARAM_STR);
    $stmt->bindParam(':m_email', $m_email, PDO::PARAM_STR);
    $stmt->bindParam(':m_address', $m_address, PDO::PARAM_STR);
    $stmt->bindParam(':m_img', $newname, PDO::PARAM_STR);
    $stmt->bindParam(':m_id', $m_id, PDO::PARAM_INT);
    $result = $stmt->execute();

    if($result){
        // อัปเดต Session รูปภาพใหม่ทันที
        $_SESSION['m_img'] = $newname; 
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "แก้ไขข้อมูลสำเร็จ",
                  type: "success",
                  timer: 1000,
                  showConfirmButton: false
              }, function() {
                  window.location = "edit_profile.php"; 
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
                  window.history.back();
              });
            }, 1000);
        </script>';
    }
}
?>