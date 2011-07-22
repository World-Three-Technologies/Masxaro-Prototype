var Receipts = Backbone.Collection.extend({
  model: Receipt,

  url: 'receiptOperation.php',

  initialize:function(){
    _.bindAll(this,"sync","search");
  },

  sync:function(method,model,options){
    var data;
    if(method == "read"){
      data = {
        opcode : "user_get_all_receipt",
        acc: account
      }
    }
    $.post(this.url,data,options.success).error(options.error);
  },

  search:function(query,success){
    var model = this;
    $.post(this.url,{
      opcode : "key_search",
      acc: account,
      key : query
    }).success(function(data){
      model.reset(data);
      success();
    });
  },
});
