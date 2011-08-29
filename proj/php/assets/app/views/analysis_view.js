//draw pie-chart with analysis.js data
//and show data in table
window.AnalysisView = Backbone.View.extend({
  el:$("#analysis-view"),

  //colors for pie-chart, will cycle in colors array
  colors:["#FF0","#FFF","#356","#A23","#F2A","#0E0","#AC5","0F0"],
  colorIndex:0,
  tableTemplate:_.template("<tr><td><%= category %></td><td><%=value %></td></tr>"),

  initialize:function(){
    _.bindAll(this,"drawChart","drawSlice","setTable",
                   "initCanvas","fetchModel","fetchModelByType","clear");

    this.model = new Analysis();

    this.model.bind("change",this.drawChart);
    this.model.bind("change",this.setTable);

    this.initCanvas();
    this.fetchModel("tag");
  },

  events:{
    "click .button":"fetchModelByType",
  },

  //view draw pie chart depend by receipt tag or receipt position 
  //data type decided by the link attribute "data-type"
  fetchModelByType:function(){
    this.fetchModel($(event.target).attr("data-type"));
  },

  fetchModel:function(type){
    
    //clear the model but not trigger change event
    this.model.clear({slient:true});
    this.model.fetch({
      data:{"opcode":type},
      //set processData to encode data to params string,
      //because backbone set processData off.
      processData:true,
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
      labelFont : "bold 13px 'Trebuchet MS', Verdana, sans-serif",
      fontColor : "black"
    };
  },

  setTable:function(model){
    var table = this.$("#data-table tbody"),
        template = this.tableTemplate;

    table.empty();
    _.each(model.attributes,function(v,k){
      table.append($(template(v)));
    });
  },

  clear:function(){
    this.ctx.clearRect(0,0,this.width,this.height);
  },

  drawChart:function(model){

    var currentPos = this.currentPos = 0,
        totalValue = this.totalValue = model.totalValue(),
        view = this;

    this.clear();

    _.each(model.attributes,function(v,k){
      var chartData = {
        startAngle: 2 * Math.PI * currentPos,
        endAngle: 2 * Math.PI * (currentPos + ( v['value'] / totalValue) ),
        value : v['value']
      }
      currentPos += v['value'] / totalValue;

      view.drawSlice(v['category'],chartData);
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
    ctx.fillStyle = this.colors[(this.colorIndex++ % this.colors.length)];
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
