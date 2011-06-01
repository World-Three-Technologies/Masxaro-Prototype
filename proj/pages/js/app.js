$(function(){
  var mockReceipts = [{
    total_cost:15,
    items:" greek salad and miso soup",
    store:"pret A monger",
    time:"1 day ago"
  },
  {
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  }];

  var Receipt = Backbone.Model;

  var Receipts = Backbone.Collection.extend({
    model: Receipt
  });
  window.receipts = new Receipts(mockReceipts);
  //alert(JSON.stringify(receipts));

  var ReceiptView = Backbone.View.extend({

    tagName:"article",
    
    template:_.template($('#receipt-template').html()),

    initialize:function(){
      _.bindAll(this,'render');

      this.model.bind('change',this.render);
      this.model.view = this;
    },

    render:function(){
      $(this.el).html(this.template(this.model.toJSON()));     
      return this;
    }
  });

  var ReceiptsView = Backbone.View.extend({
    el:$("#receipts"),

    initialize:function(){
      _.bindAll(this,"render","renderReceipt");
      this.render();
    },

    render:function(){
      receipts.each(this.renderReceipt);
    },

    renderReceipt:function(receipt){
      var view = new ReceiptView({model:receipt});
      this.el.append(view.render().el);
    }
  });

  window.receiptsView = new ReceiptsView();

});
