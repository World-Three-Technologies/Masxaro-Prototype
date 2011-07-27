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
    _.bindAll(this,"sync","search","searchTag");
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

  search:function(query,success){
    var model = this;
    $.post(this.url,{
      opcode : "key_search",
      acc: account,
      keys : query
    }).success(function(data){
      model.reset(data);
      success();
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
                           "<a href='#tag/<%= tag %>'><%= tag %></a></li>"),

  initialize:function(){
    _.bindAll(this,"setTags","setActive");   
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
window.AppView = Backbone.View.extend({
  el:$("#receipts"),

  pageSize:10,

  start:1,

  end:1,

  initialize:function(){
    _.bindAll(this,"render","renderMore","renderReceipt","cleanResults",
                  "setEnd","search","after","fetch");
    this.model.bind("sync",this.before);
    this.model.bind("reset",this.render);
  },

  events:{
    "click .more": "renderMore",
    "click #search-button": "searchByForm",
    "keyup #search-query": "submitSearch"
  },

  search:function(query){
    this.before();
    this.model.search(query,this.after);
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

  searchByForm:function(){
    this.search($('#search-query').val());
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
  },

  fetch:function(options){
    this.before();
    this.model.fetch({success:this.after,error:options.error});      
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
              'editTags','getTags');
    this.model.bind('change',this.showReceipt);
  },

  events:{
    "click .receipt-row" : "showReceipt",
    "click .close" :"render",
    "click .add-button" : "newTag",
    "click .edit-button" : "editTags",
    "click .delete-button" : "deleteTag"
  },

  render:function(){
    console.log("rendering...");
    var view = $(this.el);
    view.html(this.template(this.model.toJSON()));

    var text = this.getItemText(this.model.get("items"));
    view.find(".items").html(text);
    view.find(".date").html(new Date(this.model.get("receipt_time")).format());
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
    var tag = $("<input type='text' size='10' class='edit-tag'/><span class='delete-btn'>[X]</span>");
    this.$('.edit-area').append(tag);     
  },

  deleteTag:function(){
    var tag = $(event.target).prev().val();
    var tags = this.model.get("tags");
    this.model.set({tags: _.without(tags,tag)});
  },

  showReceipt:function(){

    if(window.lastOpen && window.lastOpen != this){
      window.lastOpen.render();
    }
    window.lastOpen = this;

    $(this.el).html(this.fullTemplate(this.model.toJSON()));
    $(this.el).find(".date").html(new Date(this.model.get("receipt_time")).format());

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
  }
});
var UserView = Backbone.View.extend({

  initialize:function(){
    _.bindAll(this,"render");
    this.el = $("#user");
    this.render();
  },

  render:function(){
    $("#username").text(this.model.get("account")); 
    return this;
  }
});
var AppRouter = Backbone.Router.extend({

  initialize: function(){
    _.bindAll(this,"index","search","searchTag");
    var user = this.user = new User({
      account:readCookie("user_acc"),
    });

    var receipts = this.receipts = new Receipts();
    window.account = receipts.account = user.get("account");
    window.appView = new AppView({model:receipts });
    window.userView = new UserView({model:user});
    window.actionView = new ActionView();
  },

  routes: {
    "" : "index",
    "index" : "index",      
    "search/:query" : "search",
    "tag/:tag" : "searchTag"
  },

  index: function(){
    var options = {
      error: function(){
        $("#ajax-loader").html("<h3>error in model request</h3>");
      }
    }
    appView.fetch(options);
    actionView.setTags("recent");
  },

  search: function(query){
    appView.search(query);      
  },

  searchTag: function(tag){
    actionView.setTags(tag);
    appView.searchTag(tag);           
  }
});
$(function(){
  new AppRouter();
  Backbone.history.start({pushState:false});
});
