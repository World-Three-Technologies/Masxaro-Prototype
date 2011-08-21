var Receipts = Backbone.Collection.extend({
  model: Receipt,

  url: 'receiptOperation.php',

  limit:100,

  initialize:function(){
    _.bindAll(this,"sync","search","searchByKeys","searchByTags");
  },

  sync:function(method,model,options){
    var data;
    if(method == "read"){
      data = {
        opcode : "user_get_all_receipt",
        acc: this.account,
        limitStart:0,
        limitOffset:this.limit
      }
    }
    $.post(this.url,data,options.success).error(options.error);
  },

  searchByKeys:function(keys,success){
    this.search({ keys:keys });
  },

  searchByTags:function(tags,success){
    this.search({ tags:tags });
  },

  search:function(data,success){
    var model = this;
    data["opcode"] = "search";
    data["acc"] = account;
    data["limitStart"] = 0;
    data["limitOffset"] = this.limit;
    $.post(this.url,data).success(function(data){
      model.reset(data);
      if(success !== null && typeof success !== "undefined"){
        success();
      }
    });
  }
});
