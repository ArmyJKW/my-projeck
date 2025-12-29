<?php
session_start();
if (isset($_POST['m_username'])) {
     include '../condb.php';
     
     // เรียกใช้ SweetAlert2
      echo '
      <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
      
    // รับค่า
    $m_username = $_POST['m_username'];
    $m_password = $_POST['m_password']; // อาจจะเพิ่มการ hash password ได้ตรงนี้
    $m_name = $_POST['m_name'];
    $m_tel = $_POST['m_tel'];
    $m_email = $_POST['m_email'];
    $m_address = $_POST['m_address'];
    $m_level = $_POST['m_level'];
    
    // เช็ค Username ซ้ำ
    $check = $conn->prepare("SELECT m_id FROM tbl_member WHERE m_username = :user");
    $check->bindParam(':user', $m_username, PDO::PARAM_STR);
    $check->execute();

    if($check->rowCount() > 0){
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "ข้อมูลซ้ำ",
                  text: "Username นี้มีผู้ใช้งานแล้ว",
                  type: "warning"
              }, function() {
                  window.history.back();
              });
            }, 100);
        </script>';
        exit;
    }

    // อัปโหลดรูปภาพ
    $date1 = date("Ymd_His");
    $numrand = (mt_rand());
    $upload = $_FILES['m_img']['name'];

    if($upload !='') { 
        $path="../m_img/"; // ตรวจสอบ path ให้ถูก (อาจจะเป็น admin/m_img หรือ ../m_img แล้วแต่โครงสร้าง)
        $type = strrchr($_FILES['m_img']['name'],".");
        $newname = $numrand.$date1.$type;
        $path_copy = $path.$newname;
        move_uploaded_file($_FILES['m_img']['tmp_name'],$path_copy);  
    }else{
        $newname = 'default.png'; // รูปเริ่มต้นถ้าไม่ได้อัปโหลด
    }

    $stmt = $conn->prepare("INSERT INTO tbl_member (m_username, m_password, m_name, m_tel, m_email, m_address, m_level, m_img) 
    VALUES (:user, :pass, :name, :tel, :email, :addr, :level, '$newname')");
    
    $stmt->bindParam(':user', $m_username, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $m_password, PDO::PARAM_STR);
    $stmt->bindParam(':name', $m_name, PDO::PARAM_STR);
    $stmt->bindParam(':tel', $m_tel, PDO::PARAM_STR);
    $stmt->bindParam(':email', $m_email, PDO::PARAM_STR);
    $stmt->bindParam(':addr', $m_address, PDO::PARAM_STR);
    $stmt->bindParam(':level', $m_level, PDO::PARAM_STR);
    
    $result = $stmt->execute();
    
    if($result){
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "เพิ่มสมาชิกสำเร็จ",
                  type: "success",
                  timer: 1000,
                  showConfirmButton: false
              }, function() {
                  window.location = "member.php"; 
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
                  window.location = "member.php";
              });
            }, 1000);
        </script>';
    } 
}
?>