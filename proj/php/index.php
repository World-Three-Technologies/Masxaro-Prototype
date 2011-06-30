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
      <nav id="action-bar">
        <div id="action-bar-inner">
          <h4>catagory</h4>
          <ul>
            <li class="active"><a href="#index">Recent [15]</a></li>
            <li><a href="#index">Grocery</a></li>
            <li><a href="#index">Payment</a></li>
            <li><a href="#index">Overview</a></li>
          </ul>
          <h4>tags</h4>
        <div>
      </nav>
      <div id="content">
        <div id="receipts">
          <div id="search-bar">
            <form action="" method="GET">
              <input type="text" placeholder="search"/>
              <button type="submit" title="search"></button>
            </form>
          </div>
          <table id="receipts-table">
            <td id="ajax-loader" colspan="4"><img src="assets/img/ajax-loader.gif"/></td>
          </table>
          <div class="receipts-stat">
            <span class="stat"></span>
            <button class="more">more</button>
          </div>
        </div>
      </div>
    </div>
  </div> <!-- eo #container -->

  <?php 
    include_once "layout/footer.php";
    include_once "layout/template.php"; 
    include_once "layout/scripts.php";
  ?>

</body>
</html>
