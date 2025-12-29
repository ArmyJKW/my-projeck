<?php
    // ตรวจสอบ Session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    include 'condb.php';

    // ดึงข้อมูลประเภทสินค้าสำหรับเมนู Dropdown
    $sql = "SELECT * FROM tbl_type";
    $stmt = $conn->query($sql);
?>

<header class="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg header-nav">
            
            <div class="navbar-header">
                <a id="mobile_btn" href="javascript:void(0);">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>
                <a href="index.php" class="navbar-brand logo">
                    <img src="banner/logo.png" width="100" height="85" alt="Logo">
                </a>
            </div>

            <div class="main-menu-wrapper">
                <div class="menu-header">
                    <a href="index.php" class="menu-logo">
                        <img src="banner/logo.png" width="100" height="85" alt="Logo">
                    </a>
                    <a id="menu_close" class="menu-close" href="javascript:void(0);">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
                
                <ul class="main-nav">
                    <li><a href="index.php">หน้าหลัก</a></li>

                    <li class="has-submenu">
                        <a href="#">ประเภทสินค้า <i class="fas fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                <li>
                                    <a href="index.php?act=showbytype&type_id=<?php echo $row['type_id'];?>">
                                        <?php echo $row['type_name'];?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>

                    <li><a href="promotion.php">โปรโมชั่น</a></li>
                    <li><a href="https://www.facebook.com/devtai.com2019" target="_blank">ติดต่อเรา</a></li>
                </ul>
            </div>

            <ul class="nav header-navbar-rht">
                
                <?php if(isset($_SESSION['m_id'])){ ?>
                    <li class="nav-item">
                        <a href="cart.php" class="btn btn-light" style="margin-right: 10px;">
                            <i class="fas fa-shopping-cart"></i> ตะกร้าสินค้า 
                            <?php 
                                // (Optional) แสดงจำนวนสินค้าในตะกร้า
                                if(isset($_SESSION['cart'])){
                                    $count = count($_SESSION['cart']);
                                    echo "<span class='badge badge-danger'>$count</span>";
                                }
                            ?>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="edit_profile.php">
                            <i class="fas fa-user-circle"></i> คุณ <?php echo $_SESSION['m_name']; ?>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link header-login" href="logout.php" style="background-color: #dc3545; color: white;">
                            ออกจากระบบ
                        </a>
                    </li>

                <?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link header-login add-listing" href="login.php" >เข้าสู่ระบบ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link header-login add-listing" href="register.php">
                            <i class="fa-solid fa-plus"></i> สมัครสมาชิก
                        </a>
                    </li>
                <?php } ?>

            </ul>

        </nav>
    </div>
</header>