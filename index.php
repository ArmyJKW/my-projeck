<?php require_once("head.php") ?>
session_start();
<body>
        
         <div class="main-wrapper innerpagebg">
        <?php require_once("header.php") ?>

        <!-- Banner Section -->
        <?php require_once("banner.php") ?>
        <!-- /Banner Section -->

        <!-- ล่าสุด Section -->
<div class="container">
        <?php include 'show_product.php'; ?>
        <!-- แสดง carousel ตามประเภทของสินค้า -->
</div>
</body>
<?php require_once("footer.php") ?>