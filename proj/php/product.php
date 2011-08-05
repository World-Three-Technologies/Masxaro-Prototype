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
  <script src="assets/js/vendor/jquery.js"></script>
  <script src="assets/js/vendor/jquery.validate.js"></script>
<script>

var bindAjax = function(el){
  var target = $(el),
      action = target.attr("action"),
      data = {},
      attrs = target.find("input"),
      dest = target.attr("dest"),
      message = target.attr("message");

  target.validate({
    submitHandler:function(form){

      $.each(attrs,function(i,v){
        var el = $(v)
        data[el.attr("name")] = el.val();
      });

      $.post(action,data).success(function(data){
        if(data != "1"){
          alert(data);
          return;
        }
        if(message){
          alert(message);
        }
        if(dest){
          location.href = dest;
        }
      });
    }
  });
}

$(function(){
  $.each($("[ajaxian]"),function(k,v){
    bindAjax(v);
  });
});
  </script>
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
            <form id="login" action="login.php" dest="index.php" ajaxian>
              <input type="hidden" name="type" value="user"/>
              <label for="acc" title="Username">Username</label>
              <input class="required acc" type="text" name="acc"></input>
              <label for="pwd" title="password">Password</label>
              <input class="required pwd" type="password" name="pwd"></input>
              <button>Login</button>
            </form>
          </div>
        </div>
      </div>
    </header>
    <div id="main" role="main">
      <div class="clearfix">
        <div>
          <form id="register" message="Thank you for register, please check your email for activating" action="register.php" ajaxian>
            <table>
              <tr>
                <td><label for="userAccount" title="Username">Username</label></td>
                <td><input class="required user-account" type="text" name="userAccount"></input></td>
                <td></td>
              </tr>
              <tr>
                <td><label for="firstName" title="firstName">First Name</label></td>
                <td><input class="required first-name" type="text" name="firstName"></input></td>
              </tr>
              <tr>
                <td><label for="pwd" title="password">Password</label></td>
                <td><input class="required pwd" minlength="6" type="password" name="pwd"></input></td>
              </tr>
              <tr>
                <td><label for="email" title="email">Email</label></td>
                <td><input class="required email" type="text" name="email"></input></td>
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
