var Receipt = Backbone.Model.extend({

  url: 'receiptOperation.php',

  initialize:function(){
    _.bindAll(this,'sync');
  },

  sync:function(method,model,success,error){
    model.set({"user_account":account});
    var data;
    if(method == "read"){
      data = {
        opcode : "user_get_receipt_detail",
        receipt_id: model.get("receipt_id"),
      }
    }else if(method == "delete"){
      data = {
        opcode : "f_delete_receipt",
        receipt_id: model.get("receipt_id")
      }
    }
    $.post(this.url,data,success).error(error);
  }
});
