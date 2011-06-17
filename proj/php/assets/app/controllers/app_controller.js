var AppController = Backbone.Controller.extend({

  initialize: function(){
    var user = new User({
      account:readCookie("user_acc"),
      flash:"You have 3 new receipts."
    });

    var receipts = new Receipts();
    receipts.account = user.get("account");
    window.appView = new AppView({model:receipts });
  },

  routes: {
    "" : "index"        
  },

  index: function(){
    appView.model.fetch();
  }

});
