window.AppView = Backbone.View.extend({
  el:$("#receipts"),

  pageSize:10,

  start:1,

  end:1,

  initialize:function(){
    _.bindAll(this,"render","renderMore","renderReceipt","cleanResults",
                  "setEnd","search","after","fetch");
    this.model.bind("sync",this.before);
    this.model.bind("reset",this.render);
    this.model.bind("change",this.render);
  },

  events:{
    "click .more": "renderMore",
    "click #search-button": "searchByForm",
    "keyup #search-query": "submitSearch"
  },

  search:function(query){
    this.before();
    this.model.search(query,this.after);
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

  searchByForm:function(){
    this.search($('#search-query').val());
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
  },

  fetch:function(options){

    this.before();
    this.model.fetch({success:this.after,error:options.error});      
  }
});
