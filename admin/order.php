<!DOCTYPE html>
<html lang="en">
<?php $menu = "order";?>
<?php include'head.php'; ?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <?php include'nav.php'; ?>
  <?php include'menu.php'; ?>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
         <?php 
            $act = (isset($_GET['act']) ? $_GET['act'] : '');
            if ($act == 'detail') {
                include('order_detail.php'); // แสดงรายละเอียดบิล
            }else{
                include('order_list.php'); // แสดงรายการออเดอร์ทั้งหมด
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