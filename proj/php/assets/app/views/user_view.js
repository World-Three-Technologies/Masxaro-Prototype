var UserView = Backbone.View.extend({

  initialize:function(){
    _.bindAll(this,"render");
    this.el = $("#user");
    this.render();
  },

  render:function(){
    $("#username").text("Hello, " + this.model.get("account")); 
    return this;
  }
});
