<?php
// เช็คว่ามีการค้นหา หรือ เลือกหมวดหมู่มาหรือไม่?
$q = isset($_GET['q']) ? $_GET['q'] : '';
$type_id_filter = isset($_GET['type_id']) ? $_GET['type_id'] : '';

// ------------------------------------------
// CASE 1: ถ้ามีการค้นหา หรือ เลือกดูหมวดหมู่ย่อย (ใช้ Logic เดิม)
// ------------------------------------------
if ($q != '' || $type_id_filter != '') {
    
    // SQL เดิมสำหรับหน้าค้นหา/ดูรวม
    $where = " WHERE 1=1 "; 
    if($q != ''){ $where .= " AND p.p_name LIKE :q "; }
    if($type_id_filter != ''){ $where .= " AND p.type_id = :type_id "; }

    $sql = "SELECT p.*, t.type_name 
            FROM tbl_product as p 
            LEFT JOIN tbl_type as t ON p.type_id = t.type_id
            $where 
            ORDER BY p.p_id DESC";
    $stmt = $conn->prepare($sql);
    if($q != ''){ $stmt->bindValue(':q', "%$q%"); }
    if($type_id_filter != ''){ $stmt->bindValue(':type_id', $type_id_filter); }
    $stmt->execute();
    $result = $stmt->fetchAll();
    
    // เรียกฟังก์ชันแสดงสินค้า (reuse code)
    renderProductGrid($result, "ผลการค้นหา / สินค้าตามหมวดหมู่");

} else {
    // ------------------------------------------
    // CASE 2: หน้าแรก (Homepage) -> แสดงแยกตามหมวดหมู่ (เหมือนในรูป)
    // ------------------------------------------
    
    // 1. ดึงข้อมูลประเภทสินค้าทั้งหมดมาก่อน
    $sql_type = "SELECT * FROM tbl_type ORDER BY type_id ASC";
    $stmt_type = $conn->prepare($sql_type);
    $stmt_type->execute();
    $types = $stmt_type->fetchAll();

    // 2. วนลูปทีละหมวดหมู่
    foreach ($types as $row_type) {
        $current_type_id = $row_type['type_id'];
        $current_type_name = $row_type['type_name'];

        // 3. ดึงสินค้า 4 ชิ้นล่าสุด ของหมวดหมู่นั้นๆ
        $sql_prd = "SELECT p.*, t.type_name 
                    FROM tbl_product as p 
                    LEFT JOIN tbl_type as t ON p.type_id = t.type_id
                    WHERE p.type_id = :type_id 
                    ORDER BY p.p_id DESC LIMIT 4"; // LIMIT 4 ชิ้น
        
        $stmt_prd = $conn->prepare($sql_prd);
        $stmt_prd->bindValue(':type_id', $current_type_id, PDO::PARAM_INT);
        $stmt_prd->execute();
        $result_prd = $stmt_prd->fetchAll();

        // ถ้าหมวดหมู่นี้มีสินค้า ให้แสดงผล
        if (count($result_prd) > 0) {
            renderSectionHeader($current_type_name, $current_type_id);
            renderProductGrid($result_prd, ""); // แสดงสินค้า
        }
    }
}

// ==================================================================================
// FUNCTIONS: ส่วนฟังก์ชันแสดงผล (แยกออกมาเพื่อให้โค้ดดูง่าย และแก้ CSS ที่เดียวจบ)
// ==================================================================================

// ฟังก์ชันแสดงหัวข้อ Section และปุ่ม "แสดงทั้งหมด"
function renderSectionHeader($title, $type_id) {
    echo '
    <div class="row align-items-end mb-3 mt-5">
        <div class="col-8">
            <h2 style="font-weight: 700; color: #2c3e50; font-size: 1.8rem; margin-bottom: 5px;">'.$title.'</h2>
            <span class="text-muted" style="font-size: 0.9rem;">รายการอัพเดทล่าสุดตามประเภท!</span>
        </div>
        <div class="col-4 text-right">
            <a href="index.php?act=showbytype&type_id='.$type_id.'" class="btn-see-all">
                แสดงทั้งหมด
            </a>
        </div>
    </div>
    <hr class="mb-4 mt-2">';
}

