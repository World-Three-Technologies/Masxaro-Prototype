window.ActionView = Backbone.View.extend({
  
  el:$("#action-bar"),

  initialize:function(){
    _.bindAll(this,"setTags","setActive","setActiveByTag");   
  },

  isTagsLoaded:false,

  events:{
    "click .action li":"setActive"
  },

  //check if the 
  setTags:function(tag){
    if(this.isTagsLoaded){
      this.setActiveByTag(tag);
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
      view.setActiveByTag(tag);
      view.isTagsLoaded = true;
    });
  },

  setActive:function(){
    this.$(".active").removeClass("active");
    this.$(event.target).parent().addClass("active");
  },

  setActiveByTag:function(tag){
    this.$(".active").removeClass("active");
    this.$(".tag-"+tag).addClass("active");
  }
});
