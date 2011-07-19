var ReceiptView = Backbone.View.extend({

  tagName:"tr",
  className:"row",
  template:_.template($('#receipt-row-template').html() || "<div/>"),
  fullTemplate:_.template($('#receipt-full-template').html() || "<div/>"),
  itemTemplate:_.template($('#receipt-item-template').html() || "<div/>"),

  initialize:function(){
    _.bindAll(this,'render','showReceipt','getItemText');
    this.model.bind('change',this.render);
  },

  events:{
    "click .receipt-row" : "showReceipt",
    "click .close" :"render",
    "dblclick .item_name" : "edit",
    "blur input" : "afterEdit"
  },

  render:function(){
    var view = $(this.el);
    view.html(this.template(this.model.toJSON()));

    var text = this.getItemText(this.model.get("items"));
    view.find(".items").html(text);
    view.find(".date").html(new Date(this.model.get("receipt_time")).format());
    return this;
  },

  edit:function(event){
    var receipt = $(event.target).parent().parent();
    receipt.addClass("editing");
    this.$(".editing input").focus();
    //this.$(".receipt-item").addClass("editing");
  },

  afterEdit:function(event){
    var receipt = $(event.target).parent().parent(),
        value = receipt.find("input").val();
    receipt.removeClass("editing");
    receipt.find(".item_name").text(value);

    var item_id = receipt.attr("id-data"); 

    var items = this.model.get("items");
    _.each(items,function(item){
      if(item.item_id == item_id){
        item.item_name = value;
      }
    });
    this.model.set({"items":items});
    this.model.save();
    //this.$(".receipt-item").removeClass("editing");        
  },

  showReceipt:function(){

    if(window.lastOpen){
      window.lastOpen.render();
    }

    $(this.el).html(this.fullTemplate(this.model.toJSON()));
    $(this.el).find(".date").html(new Date(this.model.get("receipt_time")).format());

    var items = $(this.el).find(".items"),
        self = this;

    _.each(self.model.get("items"),function(model){
      items.append(self.itemTemplate(model));
    });

    window.lastOpen = this;
  },

  getItemText:function(items){
    return _.reduce(items,function(memo,item){
      return memo + item.item_name + ", ";
    },"").slice(0,-2);
  }
});
