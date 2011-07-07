var Receipts = Backbone.Collection.extend({
  model: Receipt,

  url: 'receiptOperation.php',

  initialize:function(){
    _.bindAll(this,"sync");
  },

  sync:function(method,model,success,error){
    var data;
    if(method == "read"){
      data = {
        opcode : "user_get_all_receipt",
        acc: this.account
      }
    }
    $.post(this.url,data,success).error(error);
  }
});
