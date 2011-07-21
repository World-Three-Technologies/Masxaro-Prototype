var Receipts = Backbone.Collection.extend({
  model: Receipt,

  url: 'receiptOperation.php',

  initialize:function(){
    _.bindAll(this,"sync","search");
  },

  sync:function(method,model,success,error){
    var data;
    if(method == "read"){
      data = {
        opcode : "user_get_all_receipt",
        acc: account
      }
    }
    $.post(this.url,data,success).error(error);
  },

  search:function(query,success){
    var model = this;
    $.post(this.url,{
      opcode : "key_search",
      acc: account,
      key : query
    }).success(function(data){
      model.refresh(data);
      success();
    });
  },

  category:function(category,success){
    var model = this;
    $.post(this.url,{
      opcode : "get_category_receipt",
      acc:this.account,
      receipt_category : category
    }).success(function(data){
      model.refresh(data);
      success();
    });
  }
});
