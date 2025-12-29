<!DOCTYPE html>
<html lang="en">
<?php $menu = "stock";?>
<?php include'head.php'; ?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <?php include'nav.php'; ?>
  <?php include'menu.php'; ?>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <a href="product.php?act=add" type="button" class="btn btn-success btn-rounded btn-sm"><i class="fas fa-plus-circle"></i> เพิ่มสินค้าใหม่</a><p>
         <?php 
            $act = (isset($_GET['act']) ? $_GET['act'] : '');
            if ($act == 'add') {
                include('product_add.php'); // ยืมหน้าเพิ่มสินค้ามาใช้
            }elseif ($act == 'edit') {
                include('product_edit.php'); // ยืมหน้าแก้ไขสินค้ามาใช้
            }else{
                include('stock_list.php'); // เรียกไฟล์ตารางสต็อกมาแสดง
            }
          ?>
      </div></div>
  </div>
  <?php include'footer.php'; ?>
  <aside class="control-sidebar control-sidebar-dark">
    </aside>
  </div>
<?php include'script.php'; ?>
</body>
</html>