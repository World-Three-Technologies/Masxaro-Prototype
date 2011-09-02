window.ReceiptsView = Backbone.View.extend({
  el:$("#receipts"),

  pageSize:10,

  end:0,

  initialize:function(){
    _.bindAll(this,"render","renderMore","renderReceipt","cleanResults",
                  "nextPage","search","after","fetch","error");
    this.model.bind("sync",this.before);
    this.model.bind("reset",this.render);
    this.actionView = new ActionView();
  },

  events:{
    "click .more": "renderMore",
    "click #search-button": "searchByForm",
    "keyup #search-query": "submitSearch"
  },

  search:function(query,type){

    if(typeof query == "undefined" || query == ""){
      return;
    }
    var keys = query.split(" "); 
    this.before();
    if(type=="keys"){
      this.model.searchByKeys(keys,this.after);
    }else if(type == "tags"){
      this.model.searchByTags(keys,this.after);
    }
  },

  //handle search on search bar
  searchByForm:function(){
    var type = $("#search-type :checked").val()
    this.search($('#search-query').val(),type);
  },
  submitSearch:function(event){
    if(event.which == 13){
      this.searchByForm();
    }
  },

  //pre handle view, show progress bar
  before:function(){
    this.cleanResults();
    this.$('.receipts-stat').hide();
    this.$('#ajax-loader').show();
  },

  //hide progress bar and show status
  after:function(){
    this.$('#ajax-loader').hide();
    this.$('.receipts-stat').show();
  },

  updateStatus:function(){
    this.$(".stat").text("1 to "+ this.end +" in "+this.model.length);

    if(this.end == this.model.length){
      this.$(".more").hide();
    }else{
      this.$(".more").show();
    }
  },

  render:function(){
    this.cleanResults();
    this.$('#ajax-loader').hide();

    _.each(this.model.models.slice(0,this.nextPage()),this.renderReceipt);
    this.updateStatus();
    return this;
  },

  cleanResults:function(){
    this.$('.row').remove();
  },

  renderMore:function(){
    _.each(this.model.models.slice(this.end,this.nextPage()),this.renderReceipt);

    this.updateStatus();
  },

  renderReceipt:function(receipt){
    var view = new ReceiptView({model:receipt});
    this.el.children("table").append(view.render().el);
  },

  //return the next page's length and set the range
  nextPage:function(){
    return this.end = (this.end + this.pageSize <= this.model.length) ? 
            this.end + this.pageSize : this.model.length;
  },

  fetch:function(options){
    this.before();
    this.model.fetch({success:this.after,error:this.error});      
  },

  error:function(){
    $("#ajax-loader").html("<h3>error in model request</h3>");
  }
});
