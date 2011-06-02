var Receipt = Backbone.Model;

var Receipts = Backbone.Collection.extend({
  model: Receipt
});

$(function(){
  var mockReceipts = [{
    total_cost:15,
    items:" greek salad and miso soup",
    store:"pret A monger",
    time:"1 day ago",
    image:true
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
    time:"May 15",
    image:true
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15",
    image:true
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15"
  },{
    total_cost:60,
    items:" L.A Noire",
    store:"Game Stop",
    time:"May 15",
    image:true
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


  var receipts = new Receipts();
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
      if(this.model.get("image") === true){
        $(this.el).find("img.receipt-image").prop("src","img/fake_receipt.jpg");
      }
      window.lastOpen = this;
    }
  });

  window.ReceiptsView = Backbone.View.extend({
    el:$("#receipts"),

    pageSize:10,

    start:1,

    end:10,

    initialize:function(){
      _.bindAll(this,"render","renderMore","renderReceipt");

      receipts.bind("refresh",this.render);
      receipts.view = this;
    },

    events:{
      "click .more": "renderMore",
    },

    updateStatus:function(){
      this.$(".receipts-stat .stat").text(this.start + " to "+ this.end +" in "+receipts.length);
    },

    render:function(){
      this.updateStatus();
      _.each(receipts.first(this.pageSize),this.renderReceipt);
    },

    renderMore:function(){
      this.end = (this.end + this.pageSize >= receipts.length) ? receipts.length : this.end + this.pageSize;
      this.updateStatus();
      _.each(receipts.rest(10),this.renderReceipt);
      this.$(".more").hide();
    },

    renderReceipt:function(receipt){
      var view = new ReceiptView({model:receipt});
      this.el.children("table").append(view.render().el);
    },
  });

  var receiptsView = new ReceiptsView();
  receipts.refresh(mockReceipts);
});
