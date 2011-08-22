window.AnalysisView = Backbone.View.extend({
  el:$("#analysis-view"),

  colors:["#FF0","#FFF","#356","#A23","#F2A","#0E0","#AC5","0F0"],

  initialize:function(){
    _.bindAll(this,"drawChart","drawSlice","setTable",
                   "initCanvas","fetchModel","fetchStore");

    this.model = new Analysis();

    this.initCanvas();
    
    this.fetchModel("tag");
  },

  events:{
    "click .store":"fetchStore"
  },

  fetchStore:function(){
    this.fetchModel("store");
  },

  fetchModel:function(type){
    
    var view = this;
    this.model.clear()
    this.model.fetch({
      data:{"opcode":type},
      processData:true,
      success:function(model){
        view.setTable(model);
        view.drawChart(model);
      }
    });
  },

  initCanvas:function(){
             
    var canvas = this.canvas = document.getElementById("analysis-canvas");
    var ctx = this.ctx = canvas.getContext("2d");

    this.width = canvas.width;
    this.height = canvas.height;
    this.centerX = this.width / 2;
    this.centerY = this.height / 2;
    this.radius = 165;

    this.params = {
      borderWidth:1,
      borderStyle:"#fff",
      sliceGradientColour: "#ddd",
      labelFont : "bold 13px 'Trebuchet MS', Verdana, sans-serif",
      fontColor : "black"
    };
  },

  setTable:function(model){
    var table = this.$("#data-table");
    _.each(model.attributes,function(v,k){
      table.append($("<tr><td>"+v['category']+"</td><td>"+v['value']+"$</td></tr>"));
    });
  },

  drawChart:function(model){

    var chartData = this.chartData = {},
        currentPos = this.currentPos = 0,
        totalValue = this.totalValue = model.totalValue();

    _.each(model.attributes,function(v,k){
      chartData[v['category']] = {
        startAngle: 2 * Math.PI * currentPos,
        endAngle: 2 * Math.PI * (currentPos + ( v['value'] / totalValue) ),
        value : v['value']
      }
      currentPos += v['value'] / totalValue;
    });

    this.ctx.clearRect(0,0,this.width,this.height);
    var view = this;
    _.each(this.chartData,function(v,k){
      view.drawSlice(k,v);
    });
  },

  drawSlice:function(name,value){
    var ctx = this.ctx,
        startAngle = value["startAngle"],
        endAngle = value["endAngle"],
        midAngle = (startAngle + endAngle) / 2,
        textDistance = this.radius * 0.65,
        centerX = this.centerX,
        centerY = this.centerY;
    ctx.beginPath();
    ctx.moveTo(centerX,centerY);    
    ctx.arc(centerX,centerY,this.radius,startAngle,endAngle);
    ctx.lineTo(centerX,centerY);    
    ctx.closePath();
    ctx.fillStyle = this.colors.pop()//this.params.sliceGradientColour; 
    ctx.fill();

    ctx.fillStyle = this.params.fontColor;
    ctx.textAlign = "center";
    ctx.font = this.params.labelFont;
    ctx.fillText(name + " (" + parseInt((value["value"]/this.totalValue) * 100) +"%)",
        centerX + Math.cos(midAngle) * textDistance,
        centerY + Math.sin(midAngle) * textDistance);

    ctx.lineWidth = this.params.borderWidth;
    ctx.strokeStyle = this.params.borderStyle;
    ctx.stroke();
  }
});
