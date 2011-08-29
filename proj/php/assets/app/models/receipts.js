//fetch receipts data and search receipts by keywords and tags
var Receipts = Backbone.Collection.extend({
  model: Receipt,

  url: 'receiptOperation.php',

  limit:100,

  defaultParams:function(){
    return {
      opcode : "user_get_all_receipt",
      acc: window.account,
      limitStart:0,
      limitOffset:this.limit
    };
  },

  initialize:function(){
    _.bindAll(this,"sync","search","searchByKeys","searchByTags","defaultParams");
  },

  sync:function(method,model,options){
    $.post(this.url,this.defaultParams(),options.success).error(options.error);
  },

  searchByKeys:function(keys,success){
    this.search({ keys:keys });
  },

  searchByTags:function(tags,success){
    this.search({ tags:tags });
  },

  search:function(query,success){
    var model = this;

    var params = this.defaultParams();
    params["opcode"] = "search";
    $.extend(params,query);

    $.post(this.url,params).success(function(data){
      model.reset(data);
      if(success !== null && typeof success !== "undefined"){
        success();
      }
    });
  }
});
