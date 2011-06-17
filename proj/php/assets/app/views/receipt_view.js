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
