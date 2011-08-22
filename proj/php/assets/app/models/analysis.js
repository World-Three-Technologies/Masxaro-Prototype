var Analysis = Backbone.Model.extend({
  
  url:"analysisOperation.php",
  
  initialize:function(){
    _.bindAll(this,"totalValue");
  },
  
  totalValue:function(){
    return _.reduce(this.attributes,function(memo,value){
      return memo + parseFloat(value["value"]);
    },0);
  }
});
