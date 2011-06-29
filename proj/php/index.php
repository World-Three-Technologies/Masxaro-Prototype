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
      <div id="content">
        <div id="user" class="clearfix">
          <div id="user-inner">
            <div><img src="assets/img/avator.png"></img></div>
            <div id="user-flash">Hello User!</div>
          </div>
        </div>
        <div id="receipts">
          <div id="search-bar">
            <form action="" method="GET">
              <input type="text" placeholder="search" width="40px"/>
              <button type="submit" title="search"></button>
            </form>
          </div>
          <nav id="main-tabs">
            <ul class="tabs clearfix">
              <li>Recent [15]</li>
              <li>Catagory V</li>
              <li>Overview </li>
              <li style="float:right;">Sort by: Date</li>
            </ul>
          </nav>
          <table id="receipts-table">
            <td id="ajax-loader" colspan="4"><img src="assets/img/ajax-loader.gif"/></td>
          </table>
          <div class="receipts-stat">
            <span class="stat"></span>
            <button class="more">more</button>
          </div>
        </div>
      </div>
      <aside id="sidebar">
        <div style="margin:10px;width:100%;height:300px;border:1px solid rgb(94,206,116)"></div>
        <div style="margin:10px;width:100%;height:300px;border:1px solid rgb(94,206,116)"></div>
      </aside>
    </div>
  </div> <!-- eo #container -->
  <?php include_once "layout/footer.php" ?>
  <?php include_once "layout/template.php" ?>
  <?php include_once "layout/scripts.php" ?>

</body>
</html>
