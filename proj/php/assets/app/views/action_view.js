window.ActionView = Backbone.View.extend({
  
  el:$("#action-bar"),

  initialize:function(){
    _.bindAll(this,"setTags");   
    this.setTags();
  },

  setTags:function(){
    $.post("tagOperation.php",{
      opcode : "get_user_tags",
      user_account: account,
    }).success(function(data){
      console.log(data);
      var tags = JSON.parse(data);
      _.each(tags,function(tag){
        this.$(".action").append("<li><a href='#tag/"+tag+"'>"+ tag +"</a></li>");
      });
    });
  }
});
