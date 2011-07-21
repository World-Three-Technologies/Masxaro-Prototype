var AppController = Backbone.Controller.extend({

  initialize: function(){
    var user = this.user = new User({
      account:readCookie("user_acc"),
    });

    var receipts = this.receipts = new Receipts();
    window.account = user.get("account");
    window.appView = new AppView({model:receipts });
    window.userView = new UserView({model:user});
  },

  routes: {
    "" : "index",
    "index" : "index",      
    "search/:query" : "search",
    "category/:category" : "category"
  },

  index: function(){
    var options = {
      error: function(){
        $("#ajax-loader").html("<h3>error in model request</h3>");
      }
    }
    this.receipts.fetch(options);
  },

  search: function(query){
    appView.search(query);      
  },

  category: function(category){
    appView.category(category);        
  }
});
