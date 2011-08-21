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
    <?php 

    $control = new UserCtrl();
    $profile = $control->getProfile("w3tAcc");
    $contactControl = new contactCtrl(); 
    $contacts = $contactControl->getContacts("w3tAcc","user");
    
    ?>
    <div id="main" class="accounts-view" role="main">
      <nav>
      <h3 class="account-nav">
      	<a href="#!profile">Profile</a>
      </h3>
      <h3 class="account-nav">
      	<a href="#!admin">Account Admin</a>
      </h3>
      <h3 class="account-nav">
      	<a href="#!preference">Preference</a>
      </h3>
      <h3 class="account-nav">
      	<a href="#!email">Email Address</a>
      </h3>
      <h3 class="account-nav">
      	<a href="#!credit">Credit Card</a>
      </h3>
      </nav>
      <div class="boards">
        <div class="profile">
          <div class="content">
            Account name : <input type="text" value="<?php echo $profile["user_account"]?>"/><br />
            Name : <input type="text" value="<?php echo $profile["first_name"]?>"/><br />

            <h4>Privacy Information</h4>
            Company : <input type="text"/><br />
            Address : <input type="text"/><br />
            City :<input type="text"/><br />
            State :<input type="text" /><br />
            Country : 
            <select>
              <option value="US">US</option>
              <option value="Canada">Canada</option>
            </select> <br />
            Zip code : <input type="text" /><br />
            Age : <input type="text" /><br />
            Gender : <input type="radio"/>Male <input type="radio"/>Female<br />
          </div>
          <aside>
            <p>Description</p>
          </aside>
        </div>

        <div class="admin">
          <div class="content">
            <h4>Change Password</h4>
            Old Password : <input type="text"/>
            New Password : <input type="text"/>
            Confirm Password : <input type="text"/>
              

            <h4>Destroy Account</h4>
            <button>Destroy</button>
          </div>
          <aside></aside>
        </div>

        <div class="preference">
          <div class="content">
            Privacy Level: Fully privacy, Connected, Engaged
            Language: En <br/>
          </div>
          <aside>
            <p>test</p>
          </aside>
        </div>
        <div class="email">
          <div class="content">
        <?php foreach($contacts as $contact){?>
          Email : <input type="text" value="<?php echo $contact["value"];?>"/><br />
        <?php } ?>
          </div>
          <aside></aside>
        </div>
        <div class="credit">
          <div class="content">
            <h3>Credit Card</h3>
            Credit card information:<input type="text"/>
          </div>
          <aside>
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
  <script>
  var AccountRouter = Backbone.Router.extend({
    initialize:function(){
      _.bindAll(this,"showPage");
      var user = this.user = new User({
        account:readCookie("user_acc"),
      });
      var userView = new UserView({model:user});
    },
    routes: {
      "!:page" : "showPage"
    },
    showPage:function(page){
      $(".boards > div").hide();
      $("."+page).show();
    }
  });

  $(function(){
    new AccountRouter();
    Backbone.history.start({pushState:false});
  });
  </script>
</body>
</html>
