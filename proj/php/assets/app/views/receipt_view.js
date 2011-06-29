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
      console.log(this.model.get("receipt_time"));

      var items = $(this.el).find('.items');
      _.each(this.model.get("items"),function(model){
        items.append("<div>"+model.item_name +"   x   - $" +model.item_price + " x " + model.item_qty+"</div>");
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
