var readCookie =function(name){
  var nameEQ = name + "=";
  var ca = document.cookie.split(";");
  for(var i = 0; i<ca.length;i++){
    var c = ca[i];
    while (c.charAt(0)== ' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
}

var User = Backbone.Model;

var user = new User({
  account:readCookie("user_acc"),
  flash:"You have 3 new receipts."
});



var Receipt = Backbone.Model;

var Receipts = Backbone.Collection.extend({
  model: Receipt,

  sync:function(method,model,success,error){
    $.post("./receiptOperation.php",{
      opcode : "user_get_all_receipt",
      acc: user.get("account")
    },function(data){
      console.log(data);
      $("#ajax-loader").hide();
      model.refresh(JSON.parse(data));
    });
  }
});

var mockReceipts = [{
  total_cost:15,
  items:[
    {
      item_name:"test",
      item_price:12,
      item_qty:3
    },{
      item_name:"test2",
      item_price:34,
      item_qty:1
    },{
      item_name:"test3",
      item_price:56,
      item_qty:2
    }  
  ], 
  store_name:"pret A monger",
  receipt_time:"1 day ago",
  image:true
}];

$(function(){
  var UserView = Backbone.View.extend({
  
    el:$("#user"),

    initialize:function(){
       $("#username").text(this.model.get("account")); 
       this.$("#user-flash").text(this.model.get("flash")); 
    }
  });

  window.userView = new UserView({model:user});

  var receipts = new Receipts();
  //alert(JSON.stringify(receipts));

  window.ReceiptView = Backbone.View.extend({

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
      view.find(".items").html(this.renderItem());

      window.lastOpen = this;
      if(this.model.get("image")===true){
        this.bindFancybox({content:"<img src='assets/img/fake_receipt.jpg'>"});
      }
      return this;
    },

    renderItem:function(){
      return _.reduce(this.model.get("items"),function(memo,item){
        return memo + item.item_name + " x" + item.item_qty+" ,";
      },"");
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

    end:2,

    initialize:function(){
      _.bindAll(this,"render","renderMore","renderReceipt");
      this.end = 10 < this.model.length + 1 ? 10 : this.model.length + 1;
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
      _.each(this.model.models.slice(0,this.end),this.renderReceipt);
      this.updateStatus();
      if(this.end >= this.model.length ){
        this.$(".more").hide();
      }
    },

    renderMore:function(){
      var pageLength = (this.end + this.pageSize <= receipts.length) ? this.end + this.pageSize : receipts.length;
      _.each(receipts.models.slice(this.end,pageLength),this.renderReceipt);

      this.end = pageLength;
      this.updateStatus();

      if(this.end === receipts.length){
        this.$(".more").hide();
      }
    },

    renderReceipt:function(receipt){
      var view = new ReceiptView({model:receipt});
      this.el.children("table").append(view.render().el);
    },
  });

  window.receiptsView = new ReceiptsView({model:receipts});
});
