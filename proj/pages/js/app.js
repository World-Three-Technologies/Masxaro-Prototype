var Receipt = Backbone.Model;

var Receipts = Backbone.Collection.extend({
  model: Receipt
});

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
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  }];


  window.receipts = new Receipts();
  //alert(JSON.stringify(receipts));

  window.ReceiptView = Backbone.View.extend({

    tagName:"tr",

    className:"row",
    
    template:_.template($('#receipt-row-template').html() || "<div/>"),

    fullTemplate:_.template($('#receipt-full-template').html() || "<div/>"),

    initialize:function(){
      _.bindAll(this,'render','showReceipt');

      this.model.bind("change",this.render);
    },

    events:{
      "click" : "showReceipt"
    },

    render:function(){
      $(this.el).html(this.template(this.model.toJSON()));     
      return this;
    },

    showReceipt:function(){
      if(window.lastOpen){
        window.lastOpen.render();
      }
      $(this.el).html(this.fullTemplate(this.model.toJSON()));
      window.lastOpen = this;
    }
  });

  var ReceiptsView = Backbone.View.extend({
    el:$("#receipts"),

    initialize:function(){
      _.bindAll(this,"render","renderMore","renderReceipt");

      receipts.bind("refresh",this.render);
      receipts.view = this;
    },

    events:{
      "click .more": "renderMore",
    },

    render:function(){
      _.each(receipts.first(10),this.renderReceipt);
    },

    renderMore:function(){
      _.each(receipts.rest(10),this.renderReceipt);
      this.$(".more").hide();
    },

    renderReceipt:function(receipt){
      var view = new ReceiptView({model:receipt});
      this.el.children("table").append(view.render().el);
    },
  });

  window.receiptsView = new ReceiptsView();
  receipts.refresh(mockReceipts);
});
