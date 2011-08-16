window.ReceiptsView = Backbone.View.extend({
  el:$("#receipts"),

  pageSize:10,

  start:1,

  end:1,


  initialize:function(){
    _.bindAll(this,"render","renderMore","renderReceipt","cleanResults",
                  "setEnd","search","after","fetch","error");
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
    if(query == "" || query == "undefined"){
      return;
    }
    var keys = query.split(" "); 
    this.before();
    if(type=="keys"){
      this.model.searchByKeys(keys,this.after);
    }else{
      this.model.searchByTags(keys,this.after);
    }
  },

  searchByForm:function(){
    var type = $("#search-type :checked").val()
    this.search($('#search-query').val(),type);
  },

  submitSearch:function(event){
    if(event.which == 13){
      this.searchByForm();
    }
  },

  before:function(){
    this.cleanResults();
    $('.receipts-stat').hide();
    $('#ajax-loader').show();
  },

  after:function(){
    $('#ajax-loader').hide();
    $('.receipts-stat').show();
  },


  updateStatus:function(){
    this.$(".stat").text(this.start + " to "+ this.end +" in "+this.model.length);
  },

  render:function(){
    this.cleanResults();
    this.setEnd();

    this.$('#ajax-loader').hide();

    _.each(this.model.models.slice(0,this.end),this.renderReceipt);
    this.updateStatus();

    if(this.end >= this.model.length ){
      this.$(".more").hide();
    }else{
      this.$(".more").show();
    }
    return this;
  },

  cleanResults:function(){
    this.$('.row').remove();
  },

  renderMore:function(){
    var pageLength = (this.end + this.pageSize <= this.model.length) 
                     ? this.end + this.pageSize : this.model.length;
    _.each(this.model.models.slice(this.end,pageLength),this.renderReceipt);

    this.end = pageLength;

    this.updateStatus();

    if(this.end === this.model.length){
      this.$(".more").hide();
    }else{
      this.$(".more").show();
    }
  },

  renderReceipt:function(receipt){
    var view = new ReceiptView({model:receipt});
    this.el.children("table").append(view.render().el);
  },

  setEnd:function(){
    this.end = (this.model.length > 10) ? 10 : this.model.length;       
  },

  searchTag:function(tags){
    this.before();
    this.model.searchTag(tags.split("-"),this.after);
    console.log(tags);
  },

  fetch:function(options){
    this.before();
  class  this.model.fetch({success:this.after,error:this.error});      
  },

  error:function(){
    $("#ajax-loader").html("<h3>error in model request</h3>");
  }
});
