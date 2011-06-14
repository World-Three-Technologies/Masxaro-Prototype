var User = Backbone.Model;
var Receipt = Backbone.Model;

var Receipts = Backbone.Collection.extend({
  model: Receipt,

  initialize:function(){
    _.bindAll(this,"sync");
  },

  sync:function(method,model,success,error){
    $.post("./receiptOperation.php",{
      opcode : "user_get_all_receipt",
      acc: this.account
    },function(data){
      $("#ajax-loader").hide();
      model.refresh(JSON.parse(data));
    });
  }
});

var UserView = Backbone.View.extend({

  el:$("#user"),

  initialize:function(){
     $("#username").text(this.model.get("account")); 
     this.$("#user-flash").text(this.model.get("flash")); 
  }
});

var ReceiptView = Backbone.View.extend({

  tagName:"tr",

  className:"row",
  
  template:_.template($('#receipt-row-template').html() || "<div/>"),

  fullTemplate:_.template($('#receipt-full-template').html() || "<div/>"),

  initialize:function(){
    _.bindAll(this,'render','showReceipt','showReceiptZoom');

    this.model.bind("change",this.render);
  },

  events:{
    "click" : "showReceipt"
  },

  render:function(){
    var view = $(this.el);
    view.html(this.template(this.model.toJSON()));

    var text = _.reduce(this.model.get("items"),function(memo,item){
      return memo + item.item_name + " x" + item.item_qty+" ,";
    },"");

    view.find(".items").html(text);
    window.lastOpen = this;
    if(this.model.get("image")===true){
      this.bindFancybox({content:"<img src='assets/img/fake_receipt.jpg'>"});
    }
    return this;
  },

  bindFancybox:function(model){
    $(this.el).fancybox(model);
  },

  showReceipt:function(){
    if(window.lastOpen){
      window.lastOpen.render();
    }
    if(this.model.get("image") !== true){
      $(this.el).html(this.fullTemplate(this.model.toJSON()));
      var items = $(this.el).find('.items');
      _.each(this.model.get("items"),function(model){
        items.append("<div>"+model.item_name +"     - $" +model.item_price + " x " + model.item_qty+"</div>");
      });
      window.lastOpen = this;
    }
  },

  showReceiptZoom:function(){
      
  }
});

window.ReceiptsView = Backbone.View.extend({
  el:$("#receipts"),

  pageSize:10,

  start:1,

  end:1,

  initialize:function(){
    _.bindAll(this,"render","renderMore","renderReceipt","setEnd");
    this.model.bind("refresh",this.render);
    this.model.bind("change",this.render);
    this.model.view = this;
    this.model.fetch();
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
    if(this.end >= this.model.length ){
      this.$(".more").hide();
    }
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



$(function(){

  var user = new User({
    account:readCookie("user_acc"),
    flash:"You have 3 new receipts."
  });

  window.userView = new UserView({model:user});

  var receipts = new Receipts();
  receipts.account = user.get("account");

  window.receiptsView = new ReceiptsView({model:receipts });
});