// ฟังก์ชันแสดง Grid สินค้า
function renderProductGrid($result, $title_search) {
    if($title_search != "") echo "<h3>$title_search</h3><br>";
    
    echo '<div class="row">';
    if(count($result) > 0){
        foreach($result as $row_prd){ 
            // *** แก้ Path รูปตรงนี้ให้ตรงกับของคุณ (p_img หรือ m_img) ***
            $img_src = "assets/img/product/" . $row_prd['p_img']; 
            
            $views = isset($row_prd['p_view']) ? $row_prd['p_view'] : 0;
            $date_show = isset($row_prd['date_save']) ? date('d/m/Y', strtotime($row_prd['date_save'])) : date('d/m/Y');
    ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="product-card h-100">
                <span class="badge-category"><?php echo $row_prd['type_name']; ?></span>

                <div class="product-img-wrapper">
                    <a href="product_detail.php?p_id=<?php echo $row_prd['p_id']; ?>">
                        <img src="<?php echo $img_src; ?>" class="product-img" alt="<?php echo $row_prd['p_name']; ?>">
                    </a>
                </div>
                
                <div class="card-body pt-2 pb-0">
                    <h5 class="card-title font-weight-bold text-truncate w-100" style="font-size: 1rem; color: #333;">
                        <?php echo $row_prd['p_name']; ?>
                    </h5>
                    <p class="mb-2 mt-2">
                        ราคา: <span class="text-price font-weight-bold"><?php echo number_format($row_prd['p_price'], 0); ?></span> บาท
                    </p>
                    
                    <div class="mb-3 mt-3">
                        <?php if($row_prd['p_qty'] > 0){ ?>
                            <a href="cart.php?p_id=<?php echo $row_prd['p_id']; ?>&act=add" class="btn-buy">
                                สั่งซื้อ <i class="fas fa-shopping-cart ml-1"></i>
                            </a>
                        <?php } else { ?>
                            <button class="btn btn-secondary btn-sm" disabled style="border-radius: 25px; padding: 8px 30px;">
                                สินค้าหมด
                            </button>
                        <?php } ?>
                    </div>
                </div>

                <div class="card-footer-custom">
                    <div><i class="fas fa-eye mr-1"></i> view <?php echo $views; ?></div>
                    <div><i class="far fa-calendar-alt mr-1"></i> <?php echo $date_show; ?></div>
                </div>
            </div>
        </div>
    <?php 
        } 
    } else { 
        echo '<div class="col-12 text-center text-muted p-5">ไม่พบสินค้า...</div>';
    } 
    echo '</div>'; // ปิด row
}
?>

<style>
    /* ปุ่มแสดงทั้งหมด (แบบในรูป) */
    .btn-see-all {
        border: 1px solid #c90d3d;
        color: #c90d3d;
        border-radius: 25px;
        padding: 5px 20px;
        font-size: 0.85rem;
        text-decoration: none;
        transition: 0.3s;
        background: white;
    }
    .btn-see-all:hover {
        background-color: #c90d3d;
        color: white;
    }

    /* Style ของการ์ดสินค้า (เหมือนเดิม) */
    .product-card {
        border: 1px solid #eee;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: #fff;
        overflow: hidden;
        position: relative;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .badge-category {
        position: absolute;
        top: 15px;
        left: 15px;
        background: #5c93fa;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        z-index: 2;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .product-img-wrapper {
        text-align: center;
        padding: 20px;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fdfdfd;
    }
    .product-img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    .btn-buy {
        background-color: #c90d3d;
        border: none;
        border-radius: 25px;
        color: white;
        padding: 6px 25px;
        font-size: 0.85rem;
        font-weight: 500;
        transition: 0.2s;
        text-decoration: none;
        display: inline-block;
        width: 100%; /* ปุ่มเต็มความกว้าง */
        text-align: center;
    }
    .btn-buy:hover {
        background-color: #a00b30;
        color: white;
    }
    .card-footer-custom {
        padding: 12px 15px;
        font-size: 0.7rem;
        color: #aaa;
        display: flex;
        justify-content: space-between;
        background: #fff;
    }
    .text-price {
        font-size: 1rem;
        color: #333;
    }
</style>
