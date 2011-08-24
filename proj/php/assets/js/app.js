/*
  Copyright 2011 World Three Technologies, Inc. 
  All Rights Reserved.

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
  */
//analysis.js:
//get analysis data from server
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
var Receipt = Backbone.Model.extend({

  tagUrl: 'tagOperation.php',

  initialize:function(){
    _.bindAll(this,'updateTags','removeTags','saveTags','changeTags');
  },

  updateTags:function(oldTags){
    var tags = this.get("tags"),
        deletedTags = _.difference(oldTags,tags),
        newTags = _.difference(tags,oldTags);
    this.removeTags(deletedTags);          
    this.saveTags(newTags);          
  },

  saveTags:function(tags){
    tags || (tags = []);
    this.changeTags("add_receipt_tags",tags);
  },

  removeTags:function(tags){
    tags || (tags = []);
    this.changeTags("delete_receipt_tags",tags);
  },
  
  changeTags:function(opcode,tags){
    if(!tags || tags.length == 0) return;
    $.post(this.tagUrl,{
      opcode:opcode,
      user_account:account,
      tags:tags,
      receipt_id:this.id
    });
  }
});
var Receipts = Backbone.Collection.extend({
  model: Receipt,

  url: 'receiptOperation.php',

  limit:100,

  defaultParams:function(){
    return {
      opcode : "user_get_all_receipt",
      acc: window.account,
      limitStart:0,
      limitOffset:this.limit
    };
  },

  initialize:function(){
    _.bindAll(this,"sync","search","searchByKeys","searchByTags","defaultParams");
  },

  sync:function(method,model,options){
    $.post(this.url,this.defaultParams(),options.success).error(options.error);
  },

  searchByKeys:function(keys,success){
    this.search({ keys:keys });
  },

  searchByTags:function(tags,success){
    this.search({ tags:tags });
  },

  search:function(query,success){
    var model = this;
    var params = this.defaultParams();
    params["opcode"] = "search";
    $.extend(params,query);
    $.post(this.url,params).success(function(data){
      model.reset(data);
      if(success !== null && typeof success !== "undefined"){
        success();
      }
    });
  }
});
var User = Backbone.Model;
window.ActionView = Backbone.View.extend({
  
  el:$("#action-bar"),

  tagTemplate : _.template("<li class='tag-<%= tag %>'>"+
                           "<a href='#receipts/tag/<%= tag %>'><%= tag %></a></li>"),

  initialize:function(){
    _.bindAll(this,"setTags","setActive");   

    this.$(".action").html('<li class="tag-recent"><a href="#receipts">Recent</a></li>');
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
window.AnalysisView = Backbone.View.extend({
  el:$("#analysis-view"),

  colors:["#FF0","#FFF","#356","#A23","#F2A","#0E0","#AC5","0F0"],
  colorIndex:0,

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

  fetchModelByType:function(){
    this.fetchModel($(event.target).attr("data-type"));
  },

  fetchModel:function(type){
    
    this.model.clear({slient:true});
    this.model.fetch({
      data:{"opcode":type},
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
    var table = this.$("#data-table"),
        template = _.template("<tr><td><%= category %></td><td><%=value %></td></tr>");

    table.find("td").remove();
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
window.DashboardView = Backbone.View.extend({
  el:$("#dashboard-view"),
});
window.DealsView = Backbone.View.extend({});
window.MediaView = Backbone.View.extend({});
var ReceiptView = Backbone.View.extend({

  tagName:"tr",
  className:"row",
  template:_.template($('#receipt-row-template').html() || "<div/>"),
  fullTemplate:_.template($('#receipt-full-template').html() || "<div/>"),
  itemTemplate:_.template($('#receipt-item-template').html() || "<div/>"),
  editTagArea :$(
    "<input type='text' size='10' class='edit-tag'/><span class='delete-button'/>"),
  isEditing:false,
  tagState:[],

  initialize:function(){
    _.bindAll(this,'render','showReceipt','getItemText',
              'editTags','getTags','setDate','animateReceipt');
    this.model.bind('change',this.showReceipt);
  },

  events:{
    "click .receipt-row" : "animateReceipt",
    "click .close" :"render",
    "click .add-button" : "newTag",
    "click .edit-button" : "editTags",
    "click .delete-button" : "deleteTag"
  },

  render:function(){
    var view = $(this.el);
    view.css({height:70});
    view.html(this.template(this.model.toJSON()));

    this.setDate(this.model.get("receipt_time"));

    var text = this.getItemText(this.model.get("items"));
    view.find(".items").html(text);
    return this;
  },

  editTags:function(){
    //sequence is important here...
    var content = this.$(".content");

    if(!this.isEditing){

      content.addClass("editing");
      this.isEditing = true;

      this.$('.edit-button').text("[save]");
      this.tagState = this.model.get("tags");

    }else{
      //set isEditing before render the receipt, so the input box will be disappear
      content.removeClass("editing");
      this.isEditing = false;

      this.model.set({tags: this.getTags() });
      this.model.updateTags(this.tagState);

      this.$('.edit-button').text("[edit]");
    }
  },

  getTags:function(){
    var tags = [];
    _.each(this.$(".edit-tag"),function(tag){
      tags.push($(tag).val());
    });     
    return tags;
  },

  newTag:function(){
    this.$('.edit-area').append(this.editTagArea);     
  },

  deleteTag:function(){
    var tag = $(event.target).prev().val();
    var tags = this.model.get("tags");
    this.model.set({tags: _.without(tags,tag)});
  },

  animateReceipt:function(){

    var itemLength = this.model.get("items").length * 26 + 106;
          
    $(this.el).css({height:itemLength,opacity:0});
    if(window.lastOpen && window.lastOpen != this){
      window.lastOpen.render();
    }
    window.lastOpen = this;
    setTimeout(this.showReceipt,300);
  },

  showReceipt:function(){

    $(this.el).html(this.fullTemplate(this.model.toJSON())).css({opacity:1});
    this.setDate(this.model.get("receipt_time"));

    var items = $(this.el).find(".items"),
        self = this;

    _.each(self.model.get("items"),function(model){
      items.append(self.itemTemplate(model));
    });

    if(this.isEditing){
      this.$(".content").addClass("editing");
      this.$('.edit-button').text("[save]");
    }
  },

  getItemText:function(items){
    return _.reduce(items,function(memo,item){
      return memo + item.item_name + ", ";
    },"").slice(0,-2);
  },

  setDate:function(date){
    var receipt_time = date.replace(/-/g,"/"); 
    $(this.el).find(".date").html(new Date(receipt_time).format());
  }
});
window.ReceiptsView = Backbone.View.extend({
  el:$("#receipts"),

  pageSize:10,

  start:1,

  end:1,

  initialize:function(){
    _.bindAll(this,"render","renderMore","renderReceipt","cleanResults",
                  "setEnd","search","after","fetch","error");
    this.model.bind("sync",this.before);
    this.model.bind("reset",this.render);
    this.actionView = new ActionView();
  },

  events:{
    "click .more": "renderMore",
    "click #search-button": "searchByForm",
    "keyup #search-query": "submitSearch"
  },

  search:function(query,type){
    if(typeof query == "undefined" || query == ""){
      return;
    }
    var keys = query.split(" "); 
    this.before();
    if(type=="keys"){
      this.model.searchByKeys(keys,this.after);
    }else{
      this.model.searchByTags(keys,this.after);
    }
  },

  searchByForm:function(){
    var type = $("#search-type :checked").val()
    this.search($('#search-query').val(),type);
  },

  submitSearch:function(event){
    if(event.which == 13){
      this.searchByForm();
    }
  },

  before:function(){
    this.cleanResults();
    this.$('.receipts-stat').hide();
    this.$('#ajax-loader').show();
  },

  after:function(){
    this.$('#ajax-loader').hide();
    this.$('.receipts-stat').show();
  },

  updateStatus:function(){
    this.$(".stat").text(this.start + " to "+ this.end +" in "+this.model.length);
  },

  render:function(){
    this.cleanResults();
    this.setEnd();

    this.$('#ajax-loader').hide();

    _.each(this.model.models.slice(0,this.end),this.renderReceipt);
    this.updateStatus();

    if(this.end >= this.model.length ){
      this.$(".more").hide();
    }else{
      this.$(".more").show();
    }
    return this;
  },

  cleanResults:function(){
    this.$('.row').remove();
  },

  renderMore:function(){
    var pageLength = (this.end + this.pageSize <= this.model.length) 
                     ? this.end + this.pageSize : this.model.length;
    _.each(this.model.models.slice(this.end,pageLength),this.renderReceipt);

    this.end = pageLength;

    this.updateStatus();

    if(this.end == this.model.length){
      this.$(".more").hide();
    }else{
      this.$(".more").show();
    }
  },

  renderReceipt:function(receipt){
    var view = new ReceiptView({model:receipt});
    this.el.children("table").append(view.render().el);
  },

  setEnd:function(){
    this.end = (this.model.length > 10) ? 10 : this.model.length;       
  },

  fetch:function(options){
    this.before();
    this.model.fetch({success:this.after,error:this.error});      
  },

  error:function(){
    $("#ajax-loader").html("<h3>error in model request</h3>");
  }
});
window.ReportsView = Backbone.View.extend({});
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
var AccountRouter = Backbone.Router.extend({

  initialize:function(){
    _.bindAll(this,"showPage","clearActive","showPage");
    var user = this.user = new User({
      account:readCookie("user_acc"),
    });
    var userView = new UserView({model:user});
  },

  routes: {
    "!:page" : "showPage"
  },

  showPage:function(page){
    this.clearActive();
    $(".boards > div").hide();
    $("."+page).show();
    $("#account-nav ."+page).addClass("active");
  },

  clearActive:function(){
    $(".active").removeClass("active");
  }
});
var AppRouter = Backbone.Router.extend({

  initialize: function(){
    _.bindAll(this,"dashboard","receipts","search","searchTag","getReceiptsView","getAnalysisView");
    var user = this.user = new User({
      account:readCookie("user_acc"),
    });
    window.account = user.get("account");

    window.userView = new UserView({model:user});

  },

  getReceiptsView:function(){
    return this.receiptsView || new ReceiptsView({model:new Receipts()});
  },

  getAnalysisView:function(){
    return this.analysisView || new AnalysisView();
  },

  routes: {
    "" : "dashboard",
    "dashboard" : "dashboard",
    "receipts" : "receipts",      
    "analysis" : "analysis",      
    "reports" : "reports",      
    "deals" : "deals", 
    "media" : "media",      
    "receipts/search/:query" : "search",
    "receipts/tag/:tag" : "searchTag"
  },

  setView:function(name){
    this.setMainTab(name.split("-")[0]);
    $("#boards").removeClass().addClass(name);
  },

  setMainTab:function(tab){
    $("#main-tab > .active").removeClass("active");           
    $("#main-tab > ."+tab).addClass("active");
  },

  dashboard:function(){
    this.setView("dashboard-view");
    this.dashboardView = new DashboardView();
  },

  analysis:function(){
    this.setView("analysis-view");
    this.analysisView = this.getAnalysisView();
  },

  reports:function(){
    this.setView("reports-view");
    this.reportsView = new ReportsView();
  },

  media:function(){
    this.setView("media-view");
    this.MediaView = new MediaView();
  },

  deals:function(){
    this.setView("deals-view");
    this.dealsView = new DealsView();
  },

  receipts: function(){
    this.setView("receipts-view");
    this.getReceiptsView().fetch({tag:"recent"});
  },

  search: function(query){
    this.setView("receipts-view");
    this.getReceiptsView().search(query);
  },

  searchTag: function(tag){
    this.setView("receipts-view");
    this.receiptsView = this.getReceiptsView();
    this.receiptsView.search(tag,"tags");           
    this.receiptsView.actionView.setActive(tag);
  }
});
$(function(){
  new AppRouter();
  Backbone.history.start({pushState:false});
});
