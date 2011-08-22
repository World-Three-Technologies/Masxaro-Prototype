<?php 
  include_once "../config.php";
  $acc = $_COOKIE["user_acc"];
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
    <?php 

    $control = new UserCtrl();
    $profile = $control->getProfile($acc);
    
    ?>
    <div id="main" class="accounts-view" role="main">
      <h2>Account Settings</h2>
      <nav id="account-nav">
        <ul>
          <li class="profile active">
            <a href="#!profile">Profile</a>
          </li>
          <li class="admin">
            <a href="#!admin">Account Admin</a>
          </li>
          <li class="preference">
            <a href="#!preference">Preference</a>
          </li>
          <li class="email">
            <a href="#!email">Email Address</a>
          </li>
          <li class="credit">
            <a href="#!credit">Credit Card</a>
          </li>
        </ul>
      </nav>
      <div class="boards">
        <div class="profile">
          <div class="content">
            <form action="userOperation.php" message="update successful" ajaxian>
            <dl>
              <input type="hidden" name="opcode" value="update_profile"/>
              <input type="hidden" name="acc" value="<?php echo $profile["user_account"]?>"/>
              <dt>Account name</dt>
              <dd><label><?php echo $profile["user_account"]?></label></dd>
              <dt>First Name</dt>
              <dd><input type="text" class="required info-first_name" name="info-first_name" value="<?php echo $profile["first_name"]?>"/></dd>
              <button class="button">Update</button>
            </dl>
            </form><hr />
            <dl>
              <dt>Privacy Information</dt>
              <dd>This information will only be used for provide better service. </dd>
              <dt>Gender</dt>
              <dd><input type="radio" name="gender"/>Male <input type="radio" name="gender"/>Female</dd>
              <dt>Zip Code</dt>
              <dd><input type="text" /></dd>
              <dt>Age</dt>
              <dd><input type="text" /></dd>
            </dl> 
            <button class="button">Update</button>
          </div>
          <aside>
            <p>You can change your account information here, these information will not be used for commercial spam.</p>
          </aside>
        </div>

        <div class="admin">
          <div class="content">
            <h4>Change your password</h4>
            <dt>Old Password</dt>
            <dd><input type="text"/></dd>
            <dt>New Password</dt>
            <dd><input type="text"/></dd>
            <dt>Confirm Password</dt>
            <dd><input type="text"/></dd>
            <button class="button">Change password</button>
            <hr />
            <h4>Delete your account</h4>
            <button class="button">Delete Account</button>
          </div>
          <aside></aside>
        </div>

        <div class="preference">
          <div class="content">
            <h4>Privacy level</h4>
            <dd>
              <input type="radio" name="preference" />Fully private
            </dd>
            <dd>
              <input type="radio" name="preference" />Conntected but anonymous</dd>
            </dd>
            <dd>
              <input type="radio" name="preference" />Engaged & protected</dd>
            <dt>Language</dt>
            <dd>
              <select>
              	<option value="en">English</option>
              	<option value="la">Spanish</option>
              	<option value="zh">Chinese</option>
              </select>
            </dd>
          </div>
          <aside>
            <p>Change your private level and language preference</p>
          </aside>
        </div>
        <div class="email">
          <div class="content">
            <dt>Your personal email address</dt>
            <dd><input type="text" value="<?php echo $profile["personal"]?>"/></dd>
            <dt>Masxaro email address</dt>
            <dd><label><?php echo $profile["masxaro"]?></label></dd>
            <button class="button">Update</button>
          </div>
          <aside>This email address will be used to notify you on chnages or help you recover lost password,
                 No commercial offerings will come to this address from masxaro.com.</aside>
        </div>
        <div class="credit">
          <div class="content">
            <h3>Conntected Credit Card</h3>
            <dt>Conntect your credit card information to Masxaro.com</dt>
            <dd class="credit">
              <span>City</span>
              <span>1234-xxxx-xxxx-3210</span>
              <span>8/14</span>
              <span class="close"/>
            </dd>
            <dd class="credit">
              <span>Bank of America</span>
              <span>1234-XXXX-XXXX-5678</span>
              <span>9/15</span>
              <span class="close"/>
            </dd>
            <button class="button">add new card</button>
            <button class="button">submit card</button>
          </div>
          <aside>
          <p>Conntect your credit card information to Masxaro will automatically 
             record your spend and receipts.</p>
          </aside>
        </div>
    </div>
  </div>
  <?php 
    include_once "layout/footer.php";
    include_once "layout/template.php"; 
    include_once "layout/scripts.php";
  ?>
  <script src="assets/app/models/user.js"></script>
  <script src="assets/app/views/user_view.js"></script>
  <script src="assets/app/routers/account_router.js"></script>
  <script>
  $(function(){
    new AccountRouter();
    Backbone.history.start({pushState:false});

    $.each($("[ajaxian]"),function(k,v){
      bindAjax(v);
    });
  });
  </script>
</body>
</html>
