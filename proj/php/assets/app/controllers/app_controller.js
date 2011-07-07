var AppController = Backbone.Controller.extend({

  initialize: function(){
    var user = this.user = new User({
      account:readCookie("user_acc"),
      flash:"You have 3 new receipts."
    });

    var receipts = this.receipts = new Receipts();
    receipts.account = user.get("account");
    window.appView = new AppView({model:receipts });
    window.userView = new UserView({model:user});
  },

  routes: {
    "" : "index",
    "index" : "index"        
  },

  index: function(){
    var options = {
      error: function(){
        $("#ajax-loader").html("<h3>error in model request</h3>");
      }
    }
    this.receipts.fetch(options);
  }

});
