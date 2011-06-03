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
      _.bindAll(this,'render','showReceipt','showReceiptZoom');

      this.model.bind("change",this.render);
    },

    events:{
      "click" : "showReceipt"
    },

    render:function(){
      $(this.el).html(this.template(this.model.toJSON()));     
      if(this.model.get("image")===true){
        this.bindFancybox({content:"<img src='img/fake_receipt.jpg'>"});
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
      _.each(receipts.models.slice(0,this.end),this.renderReceipt);
      this.updateStatus();
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

  var receiptsView = new ReceiptsView();
  receipts.refresh(mockReceipts);
});
