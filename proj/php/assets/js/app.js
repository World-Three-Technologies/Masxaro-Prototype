var Contact = Backbone.Model.extend({

});
var Receipt = Backbone.Model.extend({
  sync:function(method,model,success,error){
    var data;
    if(method == "read"){
      data = {
        opcode : "user_get_receipt_detail",
        receipt_id: model.get("receipt_id")
      }
    }else if(method == "delete"){
      data = {
        opcode : "f_delete_receipt",
        receipt_id: model.get("receipt_id")
      }
    }
    $.post(this.url,data,success).error(error);
  }
});
var Receipts = Backbone.Collection.extend({
  model: Receipt,

  url: 'receiptOperation.php',

  initialize:function(){
    _.bindAll(this,"sync");
  },

  sync:function(method,model,success,error){
    var data;
    if(method == "read"){
      data = {
        opcode : "user_get_all_receipt",
        acc: this.account
      }
    }
    $.post(this.url,data,success).error(error);
  }
});
var User = Backbone.Model;
window.AppView = Backbone.View.extend({
  el:$("#receipts"),

  pageSize:10,

  start:1,

  end:1,

  initialize:function(){
    _.bindAll(this,"render","renderMore","renderReceipt","setEnd");
    this.model.bind("refresh",this.render);
    this.model.bind("change",this.render);
    this.model.view = this;
  },

  events:{
    "click .more": "renderMore",
  },

  updateStatus:function(){
    this.$(".receipts-stat .stat").text(this.start + " to "+ this.end +" in "+this.model.length);
  },

  render:function(){
    this.setEnd();
    _.each(this.model.models.slice(0,this.end),this.renderReceipt);
    this.updateStatus();

    this.$("#ajax-loader").hide();

    if(this.end >= this.model.length ){
      this.$(".more").hide();
    }
    return this;
  },

  renderMore:function(){
    var pageLength = (this.end + this.pageSize <= this.model.length) ? this.end + this.pageSize : this.model.length;
    _.each(this.model.models.slice(this.end,pageLength),this.renderReceipt);

    this.end = pageLength;
    this.updateStatus();

    if(this.end === this.model.length){
      this.$(".more").hide();
    }
  },

  renderReceipt:function(receipt){
    var view = new ReceiptView({model:receipt});
    this.el.children("table").append(view.render().el);
  },

  setEnd:function(){
    this.end = (this.model.length > 10) ? 10 : this.model.length;       
  }
});
var ReceiptView = Backbone.View.extend({

  tagName:"tr",

  className:"row",
  
  template:_.template($('#receipt-row-template').html() || "<div/>"),

  fullTemplate:_.template($('#receipt-full-template').html() || "<div/>"),

  initialize:function(){
    _.bindAll(this,'render','showReceipt','getItemText');

    this.model.bind('change',this.render);
  },

  events:{
    "click" : "showReceipt"
  },

  render:function(){
    var view = $(this.el);
    view.html(this.template(this.model.toJSON()));

    var text = this.getItemText(this.model.get("items"));
    view.find(".items").html(text);
    view.find(".date").html(new Date(this.model.get("receipt_time")).format());
    return this;
  },

  showReceipt:function(){
    if(window.lastOpen){
      window.lastOpen.render();
    }
    if(this.model.get("image") !== true){

      $(this.el).html(this.fullTemplate(this.model.toJSON()));

      $(this.el).find(".date").html(new Date(this.model.get("receipt_time")).format());
      var items = $(this.el).find(".items");
      _.each(this.model.get("items"),function(model){
        items.append("<div class='item'>"+model.item_name +"   x   - $" +model.item_price + " x " + model.item_qty+"</div>");
      });

      window.lastOpen = this;
    }
  },

  getItemText:function(items){
    return _.reduce(items,function(memo,item){
      return memo + item.item_name + ", ";
    },"").slice(0,-1);
  }
});
var UserView = Backbone.View.extend({

  initialize:function(){
    _.bindAll(this,"render");
    this.el = $("#user");
    this.render();
  },

  render:function(){
    $("#username").text(this.model.get("account")); 
    this.$("#user-flash").text(this.model.get("flash")); 
    return this;
  }
});
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
    this.receipts.fetch();
  }

});
$(function(){
  new AppController();
  Backbone.history.start();
});
