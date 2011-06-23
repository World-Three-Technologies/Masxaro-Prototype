var Receipts = Backbone.Collection.extend({
  model: Receipt,

  url: 'receiptOperation.php',

  initialize:function(){
    _.bindAll(this,"sync");
  },

  sync:function(method,model,success,error){
    $.post(this.url,{
      opcode : "user_get_all_receipt",
      acc: this.account
    },success)
      .error(error);
  }
});
