//handle receipt action: tag clicked
//and initiate tags when receipts_view loaded
window.ActionView = Backbone.View.extend({
  
  el:$("#action-bar"),

  tagTemplate : _.template("<li class='tag-<%= tag %>'>"+
                           "<a href='#receipts/tag/<%= tag %>'><%= tag %></a></li>"),
  recentTag:$('<li class="tag-recent"><a href="#receipts">recent</a></li>'),

  initialize:function(){
    _.bindAll(this,"setTags","setActive");   

    this.$(".action").empty()
        .append(this.recentTag);
    this.setTags("recent");
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
        this.$(".action").append(view.tagTemplate({tag:tag}));
      });
      view.setActive(tag);
      view.tagsIsLoaded = true;
    });
  },

  setActive:function(target){
    this.$(".active").removeClass("active");
    if(typeof target == "undefined"){
      target = this.$(event.target).parent();
    }else{
      target = this.$(".tag-"+target).addClass("active");
    }
    target.addClass("active");
  }
});
