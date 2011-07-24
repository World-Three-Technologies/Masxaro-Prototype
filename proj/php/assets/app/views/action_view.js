window.ActionView = Backbone.View.extend({
  
  el:$("#action-bar"),

  initialize:function(){
    _.bindAll(this,"setTags","setActive");   
  },

  tagsIsLoaded:false,

  events:{
    "click .action li":"setActive"
  },

  setTags:function(tag){
    if(this.tagsIsLoaded){
      this.setActive(tag);
      return;
    }
    var view = this;
    $.post("tagOperation.php",{
      opcode : "get_user_tags",
      user_account: account,
    }).success(function(data){
      var tags = JSON.parse(data);
      _.each(tags,function(tag){
        this.$(".action").append("<li class='tag-"+ tag +
                                 "'><a href='#tag/"+tag+"'>"+ tag +"</a></li>");
      });
      view.setActive(tag);
      view.tagsIsLoaded = true;
    });
  },

  setActive:function(target){
    this.$(".active").removeClass("active");
    if(target == "undefined"){
      target = this.$(event.target).parent();
    }else{
      target = this.$(".tag-"+target).addClass("active");
    }
    target.addClass("active");
  }
});
