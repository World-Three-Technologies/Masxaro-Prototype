<?php 
  include_once "../config.php";
  $acc = $_COOKIE["acc"];
  if(!Tool::authenticate($acc)){
    Tool::redirectToProduct();
  }
?>
<!doctype html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>Masxaro</title>
  <?php include_once "layout/header.php" ?>

</head>
<body>
  <?php include_once "layout/header-bar.php" ?>
  <div id="container">
    <div id="main" role="main">
      <?php include_once "layout/tabs.php" ?>
      <div id="boards">
        <?php include_once "pages/receipts.php" ?>
        <?php include_once "pages/dashboard.php" ?>
        <?php include_once "pages/analysis.php" ?>
        <?php include_once "pages/reports.php" ?>
        <?php include_once "pages/media.php" ?>
        <?php include_once "pages/deals.php" ?>
      </div>
    </div>
  </div> <!-- eo #container -->

  <?php 
    include_once "layout/footer.php";
    include_once "layout/template.php"; 
    include_once "layout/scripts.php";
  ?>
  <script src="assets/js/app.js"></script>
</body>
</html>
