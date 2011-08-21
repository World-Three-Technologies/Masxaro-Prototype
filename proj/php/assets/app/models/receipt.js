var Receipt = Backbone.Model.extend({

  url: 'receiptOperation.php',
  tagUrl: 'tagOperation.php',

  initialize:function(){
    _.bindAll(this,'sync','updateTags','removeTags','saveTags');
  },

  sync:function(method,model,options){
    model.set({"user_account":account});
    var data = {};
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
    $.post(this.url,data,options.success).error(options.error);
  },

  updateTags:function(oldTags){
    var tags = this.get("tags"),
        deletedTags = _.difference(oldTags,tags),
        newTags = _.difference(tags,oldTags);
    this.removeTags(deletedTags);          
    this.saveTags(newTags);          
  },

  saveTags:function(tags){
    if(!tags || tags.length == 0){
      return false;
    }
    $.post(this.tagUrl,{
      opcode:"add_receipt_tags",
      user_account:account,
      tags:tags,
      receipt_id:this.id
    }).success(function(data){
      console.log(data);
    });
  },

  removeTags:function(tags){
    if(!tags || tags.length == 0){
      return false;
    }
    $.post(this.tagUrl,{
      opcode:"delete_receipt_tags",
      user_account:account,
      tags:tags,
      receipt_id:this.id
    }).success(function(data){
      console.log(data);
    });
             
  }
});
