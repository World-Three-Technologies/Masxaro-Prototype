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
  <script src="assets/js/util.js"></script>
  <script>
  $(function(){
    $.each($("[ajaxian]"),function(k,v){
      bindAjax(v);
    });
    $("#sign-in-button").click(function(e){
      e.preventDefault();
      $.Deferred(function(def){
        def.pipe(function(){ $("#sign-in").hide(); })
           .pipe(function(){ $("#login").fadeIn();});
      }).resolve();
    })
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
            <span id="sign-in">Already in the network? <a id="sign-in-button" href="#">Sign In</a></span>
            <form id="login" action="login.php" dest="index.php" ajaxian>
              <input type="hidden" name="type" value="user"/>
              <span>
                <label for="acc" title="Username">Username</label>
                <input class="required acc" type="text" name="acc"></input>
              </span>
              <span>
                <label for="pwd" title="password">Password</label>
                <input class="required pwd" type="password" name="pwd"></input>
              </span>
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
                <td><input class="required user-account" type="text" name="userAccount"></input>@masxaro.com</td>
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
          <h1>Masxaro</h1>
          <h3>where you are<br /> the center of a global Network </h3>
        </div>
      </div>
      <div id="feature">
        <div class="clearfix">
<img src="assets/img/masxaro_icon.png"  alt="Masxaro" />
<h2>Masxaro</h2> 
<p>
What is it? Masxaro is the international word for network; It is a network for everyone - where you are centered and in control. As an open source platform, it is contributed by engineers & designers world-wide with total transparency to provide collaboration & privacy at the same time. All on your terms. It is the ultimate connection.
</p>
</div>
        <div class="clearfix">
<img src="assets/img/receipts_icon.png" alt="Capture" />
<h2>Capture</h2><p>
Nearly everything in our world comes with a receipt; They trail our choices. often we don’t need them, but
with masxaro, you can elegantly capture & store them to document, recall, learn and save money.
</p></div>
        <div class="clearfix">
<img src="assets/img/report_icon.png" alt="Expense" />
<h2>Expense</h2><p>
Use our tools to create user-friendly expense reports and workflows. From receipt capture to report creation to approval, the expense module will streamline your life from purchase to accounting.
</p></div>
        <div class="clearfix">
<img src="assets/img/analysis_icon.png" alt="Learn" />
<h2>Learn</h2><p>
Find out about what and where you spend. Even
better, discover where you might save more. You can
even collaborate with others in the network to find,
review products & services you care about.
</p></div>
        <div class="clearfix">
<img src="assets/img/deals_icon.png" alt="Save" />
<h2>Save</h2><p>
Knowledge is power using masxaro, you will quickly discover who is offering the best value, receive discounts on products you usually buy, and learn about new offerings that can save you money & time. Start saving immediately - Our price is free!
</p></div>
        <div class="clearfix">
<img src="assets/img/protect_icon.png" alt="Protect" />
<h2>Protect</h2><p>
Privacy & security are extremely important to all of us. We created masxaro from the ground up with security in mind. Over two million open source coders can contribute vigilantly to protecting your information.
</p></div>
      </div>
    </div>
  </div> <!-- eo #container -->
  <?php include_once "layout/footer.php" ?>
</body>
</html>
