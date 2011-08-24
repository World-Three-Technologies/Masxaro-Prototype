var Receipt = Backbone.Model.extend({

  tagUrl: 'tagOperation.php',

  initialize:function(){
    _.bindAll(this,'updateTags','removeTags','saveTags','changeTags');
  },

  updateTags:function(oldTags){
    var tags = this.get("tags"),
        deletedTags = _.difference(oldTags,tags),
        newTags = _.difference(tags,oldTags);
    this.removeTags(deletedTags);          
    this.saveTags(newTags);          
  },

  saveTags:function(tags){
    tags || (tags = []);
    this.changeTags("add_receipt_tags",tags);
  },

  removeTags:function(tags){
    tags || (tags = []);
    this.changeTags("delete_receipt_tags",tags);
  },
  
  changeTags:function(opcode,tags){
    if(!tags || tags.length == 0) return;
    $.post(this.tagUrl,{
      opcode:opcode,
      user_account:account,
      tags:tags,
      receipt_id:this.id
    });
  }
});
