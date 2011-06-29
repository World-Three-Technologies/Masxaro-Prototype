<!doctype html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>Masxaro</title>
  <meta name="description" content="">
  <meta name="author" content="">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="/favicon.ico">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  
  <?php include_once "layout/header.php" ?>

</head>

<body>

  <div id="container">
    <header>
      <div id="header-bar">
        <div id="header-bar-inner" class="clearfix">
          <div id="header-logo">
            <h2>Masxaro</h2>
          </div>
          <div id="header-user">
            <form id="login" action="login.php" method="POST">
              <input type="hidden" name="type" value="user"/>
              <label for="acc" title="Username">Username</label><input type="text" name="acc"></input>
              <label for="pwd" title="password">Password</label><input type="password" name="pwd"></input>
              <button>Login</button>
            </form>
          </div>
        </div>
      </div>
    </header>
    <div id="main" role="main">
      <div class="clearfix">
        <div id="register">
          <form action="register.php" method="POST">
            <table>
              <tr>
                <td><label for="userAccount" title="Username">Username</label></td>
                <td><input type="text" name="userAccount"></input></td>
              </tr>
              <tr>
                <td><label for="firstName" title="firstName">First Name</label></td>
                <td><input type="text" name="firstName"></input></td>
              </tr>
              <tr>
                <td><label for="pwd" title="password">Password</label></td>
                <td><input type="password" name="pwd"></input></td>
              </tr>
              <tr>
                <td><label for="email" title="email">Email</label></td>
                <td><input type="text" name="email"></input></td>
                <input type="hidden" name="type" value="user"></input>
              </tr>
              <tr><td></td><td><button>Register</button></td></tr>
            </table>
          </form>
        </div>
        <div id="product-description">
          <h2>Project Masxaro</h2>
        </div>
      </div>
      <div id="feature">
        <div><h3>Lorem ipsum</h3> <p>Lorem ipsum dolor sit amet, per paulo tritani mentitum an, justo maiorum constituam ius ut, mel ei solum iudico quaestio. Solet nonumy mea ne, suas vidit vim ad. Duo nostrud atomorum suavitate an. Ea sea eius omnium periculis, mea essent impetus epicuri ei. </p></div>
        <div><h3>Lorem ipsum</h3><p>Lorem ipsum dolor sit amet, per paulo tritani mentitum an, justo maiorum constituam ius ut, mel ei solum iudico quaestio. Solet nonumy mea ne, suas vidit vim ad. Duo nostrud atomorum suavitate an. Ea sea eius omnium periculis, mea essent impetus epicuri ei.</p></div>
        <div><h3>Lorem ipsum</h3><p>Lorem ipsum dolor sit amet, per paulo tritani mentitum an, justo maiorum constituam ius ut, mel ei solum iudico quaestio. Solet nonumy mea ne, suas vidit vim ad. Duo nostrud atomorum suavitate an. Ea sea eius omnium periculis, mea essent impetus epicuri ei.</p></div>
      </div>
    </div>
  </div> <!-- eo #container -->
  <?php include_once "layout/footer.php" ?>

</body>
</html>
