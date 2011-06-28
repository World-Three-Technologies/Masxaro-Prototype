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
    "" : "index"        
  },

  index: function(){
    this.receipts.fetch();
  }

});
