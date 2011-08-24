var AppRouter = Backbone.Router.extend({

  initialize: function(){
    _.bindAll(this,"dashboard","receipts","search","searchTag","getReceiptsView","getAnalysisView");
    var user = this.user = new User({
      account:readCookie("user_acc"),
    });
    window.account = user.get("account");

    window.userView = new UserView({model:user});

  },

  getReceiptsView:function(){
    return this.receiptsView || new ReceiptsView({model:new Receipts()});
  },

  getAnalysisView:function(){
    return this.analysisView || new AnalysisView();
  },

  routes: {
    "" : "dashboard",
    "dashboard" : "dashboard",
    "receipts" : "receipts",      
    "analysis" : "analysis",      
    "reports" : "reports",      
    "deals" : "deals", 
    "media" : "media",      
    "receipts/search/:query" : "search",
    "receipts/tag/:tag" : "searchTag"
  },

  setView:function(name){
    this.setMainTab(name.split("-")[0]);
    $("#boards").removeClass().addClass(name);
  },

  setMainTab:function(tab){
    $("#main-tab > .active").removeClass("active");           
    $("#main-tab > ."+tab).addClass("active");
  },

  dashboard:function(){
    this.setView("dashboard-view");
    this.dashboardView = new DashboardView();
  },

  analysis:function(){
    this.setView("analysis-view");
    this.analysisView = this.getAnalysisView();
  },

  reports:function(){
    this.setView("reports-view");
    this.reportsView = new ReportsView();
  },

  media:function(){
    this.setView("media-view");
    this.MediaView = new MediaView();
  },

  deals:function(){
    this.setView("deals-view");
    this.dealsView = new DealsView();
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
