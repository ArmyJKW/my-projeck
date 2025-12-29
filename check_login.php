<?php 
session_start();
if(isset($_POST['username'])){
    //เชื่อมต่อฐานข้อมูล
    require_once("condb.php");

    //รับค่าจากฟอร์ม Login
    $username = $_POST['username'];
    $password = $_POST['password'];

    // รองรับกรณีที่หน้า Login ส่งมาชื่อ m_username / m_password
    if(isset($_POST['m_username'])){
        $username = $_POST['m_username'];
    }
    if(isset($_POST['m_password'])){
        $password = $_POST['m_password'];
    }

    // 1. เช็คข้อมูลจากตาราง tbl_member
    $stmt = $conn->prepare("SELECT * FROM tbl_member WHERE m_username = :username AND m_password = :password");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->execute();

    // 2. ถ้าเจอ Username และ Password ถูกต้อง
    if($stmt->rowCount() > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // สร้าง Session
        $_SESSION['m_id'] = $row['m_id'];
        $_SESSION['m_username'] = $row['m_username'];
        $_SESSION['m_name'] = $row['m_name'];
        $_SESSION['m_level'] = $row['m_level']; 
        $_SESSION['m_img'] = $row['m_img'];
        
        // **สำคัญ** เพิ่มบรรทัดนี้ เพื่อเก็บสถานะลง session ให้ head.php ตรวจสอบผ่าน
        $_SESSION['m_status'] = $row['m_status']; 

        // 3. ตรวจสอบระดับผู้ใช้งาน (Level) เพื่อส่งไปหน้าบ้านที่ถูกต้อง
        if($_SESSION['m_level'] == 'admin'){
            echo "<script>
                  window.location = 'admin/index.php';
                  </script>";
        } else {
            echo "<script>
                  window.location = 'index.php';
                  </script>";
        }

    } else {
        // 4. ถ้าไม่เจอ หรือรหัสผิด
        echo "<script>
              alert('Username หรือ Password ไม่ถูกต้อง');
              window.history.back();
              </script>";
    }
}
?>