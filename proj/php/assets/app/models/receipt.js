var Receipt = Backbone.Model.extend({
  sync:function(method,model,success,error){
    var data;
    if(method == "read"){
      data = {
        opcode : "user_get_receipt_detail",
        receipt_id: model.get("receipt_id")
      }
    }else if(method == "delete"){
      data = {
        opcode : "f_delete_receipt",
        receipt_id: model.get("receipt_id")
      }
    }else if(method == "update"){
      data = {
        opcode : ""
      }
    }
    $.post(this.url,data,success).error(error);
  }
});
