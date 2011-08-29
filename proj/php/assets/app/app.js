$(function(){
  //init Backbone application 
  new AppRouter();
  Backbone.history.start({pushState:false});
});
