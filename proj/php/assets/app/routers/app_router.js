var AppRouter = Backbone.Router.extend({

  initialize: function(){
    _.bindAll(this,"index","search","searchTag");
    var user = this.user = new User({
      account:readCookie("user_acc"),
    });

    var receipts = this.receipts = new Receipts();
    window.account = receipts.account = user.get("account");
    window.appView = new AppView({model:receipts });
    window.userView = new UserView({model:user});
    window.actionView = new ActionView();
  },

  routes: {
    "" : "index",
    "index" : "index",      
    "search/:query" : "search",
    "tag/:tag" : "searchTag"
  },

  index: function(){
    var options = {
      error: function(){
        $("#ajax-loader").html("<h3>error in model request</h3>");
      }
    }
    this.receipts.fetch(options);
    actionView.setTags("recent");
  },

  search: function(query){
    appView.search(query);      
  },

  searchTag: function(tag){
    actionView.setTags(tag);
    appView.searchTag(tag);           
  }
});
