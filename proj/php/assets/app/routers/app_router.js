var AppRouter = Backbone.Router.extend({

  initialize: function(){
    _.bindAll(this,"dashboard","receipts","search","searchTag","getReceiptsView");
    var user = this.user = new User({
      account:readCookie("user_acc"),
    });

    var receipts = this.receipts = new Receipts();
    window.account = receipts.account = user.get("account");
    window.userView = new UserView({model:user});
  },

  getReceiptsView:function(){
    if(!this.receiptsView){
      return new ReceiptsView({model:this.receipts});
    }else{
      return this.receiptsView;
    }                 
  },

  routes: {
    "" : "receipts",
    "dashboard" : "dashboard",
    "receipts" : "receipts",      
    "receipts/search/:query" : "search",
    "receipts/tag/:tag" : "searchTag"
  },

  setView:function(name){
    $("#boards").removeClass().addClass(name);
  },

  dashboard:function(){
    this.setView("dashboard-view");
    this.dashboardView = new DashboardView();
  },

  receipts: function(){
    this.setView("receipts-view");
    this.getReceiptsView().fetch({tag:"recent"});
  },

  search: function(query){
    this.setView("receipts-view");
    this.getReceiptsView().search(query);
  },

  searchTag: function(tag){
    this.setView("receipts-view");
    this.receiptsView = this.getReceiptsView();
    this.receiptsView.search(tag,"tags");           
    this.receiptsView.actionView.setActive(tag);
  }
});
