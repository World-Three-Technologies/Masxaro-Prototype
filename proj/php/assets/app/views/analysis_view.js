window.AnalysisView = Backbone.View.extend({

  initialize:function(){
    var canvas = document.getElementById("analysis-canvas");
    var ctx = this.ctx = canvas.getContext("2d");
    ctx.fillStyle = "black";
    ctx.strokeRect(10,20,50,100);
    ctx.beginPath();
    ctx.moveTo(75,50);
    ctx.lineTo(100,75);
    ctx.lineTo(100,25);
    ctx.fill();
    ctx.closePath();

    ctx.beginPath();
    ctx.arc(200,200,50,0,Math.PI*2,true);
    ctx.fill();
    ctx.closePath();
    ctx.beginPath();
    ctx.fillStyle = "white";
    ctx.arc(200,200,25,0,Math.PI*2,true);
    ctx.fill();
    ctx.closePath();
  }
});
