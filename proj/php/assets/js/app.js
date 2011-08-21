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
var Receipt = Backbone.Model.extend({

  url: 'receiptOperation.php',
  tagUrl: 'tagOperation.php',

  initialize:function(){
    _.bindAll(this,'sync','updateTags','removeTags','saveTags');
  },

  sync:function(method,model,options){
    model.set({"user_account":account});
    var data;
    if(method == "read"){
      data = {
        opcode : "user_get_receipt_detail",
        receipt_id: model.get("receipt_id"),
      }
    }else if(method == "delete"){
      data = {
        opcode : "f_delete_receipt",
        receipt_id: model.get("receipt_id")
      }
    }
    $.post(this.url,data,options.success).error(options.error);
  },

  updateTags:function(oldTags){
    var tags = this.get("tags"),
        deletedTags = _.difference(oldTags,tags),
        newTags = _.difference(tags,oldTags);
    this.removeTags(deletedTags);          
    this.saveTags(newTags);          
  },

  saveTags:function(tags){
    if(!tags || tags.length == 0){
      return false;
    }
    $.post(this.tagUrl,{
      opcode:"add_receipt_tags",
      user_account:account,
      tags:tags,
      receipt_id:this.id
    }).success(function(data){
      console.log(data);
    });
  },

  removeTags:function(tags){
    if(!tags || tags.length == 0){
      return false;
    }
    $.post(this.tagUrl,{
      opcode:"delete_receipt_tags",
      user_account:account,
      tags:tags,
      receipt_id:this.id
    }).success(function(data){
      console.log(data);
    });
             
  }
});
var Receipts = Backbone.Collection.extend({
  model: Receipt,

  url: 'receiptOperation.php',

  initialize:function(){
    _.bindAll(this,"sync","search","searchTag","searchByKeys","searchByTags");
  },

  sync:function(method,model,options){
    var data;
    if(method == "read"){
      data = {
        opcode : "user_get_all_receipt",
        acc: this.account
      }
    }
    $.post(this.url,data,options.success).error(options.error);
  },

  searchByKeys:function(keys,success){
    this.search({ keys:keys });
  },

  searchByTags:function(tags,success){
    this.search({ tags:tags });
  },

  search:function(data,success){
    var model = this;
    data["opcode"] = "search";
    data["acc"] = account;
    $.post(this.url,data).success(function(data){
      model.reset(data);
      if(success != null && success != "undefined"){
        success();
      }
    });
  },

  searchTag:function(tags,success){
    var model = this;
    $.post(this.url,{
      opcode : "tag_search",
      acc: account,
      tags : tags,
    }).success(function(data){
      model.reset(data);
      success();
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
    if(target == "undefined"){
      target = this.$(event.target).parent();
    }else{
      target = this.$(".tag-"+target).addClass("active");
    }
    target.addClass("active");
  }
});
window.DashboardView = Backbone.View.extend({

  el:$("#dashboard-view"),
  
  initialize:function(){

  }

});
var ReceiptView = Backbone.View.extend({

  tagName:"tr",
  className:"row",
  template:_.template($('#receipt-row-template').html() || "<div/>"),
  fullTemplate:_.template($('#receipt-full-template').html() || "<div/>"),
  itemTemplate:_.template($('#receipt-item-template').html() || "<div/>"),
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
    var tag = $("<input type='text' size='10' class='edit-tag'/><span class='delete-button'/>");
    this.$('.edit-area').append(tag);     
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
    if(query == "" || query == "undefined"){
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
    $('.receipts-stat').hide();
    $('#ajax-loader').show();
  },

  after:function(){
    $('#ajax-loader').hide();
    $('.receipts-stat').show();
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

    if(this.end === this.model.length){
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

  searchTag:function(tags){
    this.before();
    this.model.searchTag(tags.split("-"),this.after);
    console.log(tags);
  },

  fetch:function(options){
    this.before();
    this.model.fetch({success:this.after,error:this.error});      
  },

  error:function(){
    $("#ajax-loader").html("<h3>error in model request</h3>");
  }
});
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
var AppRouter = Backbone.Router.extend({

  initialize: function(){
    _.bindAll(this,"dashboard","receipts","search","searchTag","getReceiptsView");
    var user = this.user = new User({
      account:readCookie("user_acc"),
    });

    var receipts = this.receipts = new Receipts();
    window.account = receipts.account = user.get("account");
    window.userView = new UserView({model:user});
  },

  getReceiptsView:function(){
    if(!this.receiptsView){
      return new ReceiptsView({model:this.receipts});
    }else{
      return this.receiptsView;
    }                 
  },

  routes: {
    "" : "dashboard",
    "dashboard" : "dashboard",
    "receipts" : "receipts",      
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
