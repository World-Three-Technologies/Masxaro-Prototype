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
//update receipt tags data from server
//fetch receipts data in receipts.js
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
//fetch receipts data and search receipts by keywords and tags
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
window.DashboardView = Backbone.View.extend({
  el:$("#dashboard-view"),
});
window.DealsView = Backbone.View.extend({});
window.MediaView = Backbone.View.extend({});
//render receipt in row and full mode
//handle tag updates
var ReceiptView = Backbone.View.extend({

  tagName:"tr",
  className:"row",
  template:_.template($('#receipt-row-template').html() || "<div/>"),
  fullTemplate:_.template($('#receipt-full-template').html() || "<div/>"),
  itemTemplate:_.template($('#receipt-item-template').html() || "<div/>"),
  editTagArea :"<span><input type='text' size='10' class='edit-tag'/><span class='delete-button'/></span>",
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
    //set height for animation effect
    view.css({height:70})
        .html(this.template(this.model.toJSON()));

    this.setDate(this.model.get("receipt_time"));

    view.find(".items").html(
      this.getItemText(this.model.get("items"))
    );

    return this;
  },

  //edit or save tags
  editTags:function(){
    //sequence is important here...
    var content = this.$(".content");

    if(this.isEditing){
      //save tags
      content.removeClass("editing");
      this.isEditing = false;

      this.model.set({tags: this.getTags() });
      this.model.updateTags(this.tagState);

      this.$('.edit-button').text("[edit]");
    }else{
      //edit tags
      content.addClass("editing");
      this.isEditing = true;

      this.$('.edit-button').text("[save]");
      this.tagState = this.model.get("tags");
    }
  },

  //collect tags data from input
  getTags:function(){
    return _.map(this.$(".edit-tag"),function(tag){
      return $(tag).val();
    });     
  },

  newTag:function(){
    this.$('.edit-area').append($(this.editTagArea));     
  },

  deleteTag:function(){
    var tag = $(event.target).prev().val();
    $(event.target).parent().remove();
    var tags = this.model.get("tags");
    this.model.set({tags: _.without(tags,tag)});
  },

  animateReceipt:function(){

    //hard coded animation height by item height
    var itemLength = this.model.get("items").length * 26 + 106;
          
    $(this.el).css({height:itemLength,opacity:0});

    //close last opened view
    if(ReceiptView.lastOpen && ReceiptView.lastOpen != this){
      ReceiptView.lastOpen.render();
    }
    ReceiptView.lastOpen = this;
    setTimeout(this.showReceipt,300);
  },

  //render full receipt
  showReceipt:function(){

    $(this.el).html(this.fullTemplate(this.model.toJSON())).css({opacity:1});
    this.setDate(this.model.get("receipt_time"));

    var items = $(this.el).find(".items"),
        view = this;

    _.each(this.model.get("items"),function(model){
      items.append(view.itemTemplate(model));
    });

    if(this.isEditing){
      this.$(".content").addClass("editing");
      this.$('.edit-button').text("[save]");
    }
  },

  getItemText:function(items){
    return _.map(items,function(item){
      return item.item_name;
    }).join(", ");
  },

  setDate:function(date){
    var receipt_time = date.replace(/-/g,"/"); 
    $(this.el).find(".date").html(new Date(receipt_time).format());
  }
});
window.ReceiptsView = Backbone.View.extend({
  el:$("#receipts"),

  pageSize:10,

  end:0,

  initialize:function(){
    _.bindAll(this,"render","renderMore","renderReceipt","cleanResults",
                  "nextPage","search","after","fetch","error");
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
    }else if(type == "tags"){
      this.model.searchByTags(keys,this.after);
    }
  },

  //handle search on search bar
  searchByForm:function(){
    var type = $("#search-type :checked").val()
    this.search($('#search-query').val(),type);
  },
  submitSearch:function(event){
    if(event.which == 13){
      this.searchByForm();
    }
  },

  //pre handle view, show progress bar
  before:function(){
    this.cleanResults();
    this.$('.receipts-stat').hide();
    this.$('#ajax-loader').show();
  },

  //hide progress bar and show status
  after:function(){
    this.$('#ajax-loader').hide();
    this.$('.receipts-stat').show();
  },

  updateStatus:function(){
    this.$(".stat").text("1 to "+ this.end +" in "+this.model.length);

    if(this.end == this.model.length){
      this.$(".more").hide();
    }else{
      this.$(".more").show();
    }
  },

  render:function(){
    this.cleanResults();
    this.$('#ajax-loader').hide();

    _.each(this.model.models.slice(0,this.nextPage()),this.renderReceipt);
    this.updateStatus();
    return this;
  },

  cleanResults:function(){
    this.$('.row').remove();
  },

  renderMore:function(){
    console.log(123);
    _.each(this.model.models.slice(this.end,this.nextPage()),this.renderReceipt);

    this.updateStatus();
  },

  renderReceipt:function(receipt){
    var view = new ReceiptView({model:receipt});
    this.el.children("table").append(view.render().el);
  },

  //return the next page's length and set the range
  nextPage:function(){
    return this.end = (this.end + this.pageSize <= this.model.length) ? 
            this.end + this.pageSize : this.model.length;
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
//user view, show user name in header bar
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
//main App 
//handle routes and trigger correspond view
var AppRouter = Backbone.Router.extend({

  initialize: function(){
    _.bindAll(this,"dashboard","receipts","search","searchTag","getReceiptsView","getAnalysisView");
    //set the account and user view
    var user = this.user = new User({
      account:readCookie("user_acc"),
    });
    window.account = user.get("account");

    window.userView = new UserView({model:user});

  },

  //singleton for view
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

  //set tab active 
  //according the class name of tag,
  // ex setView("receipts-view") => open "receipts" tab
  setView:function(name){
    this.setMainTab(name.split("-")[0]);
    $("#boards").removeClass().addClass(name);
  },

  setMainTab:function(tab){
    $("#main-tab > .active").removeClass("active");           
    $("#main-tab > ."+tab).addClass("active");
  },

  //action for each route
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
  //init Backbone application 
  new AppRouter();
  Backbone.history.start({pushState:false});
});
