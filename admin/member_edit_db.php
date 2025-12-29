<?php 
session_start();
if (isset($_POST['m_id'])) {
    include '../condb.php';
    
    // เรียกใช้ SweetAlert
    echo '
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';

    // รับค่าจากฟอร์ม
    $m_id = $_POST['m_id'];
    $m_username = $_POST['m_username'];
    $m_password = $_POST['m_password'];
    $m_name = $_POST['m_name'];
    $m_email = $_POST['m_email'];
    $m_tel = $_POST['m_tel'];
    $m_address = $_POST['m_address'];
    $m_img2 = $_POST['m_img2']; // ชื่อรูปเดิม
    
    // รับค่า m_level (ถ้าไม่มีให้ใส่ค่า default เป็น member)
    $m_level = isset($_POST['m_level']) ? $_POST['m_level'] : 'member';

    // ส่วนจัดการรูปภาพ
    $upload = $_FILES['m_img']['name'];
    if ($upload != '') {
        // ถ้ามีการอัปโหลดรูปใหม่
        $date1 = date("Ymd_His");
        $numrand = (mt_rand());
        $typefile = strrchr($_FILES['m_img']['name'], ".");
        
        // เช็คสกุลไฟล์ (กันไฟล์แปลกปลอม)
        if($typefile =='.jpg' || $typefile =='.jpeg' || $typefile =='.png'){
            $path = "m_img/"; // โฟลเดอร์เก็บรูป
            $newname = $numrand.$date1.$typefile;
            $path_copy = $path.$newname;
            move_uploaded_file($_FILES['m_img']['tmp_name'], $path_copy);
        } else {
             // ถ้าไฟล์ไม่ใชารูปภาพ ให้ใช้รูปเดิม
             $newname = $m_img2;
        }
    } else {
        // ถ้าไม่อัปโหลดใหม่ ให้ใช้ชื่อรูปเดิม
        $newname = $m_img2;
    }

    // อัปเดตข้อมูลลงฐานข้อมูล
    $sql = "UPDATE tbl_member SET 
            m_username = :m_username,
            m_password = :m_password,
            m_name = :m_name,
            m_email = :m_email,
            m_tel = :m_tel,
            m_address = :m_address,
            m_level = :m_level,
            m_img = :m_img
            WHERE m_id = :m_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':m_username', $m_username, PDO::PARAM_STR);
    $stmt->bindParam(':m_password', $m_password, PDO::PARAM_STR);
    $stmt->bindParam(':m_name', $m_name, PDO::PARAM_STR);
    $stmt->bindParam(':m_email', $m_email, PDO::PARAM_STR);
    $stmt->bindParam(':m_tel', $m_tel, PDO::PARAM_STR);
    $stmt->bindParam(':m_address', $m_address, PDO::PARAM_STR);
    $stmt->bindParam(':m_level', $m_level, PDO::PARAM_STR);
    $stmt->bindParam(':m_img', $newname, PDO::PARAM_STR);
    $stmt->bindParam(':m_id', $m_id, PDO::PARAM_INT);
    
    $result = $stmt->execute();

    if ($result) {
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "แก้ไขข้อมูลสำเร็จ",
                  type: "success",
                  timer: 1000,
                  showConfirmButton: false
              }, function() {
                  window.location = "member.php"; 
              });
            }, 1000);
        </script>';
    } else {
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