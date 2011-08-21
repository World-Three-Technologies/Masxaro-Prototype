var AccountRouter = Backbone.Router.extend({

  initialize:function(){
    _.bindAll(this,"showPage","clearActive","showPage");
    var user = this.user = new User({
      account:readCookie("user_acc"),
    });
    var userView = new UserView({model:user});
  },

  routes: {
    "!:page" : "showPage"
  },

  showPage:function(page){
    this.clearActive();
    $(".boards > div").hide();
    $("."+page).show();
    $("#account-nav ."+page).addClass("active");
  },
  clearActive:function(){
    $(".active").removeClass("active");
  }
});
