var UserView = Backbone.View.extend({

  el:$("#user"),

  initialize:function(){
     $("#username").text(this.model.get("account")); 
     this.$("#user-flash").text(this.model.get("flash")); 
  }
});
