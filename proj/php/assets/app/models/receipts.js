var Receipts = Backbone.Collection.extend({
  model: Receipt,

  initialize:function(){
    _.bindAll(this,"sync");
  },

  sync:function(method,model,success,error){
    $.post("./receiptOperation.php",{
      opcode : "user_get_all_receipt",
      acc: this.account
    },function(data){
      $("#ajax-loader").hide();
      model.refresh(JSON.parse(data));
    });
  }
});
